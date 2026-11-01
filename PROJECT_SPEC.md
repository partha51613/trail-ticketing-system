# Team Ticket Tracker — Project Specification & Architecture Guide

> **Developer & AI Agent Note:** This document is the single source of truth for the Team Ticket Tracker application. Any code generation, refactoring, or feature development must strictly adhere to the standards, permission models, and business logic detailed below. AI coding assistants should act as senior architects and mentors, guiding the developer step-by-step to implement code adhering to this specification.

---

## 🚀 Live Project Status & Progress Tracker

* [x] **Milestone 1:** Database, Migrations & Models *(Completed)*
  * [x] Subtask 1.1: Update `users` migration (added `role` enum, `softDeletes`)
  * [x] Subtask 1.2: Create `projects` migration (`name`, `description`, `timestamps`, `softDeletes`, `deleted_by_id`)
  * [x] Subtask 1.3: Create `project_manager` pivot migration (compound PK `['user_id', 'project_id']`, cascade on delete)
  * [x] Subtask 1.4: Create `tickets` migration (FKs, status/priority enums, softDeletes, `deleted_by_id`)
  * [x] Subtask 1.5: Create `comments` migration (FKs, `edited_at`, softDeletes, `deleted_by_id`, restrict delete on user)
  * [x] Subtask 1.6: Create `ticket_watchers` migration (surrogate `id`, compound unique `['ticket_id', 'user_id']`, Eloquent Model)
  * [x] Subtask 1.7: Create `ticket_activity_logs` migration (FKs, `field_changed`, old/new text values, restrict delete on user)
  * [x] Subtask 1.8: Run migrations and verify schema (`php artisan migrate`)
  * [x] Subtask 1.9: Implement Eloquent models & relationships (`User`, `Project`, `Ticket`, `Comment`, `TicketWatcher`, `TicketActivityLog`)
  * [x] Subtask 1.10: Create Database Seeders (`AdminUserSeeder`, `SampleDataSeeder`)
* [x] **Milestone 2:** Authentication & Policies *(Completed)*
  * [x] Laravel Breeze installed (disable public registration, disable self-delete, guard `role` from mass assignment, verify Tailwind v4)
* [ ] **Milestone 3:** Controllers & Business Logic *(Current)*
  * [x] Subtask 3.1: Create Form Requests (`StoreTicketRequest`, `UpdateTicketRequest`, `TransitionTicketStatusRequest`, `StoreCommentRequest`, `UpdateCommentRequest`)
  * [x] Subtask 3.2: Implement `TicketObserver`
    * [x] Subtask 3.2.1: Generate `TicketObserver` and register it with `Ticket` model
    * [x] Subtask 3.2.2: Implement `created` event (auto-add creator & initial assignee to `ticket_watchers`)
    * [x] Subtask 3.2.3: Implement `updating` / `updated` event (audit-log changes on `status`, `assigned_to_id`, `priority`, `due_date`, `project_id`)
    * [x] Subtask 3.2.4: Handle reassignment watcher sync (auto-add new assignee to `ticket_watchers`)
  * [ ] Subtask 3.3: Implement `TicketController` (index, show, store, update, destroy)
  * [ ] Subtask 3.4: Implement ticket status transition & reassignment actions (atomic `blocked` rule + comment in `DB::transaction()`)
  * [ ] Subtask 3.5: Implement `CommentController` (store comment, 5-minute server-side policy check for update/delete)
  * [ ] Subtask 3.6: Implement `WatcherController` (toggle watch/unwatch subscriptions)
  * [ ] Subtask 3.7: Implement `ProjectController` (Admin-only CRUD for project management)
  * [ ] Subtask 3.8: Define application routes in `routes/web.php` with authorization middleware
* [ ] **Milestone 4:** Notifications & Queues *(Phase 2 / Post-MVP)*
  * [ ] Queue configuration & Mailables (`$afterCommit = true`, actor ID exclusion)
* [ ] **Milestone 5:** Blade UI & Timezone Formatting
  * [ ] Dashboard & filters (Status, Project, Priority, Assignee, Workload view)
  * [ ] Ticket detail & comment thread UI
  * [ ] `Asia/Kolkata` display formatting helpers

---

## 1. Executive Summary & Core Constraints

