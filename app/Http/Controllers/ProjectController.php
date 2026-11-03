<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with('managers')->withCount('tickets')->latest()->paginate(15);

        return view('projects.index', compact('projects'));
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
            'description' => ['nullable', 'string'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['nullable', 'exists:users,id'],
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (! empty($validated['manager_ids'])) {
            $project->managers()->sync($validated['manager_ids']);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function show(Project $project): View
    {
        $project->load(['managers', 'tickets.creator', 'tickets.assignedTo']);

        return view('projects.show', compact('project'));
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
            'description' => ['nullable', 'string'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['integer', 'exists:users,id'],
        ]);
        $project->update([
            'name' => $validated['name'],
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
}
