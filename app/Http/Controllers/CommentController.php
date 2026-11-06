<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket, AttachmentService $attachmentService): RedirectResponse
    {
        $comment = $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->validated('body'),
        ]);

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