* **Purpose:** Internal work-tracking system for ~50 users across projects.
* **Core Principle:** Simple, readable, maintainable internal tool. **Do not turn this into a complex Jira clone or introduce microservices.**
* **Stack:**
  * **Backend:** Laravel 12 (PHP 8.3+)
  * **Database:** MySQL / MariaDB
  * **Frontend:** Laravel Blade + Tailwind CSS v4
  * **Email & Queues (Phase 2):** SMTP dispatched asynchronously via Laravel Queued Jobs
* **Timezone Rules:**
  * `config('app.timezone')` must remain **`UTC`**.
  * All database timestamps (`created_at`, `updated_at`, `deleted_at`, `edited_at`) stored strictly in **UTC**.
  * Conversion to **`Asia/Kolkata`** (`IST`) happens only at display time in Blade templates or presentation accessors.
  * `due_date` is stored as **Date only** (`YYYY-MM-DD`), without time/timezone components.
  * **Overdue Definition:** A ticket is overdue if `due_date < today_in_kolkata` AND `status !== 'closed'`. Tickets due today are not overdue until midnight IST passes. Closed tickets are never overdue.

---

## 2. Authorization & Roles Architecture

There are strictly **three permission levels**: `user`, `manager`, and `admin`.

```
[ User ]   ---> Global viewing; creates tickets; edits OWN ticket details (if open); reassigns tickets assigned to them; transitions any ticket to active/closed; reopens tickets assigned to them or created by them.
[ Manager] ---> User permissions + Project-level management for assigned projects (edit open ticket details, manage/delete tickets & comments, move tickets between projects they manage, reopen any ticket in assigned projects).
[ Admin ]   ---> Unrestricted system access (user management, project assignments, global actions, comment editing anytime, move tickets across any project, soft-delete restores).
```

### 2.1 Project Manager Relationship & Helpers
* Managers are **Project Managers** mapped to projects via the `project_manager` pivot table.
* A project can have multiple Managers. A user can manage multiple projects.
* **CRITICAL POLICY RULE:** Authorization checks for project management MUST evaluate both role and project context:
  ```php
  public function managesProject(int|Project $project): bool
  {
      if ($this->role !== 'manager') {
          return false;
      }
      $projectId = $project instanceof Project ? $project->id : $project;
      return $this->managedProjects()->where('projects.id', $projectId)->exists();
  }
  ```
  *(Verifying `role === 'manager'` ensures stale `project_manager` rows after a user demotion grant no privileges).*

### 2.2 Permissions Matrix

| Action | Creator | Assigned User | Other User | Manager (Assigned Project) | Manager (Other Project) | Admin |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| View any ticket / comment / history | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Create ticket | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Edit open ticket details** *(title, description, priority, due date)* | ✅ (if open) | ❌ | ❌ | ✅ (if open) | ❌ | ✅ (if open) |
| **Edit ticket details while Closed** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ *(Must reopen first)* |
| **Reassign ticket** *(hand-off / unassign)* | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ |
| **Transfer project** | ❌ | ❌ | ❌ | ✅ *(if manages BOTH projects)* | ❌ | ✅ |
| **Transition active statuses** *(new ↔ in_progress ↔ waiting ↔ blocked)* | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Close ticket** *(Transition to `closed`)* | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Reopen ticket** *(Transition from `closed` to any active status: new, in_progress, waiting, blocked)* | ✅ | ✅ | ❌ | ✅ | ✅ *(if assigned or creator)* | ✅ |
| **Add comment while Open or Closed** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Edit comment** | Author (≤ 5 min) | Author (≤ 5 min) | Author (≤ 5 min) | Author / PM (≤ 5 min) | Author (≤ 5 min) | ✅ *(Anytime)* |
| **Delete comment** | Author (≤ 5 min) | Author (≤ 5 min) | Author (≤ 5 min) | ✅ | Author (≤ 5 min) | ✅ |
| **Delete ticket** *(Soft delete)* | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Manage users / projects / SMTP | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| View / Restore soft-deleted items | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

*Note on Precedence:* Ticket Creator permissions take priority over non-assigned Manager status. If a Manager of Project A creates a ticket in Project B, they retain full creator edit privileges on that ticket.

---

## 3. Data Schema & Model Specification

```
 [ users ]
    │
    ├── pivot: project_manager (user_id, project_id) [Compound primary key]
    ├── tickets (created_by_id, assigned_to_id, deleted_by_id)
    ├── comments (user_id, deleted_by_id)
    ├── ticket_watchers (user_id, ticket_id) [Has surrogate id, model TicketWatcher]
    ├── ticket_activity_logs (user_id, ticket_id)
    └── projects (deleted_by_id)

 [ projects ]
    │
    └── tickets (project_id)
         │
         ├── ticket_activity_logs (ticket_id, user_id)
         ├── comments (ticket_id)
         └── ticket_watchers (user_id, ticket_id)
```

