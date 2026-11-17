<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $requestedTab = $request->query('tab');
        $tab = in_array($requestedTab, ['closed', 'deleted'], true) ? $requestedTab : 'active';

        $baseQuery = Project::query();

        // Calculate tab counts
        $activeCount = (clone $baseQuery)->withoutTrashed()->where('status', '!=', 'closed')->count();
        $closedCount = (clone $baseQuery)->withoutTrashed()->where('status', 'closed')->count();
        $deletedCount = (clone $baseQuery)->onlyTrashed()->count();

        if ($tab === 'deleted') {
            $query = (clone $baseQuery)->onlyTrashed()->with(['managers', 'deletedBy']);
        } elseif ($tab === 'closed') {
            $query = (clone $baseQuery)->withoutTrashed()->where('status', 'closed')->with(['managers', 'closedBy']);
        } else {
            $query = (clone $baseQuery)->withoutTrashed()->where('status', '!=', 'closed')->with('managers');
        }

        $projects = $query
            ->withCount([
                'tickets as total_tickets_count',
                'tickets as assigned_tickets_count' => fn ($query) => $query->whereNotNull('assigned_to_id'),
                'tickets as unassigned_tickets_count' => fn ($query) => $query->whereNull('assigned_to_id'),
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_projects' => Project::withoutTrashed()->where('status', '!=', 'closed')->count(),
            'total_tickets' => Ticket::count(),
            'assigned_tickets' => Ticket::whereNotNull('assigned_to_id')->count(),
            'unassigned_tickets' => Ticket::whereNull('assigned_to_id')->count(),
        ];

        return view('projects.index', compact('projects', 'stats', 'tab', 'activeCount', 'closedCount', 'deletedCount'));
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);
        $managers = User::where('role', 'manager')->orderBy('name')->get();

        return view('projects.create', compact('managers'));
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $project = Project::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
        ]);

        if (! empty($validated['manager_ids'])) {
            $project->managers()->sync($validated['manager_ids']);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function show(Request $request, Project $project): View
    {
        $project->load('managers');

        // Tab Filter: 'active' (default) vs 'closed' vs 'deleted'
        $requestedTab = $request->query('tab');
        $tab = in_array($requestedTab, ['closed', 'deleted'], true) ? $requestedTab : 'active';

        // Scope tickets to this project and tab
        $ticketsQuery = $project->tickets()
            ->forTab($tab)
            ->with(['creator', 'assignedTo', 'deletedBy'])
            ->search($request->search)
            ->filterBy($request->all());

        // Server-side Sorting
        $allowedSorts = ['id', 'title', 'status', 'priority', 'due_date', 'created_at'];
        $sort = in_array($request->query('sort'), $allowedSorts, true) ? $request->query('sort') : 'created_at';
        $direction = strtolower($request->query('direction')) === 'asc' ? 'asc' : 'desc';

        $tickets = $ticketsQuery->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString()
            ->fragment('tickets-table');

        // Project ticket stats
        $projectStats = [
            'total' => $project->tickets()->count(),
            'active' => $project->tickets()->where('status', '!=', 'closed')->count(),
            'assigned' => $project->tickets()->whereNotNull('assigned_to_id')->count(),
            'unassigned' => $project->tickets()->whereNull('assigned_to_id')->count(),
            'blocked' => $project->tickets()->where('status', 'blocked')->count(),
            'closed' => $project->tickets()->where('status', 'closed')->count(),
            'deleted' => $project->tickets()->onlyTrashed()->count(),
        ];

        return view('projects.show', compact('project', 'tickets', 'projectStats', 'sort', 'direction', 'tab'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);
        $project->load('managers');
        $managers = User::where('role', 'manager')->orderBy('name')->get();

        return view('projects.edit', compact('project', 'managers'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        $project->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
        ]);

        $project->managers()->sync($validated['manager_ids'] ?? []);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project): RedirectResponse
    {

        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }

    public function restore(int $id): RedirectResponse
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $project);
        $project->restore();

        return back()->with('success', 'Project restored successfully');
    }

    public function toggleClose(Project $project): RedirectResponse
    {
        $this->authorize('toggleClose', $project);

        if ($project->isClosed()) {
            $project->update([
                'status' => 'active',
                'closed_by_id' => null,
                'closed_at' => null,
            ]);
            $message = 'Project reopened successfully.';
        } else {
            $project->update([
                'status' => 'closed',
                'closed_by_id' => auth()->id(),
                'closed_at' => now(),
            ]);
            $message = 'Project closed successfully. Workspace and its tickets are now read-only.';
        }

        return back()->with('success', $message);
    }
}
