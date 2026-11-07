<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
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
    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Ticket $ticket = null): bool
    {
        if ($ticket && ($ticket->trashed() || $ticket->project?->isClosed())) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        if ($comment->ticket?->trashed() || $comment->ticket?->project?->isClosed()) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $isWithinFiveMinutes = $comment->created_at->gt(now()->subMinutes(5));

        if (! $isWithinFiveMinutes) {
            return false;
        }

        $isAuthor = $user->id === $comment->user_id;
        $isManager = $comment->ticket ? $user->managesProject($comment->ticket->project_id) : false;

        return $isAuthor || $isManager;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($comment->ticket?->trashed() || $comment->ticket?->project?->isClosed()) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($comment->ticket && $user->managesProject($comment->ticket->project_id)) {
            return true;
        }

        $isAuthor = $user->id === $comment->user_id;
        $isWithinFiveMinutes = $comment->created_at->gt(now()->subMinutes(5));

        return $isAuthor && $isWithinFiveMinutes;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }
}
