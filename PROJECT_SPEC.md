Team Ticket Tracker — Project Specification & Architecture Guide

Developer & AI Agent Note: This document is the single source of truth for the Team Ticket Tracker application. Any code generation, refactoring, or feature development must strictly adhere to the standards, permission models, and business logic detailed below.

1. Executive Summary & Core Constraints

Purpose: Internal work-tracking system for ~50 users across projects.

Core Principle: Simple, readable, maintainable internal tool. Do not turn this into a complex Jira clone or introduce microservices.

Stack:

Backend: Laravel 12 (PHP 8.3+)

Database: MySQL / MariaDB

Frontend: Laravel Blade + Tailwind CSS v4

Email & Queues: SMTP dispatched asynchronously via Laravel Queued Jobs

Timezone Rules:

All database timestamps (created_at, updated_at, deleted_at, edited_at) stored in UTC.

All user display timestamps converted to Asia/Kolkata (IST).

due_date is stored as Date only (YYYY-MM-DD), without time/timezone components. Overdue checks evaluate against current date in Asia/Kolkata.

2. Authorization & Roles Architecture

There are strictly three permission levels: User, Manager, and Admin.

[ User ]   ---> Global viewing; creates tickets; edits OWN ticket details; transitions any ticket to active/closed; reopens tickets assigned to them.
[ Manager] ---> User permissions + Project-level management for assigned projects (edit all ticket details, manage/delete tickets & comments, reopen any ticket in assigned projects).
[ Admin ]   ---> Unrestricted system access (user management, project assignments, global actions, soft-delete restores).


2.1 Project Manager Relationship

Managers are Project Managers mapped to projects via the project_manager pivot table.

A project can have multiple Managers. A user can manage multiple projects.

CRITICAL POLICY RULE: Authorization checks for project management MUST evaluate project context ($user->managesProject($ticket->project_id)). Do NOT rely solely on a global $user->isManager() check.

2.2 Permissions Matrix

Action

Creator

Assigned User

Other User

Manager (Assigned Project)

Manager (Other Project)

Admin

View any ticket / comment / history

✅

✅

✅

✅

✅

✅

Create ticket

✅

✅

✅

✅

✅

✅

Edit ticket details (title, description, priority, due date, project, assignment)

✅

❌

❌

✅

❌

✅

Transition active statuses (New ↔ In Progress ↔ Blocked)

✅

✅

✅

✅

✅

✅

Close ticket (Transition to Closed)

✅

✅

✅

✅

✅

✅

Reopen ticket (Transition from Closed to active status)

✅ (if assigned or creator)

✅

❌

✅

✅ (if assigned or creator)

✅

Add comment / Watch ticket

✅

✅

✅

✅

✅

✅

Edit comment (Strictly within 5-min window)

Author (≤ 5 min)

Author (≤ 5 min)

Author (≤ 5 min)

Author / PM (≤ 5 min)

Author (≤ 5 min)

Author / Admin (≤ 5 min)

Delete comment

Author (≤ 5 min)

Author (≤ 5 min)

Author (≤ 5 min)

✅

Author (≤ 5 min)

✅

Delete ticket (Soft delete)

❌

❌

❌

✅

❌

✅

Manage users / projects / SMTP

❌

❌

❌

❌

❌

✅

View / Restore soft-deleted items

❌

❌

❌

❌

❌

✅

Note on Precedence (2B): Ticket Creator permissions take priority over non-assigned Manager status. If a Manager of Project A creates a ticket in Project B, they retain full creator edit privileges on that ticket.

3. Data Schema & Model Specification

 [ users ]
    │
    ├── pivot: project_manager (user_id, project_id) [Compound primary key]
    ├── tickets (created_by_id, assigned_to_id, deleted_by_id)
    ├── comments (user_id, deleted_by_id)
    ├── ticket_watchers (user_id, ticket_id)
    ├── ticket_activity_logs (user_id, ticket_id)
    └── projects (deleted_by_id)

 [ projects ]
    │
    └── tickets (project_id)
         │
         ├── ticket_activity_logs (ticket_id, user_id)
         ├── comments (ticket_id)
         └── ticket_watchers (user_id, ticket_id)


3.1 Migration Specifications & Edge Case Mitigations

Global Scopes & Relations (3A):