### 3.1 Migration Specifications & Edge Case Mitigations

* **Global Scopes & Soft Delete Integrity:**
  * Eloquent models representing relations to Users and Projects (such as `assignedTo`, `createdBy`, `deletedBy`, and activity logs) must append `->withTrashed()` to prevent runtime errors when referenced users/projects are soft-deleted.
  * Blade templates must utilize null-safe operators (e.g., `$ticket->assignedTo?->name ?? 'Unassigned'`).
  * `deleted_by_id` is **not** populated automatically by Laravel's `SoftDeletes` trait. Each soft-deletable model (`Project`, `Ticket`, `Comment`) must use a model `deleting` event hook (or dedicated observer) to set `$model->deleted_by_id = auth()->id(); $model->saveQuietly();` before soft-deleting.
  * Soft-deleted users retain their unique email address in the database, preventing re-registration with that same email unless permanently purged.

* **`users`**:
  * `id`
  * `name` (string)
  * `email` (string, unique)
  * `password` (string)
  * `role` (`enum: ['user', 'manager', 'admin']`, default: `'user'`)
  * `remember_token`
  * `timestamps`
  * `softDeletes`

* **`projects`**:
  * `id`
  * `name` (string)
  * `description` (text, nullable)
  * `timestamps`
  * `softDeletes`
  * `deleted_by_id` (foreignId -> `users`, nullable, nullOnDelete)

* **`project_manager`**:
  * `user_id` (foreignId -> `users`, cascadeOnDelete)
  * `project_id` (foreignId -> `projects`, cascadeOnDelete)
  * `timestamps`
  * *Primary Key:* `$table->primary(['user_id', 'project_id']);`

* **`tickets`**:
  * `id`
  * `title` (string)
  * `description` (text)
  * `project_id` (foreignId -> `projects`, restrictOnDelete)
  * `created_by_id` (foreignId -> `users`, restrictOnDelete)
  * `assigned_to_id` (foreignId -> `users`, nullable, nullOnDelete)
  * `status` (`enum: ['new', 'in_progress', 'waiting', 'blocked', 'closed']`, default: `'new'`)
  * `priority` (`enum: ['low', 'medium', 'high', 'urgent']`, default: `'medium'`)
  * `due_date` (`date`, nullable)
  * `timestamps`
  * `softDeletes`
  * `deleted_by_id` (foreignId -> `users`, nullable, nullOnDelete)

* **`comments`**:
  * `id`
  * `ticket_id` (foreignId -> `tickets`, cascadeOnDelete)
  * `user_id` (foreignId -> `users`, restrictOnDelete) *(Protects audit trail from force-deletes)*
  * `body` (text)
  * `edited_at` (timestamp, nullable)
  * `timestamps`
  * `softDeletes`
  * `deleted_by_id` (foreignId -> `users`, nullable, nullOnDelete)

* **`ticket_watchers`**:
  * `id` (surrogate primary key; model `TicketWatcher` exists for querying)
  * `ticket_id` (foreignId -> `tickets`, cascadeOnDelete)
  * `user_id` (foreignId -> `users`, cascadeOnDelete)
  * `timestamps`
  * *Unique constraint:* `$table->unique(['ticket_id', 'user_id']);`

* **`ticket_activity_logs`**:
  * `id`
  * `ticket_id` (foreignId -> `tickets`, cascadeOnDelete)
  * `user_id` (foreignId -> `users`, restrictOnDelete) *(Protects audit trail from force-deletes)*
  * `field_changed` (string)
  * `old_value` (text, nullable)
  * `new_value` (text, nullable)
  * `timestamps`

---

## 4. Key Business Logic & Transaction Rules

### 4.1 Ticket Editing & Status Transitions
* **Open Ticket Metadata Editing:**
  * Only the ticket creator, an assigned Project Manager for that ticket's project, or an Admin can edit ticket details (title, description, priority, due date).
* **Closed Ticket Editing Lock:**
  * Once a ticket is in `closed` status, its metadata (title, description, priority, due date, project, assignee) is **locked** and cannot be edited. It must be reopened first. Comments can still be added on closed tickets.
* **Reassignment Hand-off:**
  * The creator, an assigned Project Manager, an Admin, and the **currently assigned user** can reassign the ticket to another user or unassign it.
