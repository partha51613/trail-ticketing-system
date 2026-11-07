<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WatcherController extends Controller
{
    public function toggle(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->trashed() || $ticket->project?->isClosed()) {
            abort(403, 'Cannot change watcher status on a closed or deleted ticket.');
        }

        $userId = $request->user()->id;

        $changes = $ticket->watchers()->toggle($userId);

        $isWatching = count($changes['attached']) > 0;
        $message = $isWatching ? 'You are now watching this ticket' : 'You have stopped watching this ticket';

        return back()->with('success', $message);
    }
}
