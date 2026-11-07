<?php

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;

test('guests are redirected to login when visiting workload page', function () {
    $this->get(route('workload.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view workload page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('workload.index'))
        ->assertOk()
        ->assertViewIs('workload.index')
        ->assertSee('Workload Statistics')
        ->assertSee('User Workload Leaderboard');
});

test('workload displays users sorted from heaviest to lightest active assigned tickets', function () {
    $userLight = User::factory()->create(['name' => 'Alice Light']);
    $userHeavy = User::factory()->create(['name' => 'Bob Heavy']);
    $userMedium = User::factory()->create(['name' => 'Charlie Medium']);

    $project = Project::create([
        'name' => 'Active Project Alpha',
        'code' => 'ALPHA',
        'status' => 'active',
    ]);

    // Heavy: 3 active tickets (new / in_progress)
    for ($i = 0; $i < 3; $i++) {
        Ticket::create([
            'title' => "Heavy Ticket {$i}",
            'description' => "Description {$i}",
            'project_id' => $project->id,
            'assigned_to_id' => $userHeavy->id,
            'created_by_id' => $userLight->id,
            'status' => 'new',
            'priority' => 'urgent',
        ]);
    }

    // Medium: 2 active tickets
    for ($i = 0; $i < 2; $i++) {
        Ticket::create([
            'title' => "Medium Ticket {$i}",
            'description' => "Description {$i}",
            'project_id' => $project->id,
            'assigned_to_id' => $userMedium->id,
            'created_by_id' => $userLight->id,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);
    }

    // Light: 1 active ticket
    Ticket::create([
        'title' => 'Light Ticket 1',
        'description' => 'Description light',
        'project_id' => $project->id,
        'assigned_to_id' => $userLight->id,
        'created_by_id' => $userHeavy->id,
        'status' => 'new',
        'priority' => 'low',
    ]);

    // Also create closed tickets for Light to ensure closed tickets do not inflate active workload
    for ($i = 0; $i < 5; $i++) {
        Ticket::create([
            'title' => "Closed Ticket {$i}",
            'description' => "Description closed {$i}",
            'project_id' => $project->id,
            'assigned_to_id' => $userLight->id,
            'created_by_id' => $userHeavy->id,
            'status' => 'closed',
            'priority' => 'medium',
        ]);
    }

    $response = $this->actingAs($userLight)
        ->get(route('workload.index'))
        ->assertOk();

    $users = $response->viewData('users');

    expect($users->pluck('name')->all())
        ->toContain('Bob Heavy', 'Charlie Medium', 'Alice Light');

    // Bob Heavy should rank #1 with 3 active tickets
    $bob = $users->firstWhere('id', $userHeavy->id);
    $charlie = $users->firstWhere('id', $userMedium->id);
    $alice = $users->firstWhere('id', $userLight->id);

    expect($bob->active_tickets_count)->toBe(3);
    expect($charlie->active_tickets_count)->toBe(2);
    expect($alice->active_tickets_count)->toBe(1);

    // Verify Bob is at index 0
    expect($users->first()->id)->toBe($userHeavy->id);
});

test('tickets in closed projects are not counted towards active workload', function () {
    $user = User::factory()->create(['name' => 'Developer Dave']);

    $closedProject = Project::create([
        'name' => 'Closed Proj',
        'code' => 'CLSD',
        'status' => 'closed',
    ]);

    $activeProject = Project::create([
        'name' => 'Active Proj',
        'code' => 'ACTV',
        'status' => 'active',
    ]);

    // 2 tickets in closed project
    for ($i = 0; $i < 2; $i++) {
        Ticket::create([
            'title' => "Closed Proj Ticket {$i}",
            'description' => "Description closed {$i}",
            'project_id' => $closedProject->id,
            'assigned_to_id' => $user->id,
            'created_by_id' => $user->id,
            'status' => 'new',
            'priority' => 'medium',
        ]);
    }

    // 1 ticket in active project
    Ticket::create([
        'title' => 'Active Proj Ticket',
        'description' => 'Description active',
        'project_id' => $activeProject->id,
        'assigned_to_id' => $user->id,
        'created_by_id' => $user->id,
        'status' => 'new',
        'priority' => 'medium',
    ]);

    $response = $this->actingAs($user)
        ->get(route('workload.index'))
        ->assertOk();

    $users = $response->viewData('users');
    $dave = $users->firstWhere('id', $user->id);

    expect($dave->active_tickets_count)->toBe(1);
});