* **Project Transfer Restriction:**
  * A ticket's `project_id` can only be changed by an **Admin**, or by a **Manager who manages BOTH the source and target projects**. Creators cannot transfer tickets across projects.
* **Status Transitions & Policy Contract:**
  * `TicketPolicy::transition(User $user, Ticket $ticket, string $targetStatus): bool` must accept both the ticket (current status) and the desired `$targetStatus`.
  * **Closing a Ticket:** Any user can transition any ticket from an active status (`new`, `in_progress`, `waiting`, `blocked`) to `closed`.
  * **Reopening a Ticket (Exiting `closed`):** Only Admins, assigned Project Managers, the ticket's current assignee (`assigned_to_id`), and the original creator can move a ticket out of `closed`. The user may choose to reopen into `new`, `in_progress`, `waiting`, or `blocked`.
  * **Atomic Blocked Rule:** When transitioning a ticket status to `blocked` (whether from active or via reopen), the user **must** submit an accompanying comment explaining why. This update and comment creation **must** execute inside a single `DB::transaction()` block. If comment creation fails, the status update rolls back.
  * **Unblocking Rule:** Transitioning a ticket **out** of `blocked` requires **no mandatory comment or reason**.

### 4.2 Comment Rules & 5-Minute Window
* Any user can post comments on any ticket (including closed tickets).
* **5-Minute Server-Side Edit Window:**
  * For regular authors and assigned Project Managers, a comment can only be edited within **5 minutes** of its `created_at` timestamp.
  * **Carbon 3 Check:** Must be verified in `CommentPolicy` via `$comment->created_at->gt(now()->subMinutes(5))`. (`diffInMinutes` is signed in Carbon 3 and must not be used).
  * **Admin Privilege:** Admins can edit comments at **any time** (bypassing the 5-minute gate).
  * Comment deletion within 5 minutes is permitted for the author. Assigned Project Managers and Admins can delete comments at any time.

### 4.3 Activity Logging & Soft-Delete Resolution
* Every change to a ticket's tracked fields (`status`, `assigned_to_id`, `priority`, `due_date`, `project_id`) must automatically generate a record in `ticket_activity_logs`.
* **Foreign Key Field Resolution:** Because `old_value` and `new_value` are stored as text, the log stores the raw foreign key ID (e.g., user ID or project ID). The UI presentation layer must resolve referenced models using `User::withTrashed()->find($id)?->name` and `Project::withTrashed()->find($id)?->name` to render human-readable names safely.

### 4.4 Notifications & Watchers (Phase 2 / Post-MVP)
* **Automatic Watchers:** When a ticket is created, both the **ticket creator** and the **initial assignee** (if assigned) are automatically added to `ticket_watchers`. If the ticket is reassigned later, the new assignee is automatically added to `ticket_watchers`.
* Any user can toggle watch/unwatch on any ticket at will.
* **Queued Notifications:** Notifications must be dispatched asynchronously via queued Jobs (`ShouldQueue`) with `$afterCommit = true`.
* **Self-Notification Exclusion:** Because `auth()->id()` is null inside background queue workers, the actor's user ID (`$actorId`) must be passed into the Job/Notification constructor and excluded from recipient queries:
  ```php
  $recipients = $ticket->watchers()
      ->where('users.id', '!=', $actorId)
      ->get();
  ```

---

## 5. Implementation Roadmap & Milestones

### Milestone 1: Database & Models *(Current)*
* [x] **Subtask 1.1:** Update `users` migration (added `role` enum, `softDeletes`)
* [x] **Subtask 1.2:** Create `projects` migration (`name`, `description`, `timestamps`, `softDeletes`, `deleted_by_id`)
* [x] **Subtask 1.3:** Create `project_manager` pivot migration (compound PK `['user_id', 'project_id']`, cascade on delete)
* [x] **Subtask 1.4:** Create `tickets` migration (FKs, status/priority lowercase enums, softDeletes, `deleted_by_id`)
* [x] **Subtask 1.5:** Create `comments` migration (FKs, `edited_at`, softDeletes, `deleted_by_id`, restrict delete on user)
* [x] **Subtask 1.6:** Create `ticket_watchers` migration (surrogate `id`, compound unique `['ticket_id', 'user_id']`)
* [x] **Subtask 1.7:** Create `ticket_activity_logs` migration (FKs, `field_changed`, old/new text values, restrict delete on user)
* [x] **Subtask 1.8:** Run migrations and verify table schema (`php artisan migrate`)
* [x] **Subtask 1.9:** Implement Eloquent models and relationships (`User`, `Project`, `Ticket`, `Comment`, `TicketWatcher`, `TicketActivityLog`) with `withTrashed()` scopes for audit trails.
* [x] **Subtask 1.10:** Create Database Seeders (`AdminUserSeeder`, `SampleDataSeeder`).

