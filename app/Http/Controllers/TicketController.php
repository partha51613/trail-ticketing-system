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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['project', 'creator', 'assignedTo'])->latest();

        // if user selected a status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // If user selected a priority filter
        if ($request->filled('priority')) {

            $query->where('priority', $request->priority);
        }

        // If user selected a project filter
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // If user selected a 'user' filter
        if ($request->filled('assigned_to_id')) {
            if ($request->assigned_to_id === 'unassigned') {
                $query->whereNull('assigned_to_id');
            } else {
                $query->where('assigned_to_id', $request->assigned_to_id);
            }
        }

        $tickets = $query->paginate(15)->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'projects' => Project::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tickets.create', [
            'projects' => Project::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
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
        $ticket->load([
            'project',
            'creator',
            'assignedTo',
            'watchers',
            'activityLogs.user',
            'comments.user',
        ]);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'projects' => Project::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
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

    public function transitionStatus(TransitionTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('transition', [$ticket, $request->status]);

        DB::transaction(function () use ($ticket, $request) {
            $ticket->update([
                'status' => $request->status,
            ]);

            if ($request->filled('comment')) {
                $ticket->comments()->create([
                    'user_id' => $request->user()->id,
                    'body' => $request->comment,
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
}
