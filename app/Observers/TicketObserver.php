<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    // Handle the ticket creating event

    public function creating(Ticket $ticket): void
    {
        if (! $ticket->number && $ticket->project_id) {
            $maxNumber = Ticket::where('project_id', $ticket->project_id)->max('number') ?? 0;
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
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // 1. Auto-add new assignee to watchers
        if ($ticket->wasChanged('assigned_to_id') && $ticket->assigned_to_id) {
            $ticket->watchers()->syncWithoutDetaching([
                $ticket->assigned_to_id,
            ]);
        }

        // 2. Audit-log track field changes

        $trackedFields = [
            'status',
            'assigned_to_id',
            'priority',
            'due_date',
            'project_id',
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