### Milestone 2: Authentication & Authorization Policies *(Completed)*
* [x] Install Laravel Breeze (Blade stack) with hardening:
  * Disable public user registration routes (internal tool; admins create users).
  * Remove user self-delete account capability.
  * Ensure `role` attribute is not mass-assignable in `User` model.
  * Verify frontend asset pipeline uses Tailwind CSS v4.
* [x] Implement `TicketPolicy`:
  * `update`: Creator, assigned PM, Admin (only when ticket is not closed).
  * `reassign`: Creator, assigned PM, Admin, or currently assigned user.
  * `transferProject`: Admin, or Manager managing both source and target projects.
  * `transition`: Evaluates `$targetStatus`; any user for active/close; assigned user, creator, assigned PM, Admin for reopen.
  * `delete`: Assigned PM, Admin.
* [x] Implement `CommentPolicy`:
  * `update`: Admin anytime; author or assigned PM if `$comment->created_at->gt(now()->subMinutes(5))`.
  * `delete`: Author if within 5 min; assigned PM or Admin anytime.
* [x] Implement `ProjectPolicy`: Admin only for CRUD.

### Milestone 3: Controllers & Validation Logic
* [x] **Subtask 3.1:** Create Form Requests:
  * [x] `StoreTicketRequest` (validates title, description, project_id, priority, due_date, optional assigned_to_id)
  * [x] `UpdateTicketRequest` (validates title, description, priority, due_date, project_id, assigned_to_id)
  * [x] `TransitionTicketStatusRequest` (validates status in enum; requires comment if status is `blocked`)
  * [x] `StoreCommentRequest` (validates body)
  * [x] `UpdateCommentRequest` (validates body)
* [ ] **Subtask 3.2:** Implement `TicketObserver`:
  * [x] Subtask 3.2.1: Generate `TicketObserver` and register it with `Ticket` model (via attribute `#[ObservedBy]` or `EventServiceProvider`)
  * [x] Subtask 3.2.2: Implement `created` event (auto-add creator and initial assignee to `ticket_watchers`)
  * [ ] Subtask 3.2.3: Implement `updating`/`updated` event (audit-log changes on `status`, `assigned_to_id`, `priority`, `due_date`, `project_id` into `ticket_activity_logs`)
  * [ ] Subtask 3.2.4: Handle reassignment watcher sync (auto-add new assignee to `ticket_watchers` when `assigned_to_id` changes)
* [ ] **Subtask 3.3:** Implement `TicketController`:
  * `index` (with project/status/priority/assignee filters), `create`, `store`, `show`, `edit`, `update`, `destroy`.
* [ ] **Subtask 3.4:** Implement Status Transitions & Reassignment:
  * Atomic `blocked` transition with comment in `DB::transaction()`.
  * Reopen authorization checks with target status selection.
* [ ] **Subtask 3.5:** Implement `CommentController`:
  * `store`, `update`, `destroy` with 5-minute server-side policy gate.
* [ ] **Subtask 3.6:** Implement `WatcherController`:
  * Toggle watcher subscription for authenticated user.
* [ ] **Subtask 3.7:** Implement `ProjectController`:
  * Admin-only CRUD + manager assignment sync.
* [ ] **Subtask 3.8:** Register routes in `routes/web.php` with auth and policy middleware.

### Milestone 4: Notifications & Queues *(Phase 2 / Post-MVP)*
* [ ] Configure queue worker (`QUEUE_CONNECTION=database` or `redis`).
* [ ] Implement Mailables and Queued Jobs (`$afterCommit = true`) with constructor `$actorId` self-notification exclusion.
* [ ] Automatic creator & assignee watcher subscription listeners.

### Milestone 5: Blade UI & Timezone Formatting
* [ ] Create layout and navigation with Tailwind CSS v4.
* [ ] Implement Ticket Dashboard with filtering: Status, Project, Priority, Assignee, and Assignee Workload view (active open ticket counts).
* [ ] Implement Ticket Detail view with Activity History, Watcher toggle, and Comments thread (with 5-min gate and null-safe user attributes).
* [ ] Implement `Asia/Kolkata` display formatting accessors/helpers.

