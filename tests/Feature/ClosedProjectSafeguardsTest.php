<?php

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;

test('closed project prevents ticket creation via form request', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::create([
        'name' => 'Closed Proj',
        'code' => 'CLSD',
        'status' => 'closed',
        'closed_at' => now(),
    ]);

    $response = $this->actingAs($admin)->post('/tickets', [
        'title' => 'Test Ticket in Closed Project',
        'description' => 'Should fail',
        'project_id' => $project->id,
        'priority' => 'medium',
    ]);

    $response->assertSessionHasErrors('project_id');
});

test('closed project prevents ticket editing and transfer', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $closedProject = Project::create([
        'name' => 'Closed Alpha',
        'code' => 'CLSA',
        'status' => 'closed',
    ]);
    $activeProject = Project::create([
        'name' => 'Active Beta',
        'code' => 'ACTB',
        'status' => 'active',
    ]);

    $ticket = Ticket::create([
        'title' => 'Old Title',
        'description' => 'Old Desc',
        'project_id' => $closedProject->id,
        'created_by_id' => $admin->id,
        'priority' => 'medium',
        'status' => 'new',
    ]);

    $response = $this->actingAs($admin)->put("/tickets/{$ticket->id}", [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'project_id' => $activeProject->id,
        'priority' => 'high',
    ]);

    $response->assertForbidden();
});

test('closed project prevents new comments', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create([
        'name' => 'Closed Gamma',
        'code' => 'CLSG',
        'status' => 'closed',
    ]);
    $ticket = Ticket::create([
        'title' => 'Sample Ticket',
        'description' => 'Desc',
        'project_id' => $project->id,
        'created_by_id' => $user->id,
        'priority' => 'medium',
        'status' => 'new',
    ]);

    $response = $this->actingAs($user)->post("/tickets/{$ticket->id}/comments", [
        'body' => 'Attempting to comment on closed project ticket',
    ]);

    $response->assertForbidden();
});

test('closed project prevents watcher toggles', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create([
        'name' => 'Closed Delta',
        'code' => 'CLSD2',
        'status' => 'closed',
    ]);
    $ticket = Ticket::create([
        'title' => 'Sample Ticket 2',
        'description' => 'Desc',
        'project_id' => $project->id,
        'created_by_id' => $user->id,
        'priority' => 'medium',
        'status' => 'new',
    ]);

    $response = $this->actingAs($user)->post("/tickets/{$ticket->id}/watch");

    $response->assertForbidden();
});
