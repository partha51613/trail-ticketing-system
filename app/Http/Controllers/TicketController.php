<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
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
        $query = Ticket::with(['project', 'creator', 'assignedTo', ])->latest();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
