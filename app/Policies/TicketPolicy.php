<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($ticket->status === 'closed') {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $isCreator = $user->id === $ticket->created_by_id;
        $isManager = $user->managesProject($ticket->project_id);

        return $isCreator || $isManager;
    }

    /**
     * Determine whether the user can reassign the model.
     */
    public function reassign(User $user, Ticket $ticket): bool
    {
        if ($ticket->status === 'closed') {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $isCreator = $user->id === $ticket->created_by_id;
        $isAssignee = $user->id === $ticket->assigned_to_id;
        $isManager = $user->managesProject($ticket->project_id);

        return $isCreator || $isAssignee || $isManager;
    }

    public function transferProject(User $user, Ticket $ticket, int $targetProjectId): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->managesProject($ticket->project_id) && $user->managesProject($targetProjectId);
    }

    public function transition(User $user, Ticket $ticket, string $targetStatus): bool
    {
        if ($ticket->status === 'closed') {
            if ($user->role === 'admin') {
                return true;
            }

            $isCreator = $user->id === $ticket->created_by_id;
            $isAssignee = $user->id === $ticket->assigned_to_id;
            $isManager = $user->managesProject($ticket->project_id);

            return $isCreator || $isAssignee || $isManager;
        }

        // Active tickets can be transitioned or closed by any authenticated user
        return true;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->managesProject($ticket->project_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin';
    }
}