Eloquent models representing relationships to Users and Projects (such as assignedTo, createdBy, deletedBy, and activity logs) must append ->withTrashed() where necessary to prevent runtime errors (Attempt to read property 'name' on null) when entities are soft-deleted.

Blade templates must utilize null-safe operators (e.g., $ticket->assignedTo?->name ?? 'Unassigned').

users:

id

name (string)

email (string, unique)

password (string)

role (enum: ['user', 'manager', 'admin'], default: 'user')

remember_token

timestamps

softDeletes

projects:

id

name (string)

description (text, nullable)

timestamps

softDeletes

deleted_by_id (foreignId -> users, nullable, nullOnDelete)

project_manager:

user_id (foreignId -> users, cascadeOnDelete)

project_id (foreignId -> projects, cascadeOnDelete)

timestamps

Primary Key / Unique constraint: $table->primary(['user_id', 'project_id']); (No surrogate id column required).

tickets:

id

title (string)

description (text)

project_id (foreignId -> projects, restrictOnDelete)

created_by_id (foreignId -> users, restrictOnDelete)

assigned_to_id (foreignId -> users, nullable, nullOnDelete)

status (enum: ['New', 'In Progress', 'Blocked', 'Closed'], default: 'New')

priority (enum: ['Low', 'Medium', 'High', 'Urgent'], default: 'Medium')

due_date (date, nullable)

timestamps

softDeletes

deleted_by_id (foreignId -> users, nullable, nullOnDelete)

comments:

id

ticket_id (foreignId -> tickets, cascadeOnDelete)

user_id (foreignId -> users, cascadeOnDelete)

body (text)

edited_at (timestamp, nullable)

timestamps

softDeletes

deleted_by_id (foreignId -> users, nullable, nullOnDelete)

ticket_watchers:

id

ticket_id (foreignId -> tickets, cascadeOnDelete)

user_id (foreignId -> users, cascadeOnDelete)

timestamps

Unique constraint: ['ticket_id', 'user_id']

ticket_activity_logs:

id

ticket_id (foreignId -> tickets, cascadeOnDelete)

user_id (foreignId -> users, cascadeOnDelete)

field_changed (string)

old_value (text, nullable)

new_value (text, nullable)

timestamps

4. Key Business Logic & Transaction Rules

4.1 Ticket Editing & Status Transitions

Ticket Metadata Editing:

Only the ticket creator, an assigned Project Manager for that ticket's project, or an Admin can edit ticket details (title, description, priority, due date, project, assignee). Regular users cannot edit other users' ticket details (subject to creator precedence override).

Status Transitions:

Any user can transition any ticket among active statuses (New, In Progress, Blocked) or move it to Closed.

Closing a Ticket: Any user can close any ticket at any time.

Reopening a Ticket (Exiting Closed - 2A): Admins, assigned Project Managers, the ticket's assigned user (assigned_to_id), and the original ticket creator can move a ticket out of Closed.

Atomic Blocked Rule: When transitioning a ticket status to Blocked, the user must submit an accompanying comment explaining why. This update and comment creation must execute inside a single DB::transaction() block. If comment creation fails, the status update rolls back.

Unblocking Rule: Transitioning a ticket out of Blocked requires no mandatory comment or reason.

4.2 Comment Rules & 5-Minute Window

Any user can post comments on any ticket.

5-Minute Edit/Delete Window:

A comment can only be edited within 5 minutes of its created_at timestamp (now()->diffInMinutes($comment->created_at) < 5). Backend CommentPolicy must strictly validate this rule based on server execution time.

Editable within 5 minutes by: comment author, assigned Project Manager, or Admin.

After 5 minutes, comment editing is permanently locked.

Comment deletion within 5 minutes is permitted for the author. Assigned Project Managers and Admins can delete comments at any time.

4.3 Activity Logging

Every change to a ticket's tracked fields (status, assigned_to_id, priority, due_date, project_id) must automatically generate a record in ticket_activity_logs.

Audit log relationships must use ->withTrashed() so historical changes safely render names even if assignees or users are soft-deleted.

4.4 Notifications & Email Queue Rules

Email notifications for ticket updates, comments, and assignments must be dispatched asynchronously via queued Jobs (ShouldQueue).

