<?php

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\TransitionTicketStatusRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

/*
|--------------------------------------------------------------------------
| Milestone 3 (Subtask 3.1) Form Requests Validation Tests
|--------------------------------------------------------------------------
*/

test('StoreTicketRequest passes with valid payload', function () {
    $request = new StoreTicketRequest;
    $rules = $request->rules();
    $rules['project_id'] = ['required', 'integer'];
    $rules['assigned_to_id'] = ['nullable', 'integer'];

    $validator = Validator::make([
        'title' => 'Fix login issue',
        'description' => 'Detailed steps to reproduce',
        'project_id' => 1,
        'priority' => 'high',
        'due_date' => '2026-11-01',
    ], $rules);

    expect($validator->passes())->toBeTrue();
});

test('StoreTicketRequest fails when required fields or priority enum are invalid', function () {
    $request = new StoreTicketRequest;
    $rules = $request->rules();
    $rules['project_id'] = ['required', 'integer'];

    $validator = Validator::make([
        'priority' => 'invalid-priority',
        'due_date' => 'invalid-date',
    ], $rules);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue()
        ->and($validator->errors()->has('description'))->toBeTrue()
        ->and($validator->errors()->has('project_id'))->toBeTrue()
        ->and($validator->errors()->has('priority'))->toBeTrue();
});

test('TransitionTicketStatusRequest enforces atomic blocked comment rule', function () {
    $request = new TransitionTicketStatusRequest;

    // Fails without comment
    $validatorWithoutComment = Validator::make(['status' => 'blocked'], $request->rules(), $request->messages());
    expect($validatorWithoutComment->fails())->toBeTrue()
        ->and($validatorWithoutComment->errors()->first('comment'))->toBe('A comment explaining the reason is required when marking a ticket as blocked.');

    // Passes with comment
    $validatorWithComment = Validator::make([
        'status' => 'blocked',
        'comment' => 'Waiting on API credentials from 3rd party',
    ], $request->rules(), $request->messages());
    expect($validatorWithComment->passes())->toBeTrue();
});

test('TransitionTicketStatusRequest allows non-blocked transitions without comment', function () {
    $request = new TransitionTicketStatusRequest;

    $validator = Validator::make(['status' => 'in_progress'], $request->rules());
    expect($validator->passes())->toBeTrue();
});

test('StoreCommentRequest and UpdateCommentRequest require a non-empty body', function () {
    $storeRequest = new StoreCommentRequest;
    $updateRequest = new UpdateCommentRequest;

    $validatorEmpty = Validator::make(['body' => ''], $storeRequest->rules());
    expect($validatorEmpty->fails())->toBeTrue()
        ->and($validatorEmpty->errors()->has('body'))->toBeTrue();

    $validatorFilled = Validator::make(['body' => 'Nice progress!'], $updateRequest->rules());
    expect($validatorFilled->passes())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Milestone 2 Policies Authorization Tests
|--------------------------------------------------------------------------
*/

test('TicketPolicy: creator can edit open ticket details but not closed tickets', function () {
    $policy = new TicketPolicy;

    $creator = new User;
    $creator->id = 10;
    $creator->role = 'user';

    $ticket = new Ticket([
        'created_by_id' => 10,
        'status' => 'new',
        'project_id' => 1,
    ]);

    expect($policy->update($creator, $ticket))->toBeTrue();

    $ticket->status = 'closed';
    expect($policy->update($creator, $ticket))->toBeFalse();
});

test('TicketPolicy: assigned user cannot edit open ticket details (spec section 2.2)', function () {
    $policy = new TicketPolicy;

    $assignee = new User;
    $assignee->id = 20;
    $assignee->role = 'user';

    $ticket = new Ticket([
        'created_by_id' => 10,
        'assigned_to_id' => 20,
        'status' => 'new',
        'project_id' => 1,
    ]);

    expect($policy->update($assignee, $ticket))->toBeFalse();
});

test('TicketPolicy: admin cannot edit details of closed tickets without reopening', function () {
    $policy = new TicketPolicy;

    $admin = new User;
    $admin->id = 99;
    $admin->role = 'admin';

    $ticket = new Ticket([
        'created_by_id' => 10,
        'status' => 'closed',
        'project_id' => 1,
    ]);

    expect($policy->update($admin, $ticket))->toBeFalse();
});

test('TicketPolicy: assigned user can reassign ticket but unrelated user cannot', function () {
    $policy = new TicketPolicy;

    $assignee = new User;
    $assignee->id = 20;
    $assignee->role = 'user';

    $stranger = new User;
    $stranger->id = 50;
    $stranger->role = 'user';

    $ticket = new Ticket([
        'created_by_id' => 10,
        'assigned_to_id' => 20,
        'status' => 'in_progress',
        'project_id' => 1,
    ]);

    expect($policy->reassign($assignee, $ticket))->toBeTrue()
        ->and($policy->reassign($stranger, $ticket))->toBeFalse();
});

test('TicketPolicy: only creator, assignee, PM, or admin can reopen closed ticket', function () {
    $policy = new TicketPolicy;

    $creator = new User;
    $creator->id = 10;
    $creator->role = 'user';

    $assignee = new User;
    $assignee->id = 20;
    $assignee->role = 'user';

    $stranger = new User;
    $stranger->id = 50;
    $stranger->role = 'user';

    $ticket = new Ticket([
        'created_by_id' => 10,
        'assigned_to_id' => 20,
        'status' => 'closed',
        'project_id' => 1,
    ]);

    expect($policy->transition($creator, $ticket, 'new'))->toBeTrue()
        ->and($policy->transition($assignee, $ticket, 'in_progress'))->toBeTrue()
        ->and($policy->transition($stranger, $ticket, 'new'))->toBeFalse();
});

test('CommentPolicy: author can edit within 5 minutes but is locked after 5 minutes', function () {
    $policy = new CommentPolicy;

    $author = new User;
    $author->id = 10;
    $author->role = 'user';

    $freshComment = new Comment(['user_id' => 10]);
    $freshComment->created_at = now()->subMinutes(2);

    $oldComment = new Comment(['user_id' => 10]);
    $oldComment->created_at = now()->subMinutes(6);

    expect($policy->update($author, $freshComment))->toBeTrue()
        ->and($policy->update($author, $oldComment))->toBeFalse();
});

test('CommentPolicy: admin can edit comments anytime bypassing 5-minute gate', function () {
    $policy = new CommentPolicy;

    $admin = new User;
    $admin->id = 99;
    $admin->role = 'admin';

    $oldComment = new Comment(['user_id' => 10]);
    $oldComment->created_at = now()->subMinutes(20);

    expect($policy->update($admin, $oldComment))->toBeTrue();
});

test('ProjectPolicy: only admin can create or manage projects', function () {
    $policy = new ProjectPolicy;

    $admin = new User;
    $admin->id = 99;
    $admin->role = 'admin';

    $user = new User;
    $user->id = 10;
    $user->role = 'user';

    expect($policy->create($admin))->toBeTrue()
        ->and($policy->create($user))->toBeFalse();
});
