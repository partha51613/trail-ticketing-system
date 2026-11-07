<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class TicketObserver
{
    /**
     * Handle the ticket creating event.
     */
    public function creating(Ticket $ticket): void
    {
        if (! $ticket->number && $ticket->project_id) {
            $maxNumber = Ticket::withTrashed()->where('project_id', $ticket->project_id)->lockForUpdate()->max('number') ?? 0;
            $ticket->number = $maxNumber + 1;
        }
    }

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        $watcherIds = collect([$ticket->created_by_id, $ticket->assigned_to_id])
            ->filter()
            ->unique()
            ->values();

        $ticket->watchers()->syncWithoutDetaching($watcherIds);

        // If ticket was created with an assignee directly, send assignment notification
        // (Only if not self-assigned)
        if ($ticket->assigned_to_id && $ticket->assigned_to_id !== Auth::id()) {
            $assignee = $ticket->assignedTo;
            if ($assignee && $assignee->email) {
                $assignee->notify(new TicketAssignedNotification($ticket, Auth::user()));
            }
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // 1. Assignee changed: auto-add to watchers and notify the new assignee (only if not self-assigned)
        if ($ticket->wasChanged('assigned_to_id') && $ticket->assigned_to_id) {
            $ticket->watchers()->syncWithoutDetaching([
                $ticket->assigned_to_id,
            ]);

            // Only email the newly assigned user, never the user performing the reassignment
            if ($ticket->assigned_to_id !== Auth::id()) {
                $assignee = $ticket->assignedTo;
                if ($assignee && $assignee->email) {
                    $assignee->notify(new TicketAssignedNotification($ticket, Auth::user()));
                }
            }
        }

        // 2. Status changed: notify watchers / respondents / creator (excluding the user changing the status)
        if ($ticket->wasChanged('status')) {
            $recipients = $ticket->getNotificationRecipients(Auth::id() ?? 0);
            if ($recipients->isNotEmpty()) {
                $oldStatus = $ticket->getOriginal('status');
                Notification::send($recipients, new TicketStatusUpdatedNotification($ticket, $oldStatus));
            }
        }

        // 3. Audit-log track field changes
        $trackedFields = [
            'status',
            'assigned_to_id',
            'priority',
            'due_date',
            'project_id',
            'description',
        ];

        foreach ($trackedFields as $field) {
            if ($ticket->wasChanged($field)) {
                $ticket->activityLogs()->create([
                    'user_id' => Auth::id() ?? $ticket->created_by_id,
                    'field_changed' => $field,
                    'old_value' => $ticket->getOriginal($field) !== null ? (string) $ticket->getOriginal($field) : null,
                    'new_value' => $ticket->getAttribute($field) !== null ? (string) $ticket->getAttribute($field) : null,
                ]);
            }
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
