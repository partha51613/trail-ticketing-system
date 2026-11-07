<?php

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('tickets can be created with attachments', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $project = Project::create([
        'name' => 'Attachment Proj',
        'code' => 'ATT',
        'status' => 'active',
    ]);

    $file = UploadedFile::fake()->image('screenshot.png', 400, 300);
    $doc = UploadedFile::fake()->create('error_log.txt', 50, 'text/plain');

    $response = $this->actingAs($user)->post(route('tickets.store'), [
        'title' => 'Issue with attachments',
        'description' => 'See attached screenshot and logs',
        'project_id' => $project->id,
        'priority' => 'high',
        'attachments' => [$file, $doc],
    ]);

    $ticket = Ticket::where('title', 'Issue with attachments')->first();
    expect($ticket)->not->toBeNull();

    $response->assertRedirect(route('tickets.show', $ticket));

    expect($ticket->attachments)->toHaveCount(2);
    expect($ticket->attachments->pluck('file_name')->all())->toContain('screenshot.png', 'error_log.txt');
});

test('tickets show page displays attachments associated with ticket description', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $project = Project::create([
        'name' => 'Attachment View Proj',
        'code' => 'ATTV',
        'status' => 'active',
    ]);

    $file = UploadedFile::fake()->image('diagram.png', 300, 300);

    $this->actingAs($user)->post(route('tickets.store'), [
        'title' => 'Diagram Issue',
        'description' => 'System diagram attached',
        'project_id' => $project->id,
        'priority' => 'medium',
        'attachments' => [$file],
    ]);

    $ticket = Ticket::where('title', 'Diagram Issue')->first();

    $this->actingAs($user)
        ->get(route('tickets.show', $ticket))
        ->assertOk()
        ->assertSee('diagram.png')
        ->assertSee('Attachments (1)');
});

test('ticket attachments can be added and removed during ticket edit', function () {
    Storage::fake('local');

    $admin = User::factory()->create(['role' => 'admin']);
    $project = Project::create([
        'name' => 'Edit Attachment Proj',
        'code' => 'EDATT',
        'status' => 'active',
    ]);

    $file1 = UploadedFile::fake()->image('initial_photo.jpg', 300, 300);

    $this->actingAs($admin)->post(route('tickets.store'), [
        'title' => 'Initial Ticket Title',
        'description' => 'Initial Description',
        'project_id' => $project->id,
        'priority' => 'medium',
        'attachments' => [$file1],
    ]);

    $ticket = Ticket::where('title', 'Initial Ticket Title')->first();
    expect($ticket->attachments)->toHaveCount(1);
    $initialAttachment = $ticket->attachments->first();

    // Now edit the ticket: remove initial_photo.jpg and upload new_report.pdf
    $file2 = UploadedFile::fake()->create('new_report.pdf', 100, 'application/pdf');

    $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
        'title' => 'Updated Ticket Title',
        'description' => 'Updated Description with new files',
        'project_id' => $project->id,
        'priority' => 'high',
        'remove_attachments' => [$initialAttachment->id],
        'attachments' => [$file2],
    ]);

    $response->assertRedirect(route('tickets.show', $ticket));

    $ticket->refresh();
    expect($ticket->attachments)->toHaveCount(1);
    expect($ticket->attachments->first()->file_name)->toBe('new_report.pdf');
});