---

## 6. QA Acceptance Criteria & Edge Case Rules

1. **Contextual Manager Rule:** A user who is a Manager for Project A cannot delete comments, delete tickets, or edit non-created ticket details in Project B unless explicitly assigned as a Manager for Project B.
2. **Reopen Permission Guard:** A ticket in `closed` status can only be reopened by its current assignee (`assigned_to_id`), its original creator, an assigned Project Manager, or an Admin. Other users attempting to reopen receive a 403 Forbidden. Reopened tickets can be moved to `new`, `in_progress`, or `blocked` (which triggers the atomic comment rule).
3. **Closed Ticket Lock:** A ticket in `closed` status cannot have its details (title, description, priority, due date) updated. It must be reopened first. Comments can still be posted on closed tickets.
4. **Reassignment Permission:** The currently assigned user can reassign the ticket to another user or unassign it without requiring PM or creator intervention.
5. **Project Transfer Guard:** Only an Admin, or a Manager managing both the source and target projects, can move a ticket to a different project.
6. **Closing Permission Check:** Any authenticated user can transition any ticket from an active status (`new`, `in_progress`, `blocked`) to `closed`.
7. **Blocked Status Guard:** Submitting a status change to `blocked` without an accompanying comment must fail validation and perform no DB modifications. Moving out of `blocked` requires no comment.
8. **Comment 5-Minute Gate:** Regular users and PMs attempting to update a comment at 5 minutes and 1 second after `created_at` must be rejected by `CommentPolicy`. Admins can update comments at any time.
9. **Timezone & Overdue Accuracy:** Database stores UTC timestamps; UI displays `Asia/Kolkata` (IST). `due_date` displays strictly as `YYYY-MM-DD`. A ticket is overdue only if `due_date < today_in_kolkata` and status is not `closed`.
10. **Audit Protection:** Users cannot be deleted if referenced by comments or activity logs (`restrictOnDelete`). Historical logs resolve deleted user and project names safely via `withTrashed()`.

---

## 7. Decision Log

| Date | Topic | Decision | Rationale |
| :--- | :--- | :--- | :--- |
| Oct 2026 | Milestone 4 Priority | Phase 2 / Post-MVP (Option 1) | Allows core ticket workflows and UI to be tested and deployed quickly without waiting for SMTP setup. |
| Oct 2026 | Admin Comment Editing | Unrestricted (Option 1) | Aligns with Admin role definition; Admins can fix errors or moderate content at any time. |
| Oct 2026 | Reopened Target Status | User Choice (Option 1) | Allows flexibility to reopen directly into `new`, `in_progress`, or `blocked` (with atomic comment). |
| Oct 2026 | Ticket Reassignment | Permitted for Assignee (Option 1) | Enables direct task hand-offs without requiring creator or manager bottlenecks. |
| Oct 2026 | Editing Closed Tickets | Locked Metadata, Open Comments (Option 1) | Protects historical record of closed work while permitting post-resolution discussion. |
| Oct 2026 | Project Transfer | Admin + Dual-Project PM Only (Option 1) | Prevents tickets from being dumped into unmanaged projects by unauthorized users. |
| Oct 2026 | Watcher Auto-Enrollment | Creator & Assignee Auto-Added (Option 1) | Ensures stakeholders automatically follow progress without manual intervention. |
| Oct 2026 | Overdue Evaluation | Due Date < Today IST & Not Closed (Option 1) | Avoids marking items due today as overdue prematurely; closed tickets cannot be overdue. |
| Oct 2026 | Workload Filter | Assignee Open Ticket Counts (Option 1) | Provides high-visibility overview of team load without Jira-level sprint complexity. |
| Oct 2026 | Enum Database Casing | Lowercase snake_case (Option 1) | Standardizes DB schema conventions; display labels formatted in presentation layer. |
| Oct 2026 | Waiting Status Added | Added `waiting` to active status enum | Allows tickets waiting on external dependencies/clients without requiring a mandatory blocking comment. |

---

## 8. AI Assistant Prompt Template

> Copy and paste this prompt when starting a new session with an AI coding assistant:
>
> *"I am working on a Laravel project following the specification in `PROJECT_SPEC.md`. We are currently on **[Milestone X, Subtask Y]**. Please act as my senior architect and coding mentor. Guide me through the implementation details and concepts so I can write the code myself. Adhere strictly to the project rules and policies."*
