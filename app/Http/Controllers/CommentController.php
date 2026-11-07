<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use App\Notifications\TicketCommentNotification;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket, AttachmentService $attachmentService): RedirectResponse
    {
        if ($ticket->trashed()) {
            abort(403, 'Cannot comment on a deleted ticket.');
        }

        if ($ticket->project?->isClosed()) {
            abort(403, 'Cannot comment on a ticket in a closed project.');
        }

        $comment = $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        $recipients = $ticket->getNotificationRecipients($request->user()->id);
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new TicketCommentNotification($ticket, $comment));
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentService->storeAttachment($file, $comment, $request->user()->id);
            }
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment posted successfully');
    }

    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {

        $this->authorize('update', $comment);

        $comment->update([
            'body' => $request->validated('body'),
            'edited_at' => now(),
        ]);

        return back()->with('success', 'Comment updated successfully');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully');
    }
}
