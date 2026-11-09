<?php

namespace App\Http\Controllers;

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

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Project::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'min:2', 'max:6', 'alpha_num', 'unique:projects,code'],
            'description' => ['nullable', 'string'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['nullable', 'exists:users,id'],
        ]);

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

        if ($tab === 'deleted') {
            $ticketsQuery = $project->tickets()->onlyTrashed()->with(['creator', 'assignedTo', 'deletedBy']);
        } elseif ($tab === 'closed') {
            $ticketsQuery = $project->tickets()->with(['creator', 'assignedTo'])->where('status', 'closed');
        } else {
            $ticketsQuery = $project->tickets()->with(['creator', 'assignedTo'])->where('status', '!=', 'closed');
        }

        // Search within project tickets
        if ($request->filled('search')) {
            $search = trim($request->search);
            $ticketsQuery->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter (only applied within the active tab)
        if ($tab === 'active' && $request->filled('status')) {
            $ticketsQuery->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $ticketsQuery->where('priority', $request->priority);
        }

        // Assignee filter
        if ($request->filled('assigned_to_id')) {
            if ($request->assigned_to_id === 'unassigned') {
                $ticketsQuery->whereNull('assigned_to_id');
            } else {
                $ticketsQuery->where('assigned_to_id', $request->assigned_to_id);
            }
        }

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

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'min:2', 'max:6', 'alpha_num', 'unique:projects,code,'.$project->id],
            'description' => ['nullable', 'string'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['integer', 'exists:users,id'],
        ]);
        $project->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
        ]);
        // Sync selected managers (if none selected, pass empty array to detach all)
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