Self-Notification Exclusion (3B): The user who performs an action must never receive an automated email notification for their own action. Query implementation for watchers/participants must explicitly exclude the triggering user:

$recipients = $ticket->watchers()
    ->where('users.id', '!=', auth()->id())
    ->get();


Jobs and listeners must specify $afterCommit = true (or dispatch post-commit) so notifications are not dispatched for rolled-back database transactions.

5. Implementation Roadmap & Milestones

Milestone 1: Database & Models

[ ] Run migrations for all 7 tables with compound keys, foreign key constraints, indexes, and soft deletes.

[ ] Implement Eloquent models and relationships (User, Project, Ticket, Comment, TicketWatcher, TicketActivityLog) with withTrashed() scopes for audit trails.

[ ] Create Model Seeders (DatabaseSeeder, RoleSeeder, SampleDataSeeder).

Milestone 2: Authentication & Authorization Policies

[ ] Setup authentication (Laravel Breeze / Blade stack).

[ ] Implement TicketPolicy:

update (creator, assigned PM, admin)

transition (any user for active/close; assigned user, creator, assigned PM, admin for reopen)

delete (assigned PM, admin)

[ ] Implement CommentPolicy:

update (author, assigned PM, admin within 5 minutes of creation)

delete (author within 5 minutes, assigned PM, admin)

[ ] Implement ProjectPolicy (admin only for CRUD).

Milestone 3: Controllers & Validation Logic

[ ] Create Form Requests (StoreTicketRequest, UpdateTicketRequest, TransitionTicketStatusRequest, StoreCommentRequest, UpdateCommentRequest).

[ ] Implement TicketController (CRUD, status transitions, blocking validation + atomic transaction).

[ ] Implement CommentController (with 5-minute constraint check) and WatcherController.

[ ] Attach TicketActivityLog listeners/observers on ticket changes.

Milestone 4: Notifications & Queues

[ ] Set up queue worker configuration (QUEUE_CONNECTION=database or redis).

[ ] Implement Mailables and Queued Jobs ($afterCommit = true) for ticket event notifications with self-notification exclusion queries.

Milestone 5: Blade UI & Timezone Formatting

[ ] Create layout and navigation with Tailwind CSS v4.

[ ] Implement Ticket Dashboard with filtering (Status, Project, Assignee, Workload).

[ ] Implement Ticket Detail view with Activity History, Watcher toggle, and Comments thread (with inline edit countdown or 5-min gate and null-safe user attributes).

[ ] Implement Asia/Kolkata date/time formatting helper/accessor.

6. QA Acceptance Criteria & Edge Case Rules

Contextual Manager Rule: A user who is a Manager for Project A cannot delete comments, delete tickets, or edit non-created ticket details in Project B unless explicitly assigned as a Manager for Project B.

Reopen Permission Guard: A ticket in Closed status can only be reopened by its current assignee (assigned_to_id), its original creator, an assigned Project Manager, or an Admin. Other users attempting to transition from Closed receive an authorization 403.

Closing Permission Check: Any authenticated user can transition any ticket from an active status (New, In Progress, Blocked) to Closed.

Edit Own Ticket Only / Precedence: A regular user can only update metadata on tickets they created. Creator permissions take priority over non-assigned Manager status.

Blocked Status Guard: Submitting a status change to Blocked without an accompanying comment must fail validation and perform no DB modifications.

Unblocking Guard: Transitioning out of Blocked to In Progress or Closed requires no mandatory comment.

Comment 5-Minute Gate: Attempting to update a comment at 5 minutes and 1 second after created_at must be rejected with an authorization or validation error.

Timezone Accuracy: Database stores UTC timestamps; UI displays 04 Oct 2026, 05:30 PM IST. due_date displays strictly as YYYY-MM-DD. Overdue checks evaluate against IST date.

Deployment Cache Safety: Post deployment scripts must run php artisan queue:restart to flush stale job workers.

7. AI Assistant Prompt Template

Copy and paste this prompt when starting a new session with an AI coding assistant:

"I am working on a Laravel project following the specification in PROJECT_SPEC.md. We are currently on [Milestone X, Task Y]. Please act as my senior architect and coding mentor. Guide me through the implementation details and concepts so I can write the code myself. Adhere strictly to the project rules and policies."
