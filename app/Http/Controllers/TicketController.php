<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\TransitionTicketStatusRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
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
        $query = Ticket::whereHas('project', function ($q) {
            $q->withoutTrashed();
        })->with(['project', 'creator', 'assignedTo'])->latest();

        // Tab Filter: 'active' (default) vs 'closed' vs 'deleted'
        $requestedTab = $request->query('tab');
        $tab = in_array($requestedTab, ['closed', 'deleted'], true) ? $requestedTab : 'active';

        // Disallow non-admins from viewing soft-deleted tickets (Section 2.2 Permissions Matrix)
        if ($tab === 'deleted' && auth()->user()?->role !== 'admin') {
            abort(403, 'Only administrators can view deleted tickets.');
        }

        // Base query with active projects
        $baseQuery = Ticket::whereHas('project', function ($q) {
            $q->withoutTrashed();
        });

        // Common context filters (for tab counts & listing)
        $applyContextFilters = function ($q) use ($request) {
            if ($request->filled('search')) {
                $search = trim($request->search);
                $q->where(function ($sub) use ($search) {
                    if (is_numeric($search)) {
                        $sub->orWhere('id', (int) $search);
                    }
                    $sub->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('project_id')) {
                $q->where('project_id', $request->project_id);
            }

            if ($request->filled('priority')) {
                $q->where('priority', $request->priority);
            }

            if ($request->filled('assigned_to_id')) {
                if ($request->assigned_to_id === 'unassigned') {
                    $q->whereNull('assigned_to_id');
                } else {
                    $q->where('assigned_to_id', $request->assigned_to_id);
                }
            }

            if ($request->boolean('watched')) {
                $q->whereHas('watchers', function ($sub) {
                    $sub->where('users.id', Auth::id());
                });
            }

            if ($request->filled('created_by_id')) {
                $q->where('created_by_id', $request->created_by_id);
            }
        };

        // Calculate tab counts
        $activeCountQuery = (clone $baseQuery)->where('status', '!=', 'closed');
        $applyContextFilters($activeCountQuery);
        $activeCount = $activeCountQuery->count();

        $closedCountQuery = (clone $baseQuery)->where('status', 'closed');
        $applyContextFilters($closedCountQuery);
        $closedCount = $closedCountQuery->count();

        $deletedCountQuery = (clone $baseQuery)->onlyTrashed();
        $applyContextFilters($deletedCountQuery);
        $deletedCount = $deletedCountQuery->count();

        // Main listing query
        if ($tab === 'deleted') {
            $query = (clone $baseQuery)->onlyTrashed()->with(['project', 'creator', 'assignedTo', 'deletedBy'])->latest('deleted_at');
            $applyContextFilters($query);
        } elseif ($tab === 'closed') {
            $query = (clone $baseQuery)->with(['project', 'creator', 'assignedTo'])->latest();
            $applyContextFilters($query);
            $query->where('status', 'closed');
        } else {
            $query = (clone $baseQuery)->with(['project', 'creator', 'assignedTo'])->latest();
            $applyContextFilters($query);
            $query->where('status', '!=', 'closed');
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        }

        $tickets = $query->paginate(10)->withQueryString()->fragment('tickets-table');

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
    public function store(StoreTicketRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by_id'] = $request->user()->id;
        $ticket = Ticket::create($validated);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Tickets created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
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

        return view('tickets.show', compact('ticket', 'timeline', 'users', 'isWatching'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'projects' => Project::withoutTrashed()->orderBy('name')->get(),
            'users' => User::withoutTrashed()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $ticket->update($request->validated());

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);
        $ticket->delete();

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

    public function transitionStatus(TransitionTicketStatusRequest $request, Ticket $ticket): RedirectResponse
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

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket status updated successfully');
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
