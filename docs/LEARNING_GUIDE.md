# Team Ticket Tracker — Learning Guide & Concept Reference
> **Milestones Covered:** Milestone 1 (Database & Models), Milestone 2 (Auth & Policies), Subtask 3.1 (Form Requests & Validation)

This guide is designed as an interactive learning reference. It explains **what** was built, **why** it was designed that way, **how** Laravel implements it under the hood, and **how you can test your understanding**.

---

## Table of Contents
1. [The Big Picture Architecture](#1-the-big-picture-architecture)
2. [Database Design & Migrations (Milestone 1)](#2-database-design--migrations-milestone-1)
3. [Eloquent Models & Relationships (Milestone 1)](#3-eloquent-models--relationships-milestone-1)
4. [Authentication & System Hardening (Milestone 2)](#4-authentication--system-hardening-milestone-2)
5. [Authorization & Policies (Milestone 2)](#5-authorization--policies-milestone-2)
6. [Form Requests & Input Validation (Subtask 3.1)](#6-form-requests--input-validation-subtask-31)
7. [Automated Testing with Pest PHP](#7-automated-testing-with-pest-php)
8. [Self-Assessment Diagnostic & Exercises](#8-self-assessment-diagnostic--exercises)

---

## 1. The Big Picture Architecture

Whenever a user performs an action in a modern Laravel application, the request travels through distinct architectural boundaries:

```
[ Incoming HTTP Request (e.g., POST /tickets/4/transition) ]
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│ 1. Form Request Layer (Subtask 3.1)                         │
│    • Checks: "Is the incoming data valid and safe?"         │
│    • Example: Is 'status' a valid enum? Is 'comment' given? │
└──────────────────────────┬──────────────────────────────────┘
                           │ (Passed Validation)
                           ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Policy / Authorization Layer (Milestone 2)               │
│    • Checks: "Does THIS user have permission to do this?"   │
│    • Example: Can this user reopen a closed ticket?         │
└──────────────────────────┬──────────────────────────────────┘
                           │ (Authorized)
                           ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Controller & Business Logic (Subtasks 3.2 – 3.7)         │
│    • Coordinates actions, dispatches events, runs DB tx     │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Eloquent Models & Database Schema (Milestone 1)          │
│    • Persists state safely with FK constraints & auditing   │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Database Design & Migrations (Milestone 1)

### Relevant Files:
* [database/migrations/0001_01_01_000000_create_users_table.php](file:///home/killerx/Documents/projects/cet-ticket-system/database/migrations/0001_01_01_000000_create_users_table.php)
* [database/migrations/2026_10_15_142249_create_projects_table.php](file:///home/killerx/Documents/projects/cet-ticket-system/database/migrations/2026_10_15_142249_create_projects_table.php)
* [database/migrations/2026_10_15_142250_create_project_manager_table.php](file:///home/killerx/Documents/projects/cet-ticket-system/database/migrations/2026_10_15_142250_create_project_manager_table.php)
* [database/migrations/2026_10_15_143024_create_tickets_table.php](file:///home/killerx/Documents/projects/cet-ticket-system/database/migrations/2026_10_15_143024_create_tickets_table.php)
* [database/migrations/2026_10_15_143025_create_comments_table.php](file:///home/killerx/Documents/projects/cet-ticket-system/database/migrations/2026_10_15_143025_create_comments_table.php)
* [database/migrations/2026_10_16_154619_create_ticket_watchers_table.php](file:///home/killerx/Documents/projects/cet-ticket-system/database/migrations/2026_10_16_154619_create_ticket_watchers_table.php)
* [database/migrations/2026_10_16_154620_create_ticket_activity_logs_table.php](file:///home/killerx/Documents/projects/cet-ticket-system/database/migrations/2026_10_16_154620_create_ticket_activity_logs_table.php)

---

### Concept 2.1: Foreign Key Delete Actions (`cascade`, `null`, `restrict`)

When two tables are linked by a foreign key, what happens to Child rows when the Parent row is deleted?

| Delete Action | SQL Behavior | Used In This Project | Why? |
| :--- | :--- | :--- | :--- |
| **`cascadeOnDelete()`** | Deletes child rows automatically | `project_manager`, `ticket_watchers` | If a ticket or project is deleted, watcher subscriptions and manager assignments have no reason to exist. |
| **`nullOnDelete()`** | Sets the child column to `NULL` | `tickets.assigned_to_id`, `deleted_by_id` | If an employee leaves and their user account is removed, the ticket should **not** disappear; it should simply become unassigned. |
| **`restrictOnDelete()`** | Blocks the parent deletion with an error | `comments.user_id`, `tickets.created_by_id` | **Audit Trail Protection.** You must never hard-delete a user if that user authored official tickets or comments. |

> **Key Takeaway:** Choosing the right delete rule at the database migration level guarantees data integrity even if application code has a bug.

---

### Concept 2.2: Compound Primary Keys vs. Surrogate Primary Keys

1. **Compound Primary Key (Pure Pivot Table):**
   * Seen in `project_manager`:
     ```php
     $table->foreignId('user_id')->constrained()->cascadeOnDelete();
     $table->foreignId('project_id')->constrained()->cascadeOnDelete();
     $table->primary(['user_id', 'project_id']);
     ```
   * *Why?* A user is either assigned to manage a project or they are not. An auto-incrementing `id` column is redundant and wastes storage. The combination of `user_id` and `project_id` uniquely identifies the record and prevents duplicate pairings.

2. **Surrogate Primary Key (`id`) with Unique Constraint:**
   * Seen in `ticket_watchers`:
     ```php
     $table->id();
     $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
     $table->foreignId('user_id')->constrained()->cascadeOnDelete();
     $table->unique(['ticket_id', 'user_id']);
     ```
   * *Why?* Watchers are treated as first-class Eloquent models ([TicketWatcher](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/TicketWatcher.php)) that can be queried, passed into events, or extended with notification preferences. Having an `id` simplifies Eloquent interactions while `$table->unique(...)` preserves duplicate prevention.

---

### Concept 2.3: Soft Deletes & the `deleted_by_id` Pattern

* **Laravel's `SoftDeletes` trait** adds a `deleted_at` timestamp. When you call `$ticket->delete()`, SQL executes:
  `UPDATE tickets SET deleted_at = '2026-10-30 22:00:00' WHERE id = 1`
  instead of `DELETE FROM tickets WHERE id = 1`.
* **The Gotcha:** Laravel does **not** automatically track *who* performed the soft delete.
* **Our Solution:** We added a nullable foreign key `deleted_by_id` to `projects`, `tickets`, and `comments`. Before a record is soft-deleted, a model event hook sets `$model->deleted_by_id = auth()->id(); $model->saveQuietly();`.

---

## 3. Eloquent Models & Relationships (Milestone 1)

### Relevant Files:
* [app/Models/User.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/User.php)
* [app/Models/Project.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/Project.php)
* [app/Models/Ticket.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/Ticket.php)
* [app/Models/Comment.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/Comment.php)
* [app/Models/TicketWatcher.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/TicketWatcher.php)
* [app/Models/TicketActivityLog.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/TicketActivityLog.php)

---

### Concept 3.1: Defining Non-Standard Foreign Keys

Laravel assumes foreign keys follow the pattern `{model_name}_id`. When your model has multiple relationships to the same table, you must explicitly specify foreign keys:

In [app/Models/User.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/User.php#L52-L60):
```php
public function ticketsCreated(): HasMany
{
    return $this->hasMany(Ticket::class, 'created_by_id');
}

public function ticketsAssigned(): HasMany
{
    return $this->hasMany(Ticket::class, 'assigned_to_id');
}
```

In [app/Models/Ticket.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/Ticket.php#L35-L42):
```php
public function creator(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by_id')->withTrashed();
}

public function assignedTo(): BelongsTo
{
    return $this->belongsTo(User::class, 'assigned_to_id')->withTrashed();
}
```

---

### Concept 3.2: Why `->withTrashed()` on BelongsTo Relationships?

Imagine User #3 creates 10 tickets. Six months later, User #3 leaves the organization and their account is soft-deleted (`deleted_at` is set).

* **Without `withTrashed()`:** Calling `$ticket->creator->name` generates a query `SELECT * FROM users WHERE id = 3 AND deleted_at IS NULL`. It returns `null`, throwing an error:
  `Attempt to read property "name" on null`.
* **With `withTrashed()`:** Eloquent removes the `deleted_at IS NULL` restriction for that relationship, allowing you to display:
  `Created by: John Doe (Deactivated)`.

---

### Concept 3.3: Model Helper Encapsulation (`managesProject`)

Instead of writing complex queries inside controllers or policies, encapsulate domain questions on the model itself:

In [app/Models/User.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/User.php#L36-L45):
```php
public function managesProject(int|string|Project $project): bool
{
    // Fail-safe: if the user was demoted to 'user', ignore any leftover pivot rows
    if ($this->role !== 'manager') {
        return false;
    }

    $projectId = $project instanceof Project ? $project->id : (int) $project;

    return $this->managedProjects()->where('projects.id', $projectId)->exists();
}
```

---

## 4. Authentication & System Hardening (Milestone 2)

### Relevant Files:
* [routes/auth.php](file:///home/killerx/Documents/projects/cet-ticket-system/routes/auth.php)
* [app/Models/User.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/User.php#L19)

---

### Concept 4.1: Internal Tool vs. Public SaaS Security

Laravel Breeze ships configured for public web applications. For an internal ticket system:
1. **Disabled Public Registration:** Removed `/register` routes. Only Admins can create user accounts.
2. **Disabled Self-Account Deletion:** Users cannot delete their own accounts from their profile page.
3. **Mass-Assignment Guarding:**
   In [app/Models/User.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/User.php#L19):
   ```php
   protected $fillable = ['name', 'email', 'password'];
   ```
   Notice that `'role'` is **not** in `$fillable`. If a malicious user submits `POST /profile` with `role=admin`, Laravel automatically discards the field, preventing unauthorized privilege escalation.

---

## 5. Authorization & Policies (Milestone 2)

### Relevant Files:
* [app/Policies/TicketPolicy.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Policies/TicketPolicy.php)
* [app/Policies/CommentPolicy.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Policies/CommentPolicy.php)
* [app/Policies/ProjectPolicy.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Policies/ProjectPolicy.php)

---

### Concept 5.1: Authentication vs. Authorization

* **Authentication (Auth):** "Who are you?" (Verifies credentials via session/cookies).
* **Authorization:** "Are you allowed to perform *this specific action* on *this specific record*?" (Handled by Policies and Gates).

---

### Concept 5.2: State-Dependent Authorization (Closed Ticket Lock)

In [app/Policies/TicketPolicy.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Policies/TicketPolicy.php#L37-L51):
```php
public function update(User $user, Ticket $ticket): bool
{
    // Rule: Once a ticket is closed, its metadata is locked for EVERYONE.
    if ($ticket->status === 'closed') {
        return false;
    }

    if ($user->role === 'admin') {
        return true;
    }

    $isCreator = $user->id === $ticket->created_by_id;
    $isManager = $user->managesProject($ticket->project_id);

    return $isCreator || $isManager;
}
```
> Notice that even an `admin` is denied if `$ticket->status === 'closed'`. To edit a closed ticket, business rules dictate that it must first be **reopened**.

---

### Concept 5.3: Project Transfer Authorization

Can a Manager move a ticket from Project A to Project B?
Only if they manage **both** projects!

In [app/Policies/TicketPolicy.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Policies/TicketPolicy.php#L73-L80):
```php
public function transferProject(User $user, Ticket $ticket, int $targetProjectId): bool
{
    if ($user->role === 'admin') {
        return true;
    }

    return $user->managesProject($ticket->project_id) && $user->managesProject($targetProjectId);
}
```

---

### Concept 5.4: Time-Window Authorization & Carbon 3

Business Rule: Regular authors can edit/delete comments, but only within **5 minutes** of posting.

In [app/Policies/CommentPolicy.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Policies/CommentPolicy.php#L37-L53):
```php
public function update(User $user, Comment $comment): bool
{
    if ($user->role === 'admin') {
        return true;
    }

    $isWithinFiveMinutes = $comment->created_at->gt(now()->subMinutes(5));

    if (! $isWithinFiveMinutes) {
        return false;
    }

    $isAuthor = $user->id === $comment->user_id;
    $isManager = $comment->ticket ? $user->managesProject($comment->ticket->project_id) : false;

    return $isAuthor || $isManager;
}
```

> **Why not `$comment->created_at->diffInMinutes() <= 5`?**
> In Carbon 3 (shipped with Laravel 11/12), `diffInMinutes()` returns a signed integer depending on which date is past or future. Using `$comment->created_at->gt(now()->subMinutes(5))` is strictly boolean and immune to sign bugs.

---

## 6. Form Requests & Input Validation (Subtask 3.1)

### Relevant Files:
* [app/Http/Requests/StoreTicketRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/StoreTicketRequest.php)
* [app/Http/Requests/UpdateTicketRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/UpdateTicketRequest.php)
* [app/Http/Requests/TransitionTicketStatusRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/TransitionTicketStatusRequest.php)
* [app/Http/Requests/StoreCommentRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/StoreCommentRequest.php)
* [app/Http/Requests/UpdateCommentRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/UpdateCommentRequest.php)

---

### Concept 6.1: Why Form Requests instead of `$request->validate()` in Controllers?

1. **Single Responsibility Principle:** Controllers shouldn't care about field lengths, regexes, or date formats. They should only handle application flow.
2. **Early Interception:** If validation fails, Laravel halts execution before the controller is ever invoked, redirecting back with errors.
3. **Unit Testability:** You can test validation rules in isolation without hitting controllers or database routes.

---

### Concept 6.2: Conditional Validation (`required_if`)

Business Rule: When a ticket is marked as `blocked`, an explanatory comment **must** be provided.

In [app/Http/Requests/TransitionTicketStatusRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/TransitionTicketStatusRequest.php#L26-L44):
```php
public function rules(): array
{
    return [
        'status' => [
            'required',
            'string',
            Rule::in(['new', 'in_progress', 'waiting', 'blocked', 'closed']),
        ],
        'comment' => ['required_if:status,blocked', 'nullable', 'string'],
    ];
}

public function messages(): array
{
    return [
        'comment.required_if' => 'A comment explaining the reason is required when marking a ticket as blocked.',
    ];
}
```

---

### Concept 6.3: Advanced After-Validation Hooks (`withValidator`)

Sometimes validation depends on dynamic authorization checks that standard validation rules cannot handle.

In [app/Http/Requests/UpdateTicketRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/UpdateTicketRequest.php#L41-L61):
```php
public function withValidator($validator): void
{
    $validator->after(function ($validator) {
        $ticket = $this->route('ticket');

        if ($ticket instanceof Ticket) {
            $newProjectId = (int) $this->input('project_id');

            // If project is being changed, verify the user has transfer rights
            if ($newProjectId && $newProjectId !== $ticket->project_id) {
                if (! $this->user()?->can('transferProject', [$ticket, $newProjectId])) {
                    $validator->errors()->add(
                        'project_id',
                        'You are not authorized to transfer this ticket to another project'
                    );
                }
            }
        }
    });
}
```

---

## 7. Automated Testing with Pest PHP

### Relevant Files:
* [tests/Unit/QaSuiteTest.php](file:///home/killerx/Documents/projects/cet-ticket-system/tests/Unit/QaSuiteTest.php)
* [tests/README.md](file:///home/killerx/Documents/projects/cet-ticket-system/tests/README.md)

### Concept 7.1: Testing Form Requests In-Memory
You don't need a running database or HTTP server to test validation rules! Use Laravel's `Validator::make()`:

```php
test('TransitionTicketStatusRequest enforces atomic blocked comment rule', function () {
    $request = new TransitionTicketStatusRequest;

    // Fails when status is 'blocked' and comment is missing:
    $validatorWithoutComment = Validator::make(
        ['status' => 'blocked'],
        $request->rules(),
        $request->messages()
    );
    expect($validatorWithoutComment->fails())->toBeTrue();

    // Passes when status is 'blocked' and comment is present:
    $validatorWithComment = Validator::make(
        ['status' => 'blocked', 'comment' => 'Waiting for vendor'],
        $request->rules(),
        $request->messages()
    );
    expect($validatorWithComment->passes())->toBeTrue();
});
```

To run this test suite anytime:
```bash
vendor/bin/pest tests/Unit/QaSuiteTest.php
```

---

## 8. Self-Assessment Diagnostic & Exercises

Test your understanding with these 4 practical questions.

### Question 1: Data Integrity
* **Scenario:** Why does `comments.user_id` use `restrictOnDelete()` while `tickets.assigned_to_id` uses `nullOnDelete()`?
* <details>
  <summary><b>View Answer</b></summary>
  Comments represent an official, immutable audit history of who said what. Hard-deleting a user who authored comments would corrupt the audit trail, so the database strictly forbids it (<code>restrict</code>). In contrast, an assigned user on a ticket is merely a current operational assignment; if that user leaves, the ticket should remain intact and simply become unassigned (<code>null</code>).
  </details>

### Question 2: RBAC Architecture
* **Scenario:** User Bob has `role = 'user'`, but a previous database entry still exists linking Bob's `user_id` in `project_manager` for Project #5. Does `Bob->managesProject(5)` return `true` or `false`?
* <details>
  <summary><b>View Answer</b></summary>
  It returns <code>false</code>. In <code>User::managesProject()</code>, we placed an explicit guard check: <code>if ($this->role !== 'manager') return false;</code>. This prevents stale pivot table records from inadvertently granting managerial privileges.
  </details>

### Question 3: Policy Gates
* **Scenario:** An Admin attempts to update the title of Ticket #12 via `POST /tickets/12`. The ticket currently has `status = 'closed'`. Does `TicketPolicy::update()` allow or deny this?
* <details>
  <summary><b>View Answer</b></summary>
  It <b>denies</b> it. The very first line of <code>TicketPolicy::update()</code> checks <code>if ($ticket->status === 'closed') return false;</code> before any admin role check. Business rules state that no one can modify a closed ticket's metadata without reopening it first.
  </details>

### Question 4: Request Pipeline
* **Scenario:** A user submits an empty comment body `POST /comments` with `['body' => '']`. Does the controller method run?
* <details>
  <summary><b>View Answer</b></summary>
  No. <code>StoreCommentRequest</code> runs before the controller method is reached. When validation fails, Laravel immediately throws a <code>ValidationException</code>, redirecting back with error messages (or sending a 422 JSON response). The controller code is never executed.
  </details>

---

## Quick Reference Summary Table

| Layer | Responsibility | Key Class in Project | Primary Gotcha / Best Practice |
| :--- | :--- | :--- | :--- |
| **Migrations** | DB Schema & integrity | `create_tickets_table.php` | Match delete behaviors (`cascade`, `null`, `restrict`) to business impact. |
| **Models** | ORM & Relationships | [Ticket.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Models/Ticket.php) | Add `->withTrashed()` to audit relations so deactivated users don't break UI. |
| **Policies** | Authorization | [TicketPolicy.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Policies/TicketPolicy.php) | Check both role and resource context (e.g., `managesProject`). |
| **Form Requests** | Input validation | [TransitionTicketStatusRequest.php](file:///home/killerx/Documents/projects/cet-ticket-system/app/Http/Requests/TransitionTicketStatusRequest.php) | Use `required_if` for conditional business rules; keep controllers slim. |
| **Unit Tests** | Verification | [QaSuiteTest.php](file:///home/killerx/Documents/projects/cet-ticket-system/tests/Unit/QaSuiteTest.php) | Test validation and policies in-memory for speed and isolation. |
