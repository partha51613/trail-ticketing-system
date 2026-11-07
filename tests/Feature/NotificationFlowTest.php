<?php

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCommentNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use Illuminate\Support\Facades\Notification;

test('assignee receives notification when assigned by another user', function () {
    Notification::fake();

    $creator = User::factory()->create();
    $assignee = User::factory()->create();
    $project = Project::create([
        'name' => 'Notification Proj',
        'code' => 'NOTIF',
        'status' => 'active',
    ]);

    $ticket = Ticket::create([
        'title' => 'Test Ticket Notification',
        'description' => 'Test description',
        'project_id' => $project->id,
        'created_by_id' => $creator->id,
        'priority' => 'medium',
        'status' => 'new',
    ]);

    $this->actingAs($creator)->patch(route('tickets.reassign', $ticket), [
        'assigned_to_id' => $assignee->id,
    ]);

    Notification::assertSentTo($assignee, TicketAssignedNotification::class);
    Notification::assertNotSentTo($creator, TicketAssignedNotification::class);
});

test('self-assignment does not send notification to oneself', function () {
    Notification::fake();

    $user = User::factory()->create();
    $project = Project::create([
        'name' => 'Self Assign Proj',
        'code' => 'SELF',
        'status' => 'active',
    ]);

    $ticket = Ticket::create([
        'title' => 'Self Assigned Ticket',
        'description' => 'Test self assign',
        'project_id' => $project->id,
        'created_by_id' => $user->id,
        'priority' => 'low',
        'status' => 'new',
    ]);

    $this->actingAs($user)->patch(route('tickets.reassign', $ticket), [
        'assigned_to_id' => $user->id,
    ]);

    Notification::assertNotSentTo($user, TicketAssignedNotification::class);
});

test('status transition notifies creator, assignee and watchers excluding actor', function () {
    Notification::fake();

    $creator = User::factory()->create();
    $assignee = User::factory()->create();
    $watcher = User::factory()->create();
    $actor = User::factory()->create();

    $project = Project::create([
        'name' => 'Status Proj',
        'code' => 'STAT',
        'status' => 'active',
    ]);

    $ticket = Ticket::create([
        'title' => 'Status Transition Ticket',
        'description' => 'Testing status notification',
        'project_id' => $project->id,
        'created_by_id' => $creator->id,
        'assigned_to_id' => $assignee->id,
        'priority' => 'high',
        'status' => 'new',
    ]);

    $ticket->watchers()->attach($watcher->id);

    $this->actingAs($actor)->patch(route('tickets.transition', $ticket), [
        'status' => 'in_progress',
    ]);

    Notification::assertSentTo($creator, TicketStatusUpdatedNotification::class);
    Notification::assertSentTo($assignee, TicketStatusUpdatedNotification::class);
    Notification::assertSentTo($watcher, TicketStatusUpdatedNotification::class);
    Notification::assertNotSentTo($actor, TicketStatusUpdatedNotification::class);
});

test('posting comment notifies creator and watchers excluding commenter', function () {
    Notification::fake();

    $creator = User::factory()->create();
    $assignee = User::factory()->create();
    $commenter = User::factory()->create();

    $project = Project::create([
        'name' => 'Comment Proj',
        'code' => 'COMM',
        'status' => 'active',
    ]);

    $ticket = Ticket::create([
        'title' => 'Comment Notification Ticket',
        'description' => 'Testing comments',
        'project_id' => $project->id,
        'created_by_id' => $creator->id,
        'assigned_to_id' => $assignee->id,
        'priority' => 'medium',
        'status' => 'new',
    ]);

    $this->actingAs($commenter)->post(route('comments.store', $ticket), [
        'body' => 'This is a test comment for QA.',
    ]);

    Notification::assertSentTo($creator, TicketCommentNotification::class);
    Notification::assertSentTo($assignee, TicketCommentNotification::class);
    Notification::assertNotSentTo($commenter, TicketCommentNotification::class);
});
