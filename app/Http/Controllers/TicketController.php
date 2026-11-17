<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\TransitionTicketStatusRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Tab Filter: 'active' (default) vs 'closed' vs 'deleted'
        $requestedTab = $request->query('tab');
        $tab = in_array($requestedTab, ['closed', 'deleted'], true) ? $requestedTab : 'active';

        // Base query with active projects, search, and contextual filters
        $baseQuery = Ticket::whereHas('project', fn ($q) => $q->withoutTrashed())
            ->search($request->search)
            ->filterBy($request->all());

        // Calculate tab counts
        $activeCount = (clone $baseQuery)->forTab('active')->count();
        $closedCount = (clone $baseQuery)->forTab('closed')->count();
        $deletedCount = (clone $baseQuery)->forTab('deleted')->count();

        // Main listing query
        $query = (clone $baseQuery)
            ->forTab($tab)
            ->with(['project', 'creator', 'assignedTo', 'deletedBy']);

        if ($tab === 'deleted') {
            $query->latest('deleted_at');
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(10)->withQueryString()->fragment('tickets-table');

        // Store the active index URL in session
        $request->session()->put('ticket_index_url', $request->fullUrl());

        return view('tickets.index', [
            'tickets' => $tickets,
            'tab' => $tab,
            'activeCount' => $activeCount,
            'closedCount' => $closedCount,
            'deletedCount' => $deletedCount,
            'projects' => Project::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Dedicated listing of tickets assigned to the authenticated user.
     */
    public function assignedToMe(Request $request)
    {
        $request->merge(['assigned_to_id' => Auth::id()]);

        return $this->index($request);
    }

    /**
     * Dedicated listing of tickets created by the authenticated user.
     */
    public function createdByMe(Request $request)
    {
        $request->merge(['created_by_id' => Auth::id()]);

        return $this->index($request);
    }

    /**
     * Dedicated listing of tickets watched by the authenticated user.
     */
    public function watchedByMe(Request $request)
    {
        $request->merge(['watched' => 1]);

        return $this->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tickets.create', [
            'projects' => Project::withoutTrashed()->orderBy('name')->get(),
            'users' => User::withoutTrashed()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request, AttachmentService $attachmentService)
    {
        $validated = $request->validated();
        $validated['created_by_id'] = $request->user()->id;
        $ticket = Ticket::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentService->storeAttachment($file, $ticket, $request->user()->id);
            }
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Tickets created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['creator', 'assignedTo', 'project', 'comments.user', 'comments.attachments', 'watchers', 'activityLogs.user', 'attachments']);

        // Merge comments and activity logs into a unified chronological stream
        $commentsStream = $ticket->comments->map(fn ($comment) => [
            'type' => 'comment',
            'id' => $comment->id,
            'timestamp' => $comment->created_at,
            'data' => $comment,
        ]);

        $activityStream = $ticket->activityLogs->map(fn ($log) => [
            'type' => 'activity',
            'id' => $log->id,
            'timestamp' => $log->created_at,
            'data' => $log,
        ]);

        $timeline = $commentsStream->concat($activityStream)
            ->sortByDesc('timestamp')
            ->values();

        $users = User::orderBy('name')->get();
        $isWatching = $ticket->watchers->contains(auth()->id());

        // Resolve return back URL (prioritize active list session or referring page, but never loop back to edit page)
        $previousUrl = url()->previous();
        $backUrl = route('projects.show', $ticket->project_id);

        if ($request->session()->has('ticket_index_url')) {
            $backUrl = $request->session()->get('ticket_index_url');
        } elseif ($previousUrl && $previousUrl !== url()->current() && ! str_contains($previousUrl, '/edit')) {
            $backUrl = $previousUrl;
        }

        return view('tickets.show', compact('ticket', 'timeline', 'users', 'isWatching', 'backUrl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        $ticket->load(['attachments']);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'projects' => Project::withoutTrashed()->orderBy('name')->get(),
            'users' => User::withoutTrashed()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket, AttachmentService $attachmentService): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $ticket->update($request->validated());

        // Process removed attachments
        if ($request->filled('remove_attachments')) {
            $attachmentsToRemove = $ticket->attachments()
                ->whereIn('id', $request->input('remove_attachments'))
                ->get();

            foreach ($attachmentsToRemove as $attachment) {
                $ticket->activityLogs()->create([
                    'user_id' => $request->user()->id,
                    'field_changed' => 'attachment_removed',
                    'old_value' => $attachment->file_name,
                    'new_value' => null,
                ]);
                $attachment->delete();
            }
        }

        // Process newly uploaded attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachment = $attachmentService->storeAttachment($file, $ticket, $request->user()->id);
                $ticket->activityLogs()->create([
                    'user_id' => $request->user()->id,
                    'field_changed' => 'attachment_added',
                    'old_value' => null,
                    'new_value' => $attachment->file_name,
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();

        // If a redirect URL was passed, or session stored origin, redirect back to it
        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Ticket deleted successfully');
        }

        if ($request->session()->has('ticket_index_url')) {
            $returnUrl = $request->session()->get('ticket_index_url');
            $request->session()->forget('ticket_index_url');

            return redirect($returnUrl)->with('success', 'Ticket deleted successfully');
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully');
    }

    /**
     * Restore a soft-deleted ticket (Admin only).
     */
    public function restore(int $id): RedirectResponse
    {
        $ticket = Ticket::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $ticket);

        $ticket->restore();

        return back()->with('success', 'Ticket restored successfully');
    }

    public function transitionStatus(TransitionTicketStatusRequest $request, Ticket $ticket): RedirectResponse|JsonResponse
    {
        $this->authorize('transition', [$ticket, $request->status]);

        DB::transaction(function () use ($ticket, $request) {
            $ticket->update([
                'status' => $request->status,
            ]);

            if ($request->filled('comment')) {
                $commentBody = $request->comment;
                if ($request->status === 'blocked') {
                    $commentBody = 'Blocked: '.ltrim($commentBody);
                }
                $ticket->comments()->create([
                    'user_id' => $request->user()->id,
                    'body' => $commentBody,
                ]);
            }
        });

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully',
                'status' => $ticket->status,
                'formatted_status' => ucfirst(str_replace('_', ' ', $ticket->status)),
            ]);
        }

        return back()->with('success', 'Ticket status updated successfully');
    }

    public function reassign(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('reassign', $ticket);

        $validated = $request->validate([
            'assigned_to_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket reassigned successfully');
    }

    public function changePriority(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
        ]);

        $ticket->update([
            'priority' => $validated['priority'],
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket priority updated successfully');
    }
}
