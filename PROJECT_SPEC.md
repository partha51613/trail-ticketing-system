# Team Ticket Tracker — Project Specification & Architecture Guide

> **Developer & AI Agent Note:** This document is the single source of truth for the Team Ticket Tracker application. Any code generation, refactoring, or feature development must strictly adhere to the standards, permission models, and business logic detailed below.

---

## 1. Executive Summary & Core Constraints

* **Purpose:** Internal work-tracking system for ~50 users across projects.
* **Core Principle:** Simple, readable, maintainable internal tool. **Do not turn this into a complex Jira clone or introduce microservices.**
* **Stack:**
  * **Backend:** Laravel (PHP 8.2+)
  * **Database:** MySQL / MariaDB
  * **Frontend:** Laravel Blade + Bootstrap
  * **Email & Queues:** SMTP dispatched asynchronously via Laravel Queued Jobs
* **Timezone Rules:**
  * All database timestamps (`created_at`, `updated_at`, `deleted_at`) stored in **UTC**.
  * All user display timestamps converted to **`Asia/Kolkata`** (`IST`).
  * `due_date` is stored as **Date only** (`YYYY-MM-DD`), without time/timezone components.

---

## 2. Authorization & Roles Architecture

There are strictly **three permission levels**: `User`, `Manager`, and `Admin`.

```
[ User ]   ---> Global viewing/editing, creates tickets, closes/reopens OWN tickets.
[ Manager] ---> User permissions + Project-level management (close/delete/manage tickets & comments in assigned projects).
[ Admin ]   ---> Unrestricted system access (user management, project assignments, global actions, soft-delete restores).
```

### 2.1 Project Manager Relationship
* Managers are **Project Managers** mapped to projects via the `project_manager` pivot table.
* A project can have multiple Managers. A user can manage multiple projects.
* **CRITICAL POLICY RULE:** Authorization checks MUST evaluate project context (`$user->managesProject($ticket->project_id)`). Do NOT rely solely on a global `$user->isManager()` check.

### 2.2 Permissions Matrix

| Action | User | Manager (Assigned Project) | Manager (Other Project) | Admin |
| :--- | :---: | :---: | :---: | :---: |
| View any ticket / comment / history | ✅ | ✅ | ✅ | ✅ |
| Create ticket | ✅ | ✅ | ✅ | ✅ |
| Edit ticket / Assign / Change status | ✅ | ✅ | ✅ | ✅ |
| Add comment / Watch ticket | ✅ | ✅ | ✅ | ✅ |
| **Close / Reopen ticket** | **Creator Only** | ✅ | **Creator Only** | ✅ |
| **Delete ticket** | ❌ | ✅ | ❌ | ✅ |
| **Delete comment** | ❌ | ✅ | ❌ | ✅ |
| Manage users/projects/SMTP | ❌ | ❌ | ❌ | ✅ |
| View / Restore soft-deleted items | ❌ | ❌ | ❌ | ✅ |

---

## 3. Data Schema & Model Specification

```
 [ users ]
    │
    ├── pivot: project_manager (user_id, project_id)
    ├── tickets (created_by_id, assigned_to_id)
    ├── comments (user_id)
    └── ticket_watchers (user_id, ticket_id)

 [ projects ]
    │
    └── tickets (project_id)
         │
         ├── ticket_activity_logs (ticket_id, user_id)
         ├── comments (ticket_id)
         └── ticket_watchers (user_id, ticket_id)
```

### 3.1 Migration Specifications

* **`users`**: `id`, `name`, `email` (unique), `password`, `role` (`enum: ['user', 'manager', 'admin']`), `remember_token`, `timestamps`, `softDeletes`.
* **`projects`**: `id`, `name`, `description` (nullable), `timestamps`, `softDeletes`.
* **`project_manager`**: `id`, `user_id` (foreignId -> users), `project_id` (foreignId -> projects), `timestamps`. *(Unique constraint on `[user_id, project_id]`)*.
* **`tickets`**: 
  * `id`, `title`, `description`
  * `project_id` (foreignId -> projects, restrictOnDelete)
  * `created_by_id` (foreignId -> users, restrictOnDelete)
  * `assigned_to_id` (foreignId -> users, nullOnDelete, nullable)
  * `status` (`enum: ['New', 'In Progress', 'Blocked', 'Closed']`, default: `'New'`)
  * `priority` (`enum: ['Low', 'Medium', 'High', 'Urgent']`, default: `'Medium'`)
  * `due_date` (`date`, nullable)
  * `timestamps`, `softDeletes`
  * `deleted_by_id` (foreignId -> users, nullOnDelete, nullable)
* **`comments`**: `id`, `ticket_id` (cascadeOnDelete), `user_id` (cascadeOnDelete), `body`, `timestamps`, `softDeletes`, `deleted_by_id` (nullable).
* **`ticket_watchers`**: `id`, `ticket_id` (cascadeOnDelete), `user_id` (cascadeOnDelete), `timestamps`. *(Unique constraint on `[ticket_id, user_id]`)*.
* **`ticket_activity_logs`**: `id`, `ticket_id` (cascadeOnDelete), `user_id` (cascadeOnDelete), `field_changed`, `old_value` (nullable), `new_value` (nullable), `timestamps`.

---

## 4. Key Business Logic & Transaction Rules

### 4.1 Status Transitions & The "Blocked" Rule
* Permitted statuses: `New`, `In Progress`, `Blocked`, `Closed`. (No `Resolved` status exists).
* **Transitions:** Any status can transition directly to any other status.
* **Atomic Blocked Rule:** When transitioning a ticket status to `Blocked`, the user **must** submit an accompanying comment explaining why.
* **DB Transaction Rule:** Updating status to `Blocked` and saving the required comment **must** execute inside a single `DB::transaction()` block. If comment creation fails, the status update must roll back.

### 4.2 Activity Logging
* Every change to a ticket's fields (`status`, `assigned_to_id`, `priority`, `due_date`, `project_id`) must automatically generate a record in `ticket_activity_logs`.
* Audit log rendering must safely render historical changes even if referenced entities (e.g., former assignee) are soft-deleted.

### 4.3 Notifications & Email Queue Rules
* Email notifications for ticket updates, comments, and assignments must be dispatched asynchronously via queued Jobs (`ShouldQueue`).
* **Self-Notification Exclusion:** The user who performs an action must **never** receive an automated email notification for their own action.
* Email dispatch must happen outside database transactions (or use `afterCommit = true`) to prevent sending emails for rolled-back operations.

---

## 5. Implementation Roadmap & Milestones

### Milestone 1: Database & Models
- [ ] Run migrations for all 7 tables.
- [ ] Implement Eloquent relationships (`belongsTo`, `hasMany`, `belongsToMany`).
- [ ] Create Model Seeders (`DatabaseSeeder`, `RoleSeeder`, `SampleDataSeeder`).

### Milestone 2: Authentication & Authorization Policies
- [ ] Install Laravel Breeze (Blade stack) / setup authentication.
- [ ] Implement `TicketPolicy` (enforcing project-context close/delete rules).
- [ ] Implement `CommentPolicy`.

### Milestone 3: Controllers & Validation Logic
- [ ] Create Form Requests (`StoreTicketRequest`, `UpdateTicketRequest`, `StoreCommentRequest`).
- [ ] Implement `TicketController` CRUD and status state transitions.
- [ ] Implement `CommentController` and `WatcherController`.
- [ ] Attach `TicketActivityLog` triggers on model update events.

### Milestone 4: Notifications & Queues
- [ ] Set up queue worker configuration (`QUEUE_CONNECTION=database` or `redis`).
- [ ] Implement Mailables and Queued Jobs for ticket event notifications.

### Milestone 5: Blade UI & Timezone Formatting
- [ ] Create primary Bootstrap layout and navigation.
- [ ] Implement Ticket Dashboard with filtering (Status, Project, Assignee, Workload).
- [ ] Implement Ticket Detail view with Activity History and Comments thread.
- [ ] Implement `Asia/Kolkata` date/time formatting helper/accessor.

---

## 6. QA Acceptance Criteria & Edge Case Rules

1. **Contextual Manager Rule:** A user who is a Manager for Project A **cannot** delete comments or close non-created tickets in Project B unless explicitly assigned as a Manager for Project B as well.
2. **Assignee Close Block:** A normal user assigned to a ticket created by someone else **cannot** close that ticket. Only the creator, project manager, or admin can close it.
3. **Blocked Status Guard:** Submitting a status change to `Blocked` without a comment body must trigger a validation failure and perform no DB modifications.
4. **Timezone Accuracy:** Database stores `2026-10-04 12:00:00` UTC; UI displays `04 Oct 2026, 05:30 PM IST`. `due_date` displays strictly as `YYYY-MM-DD`.
5. **Deployment Cache Safety:** Post FTP deployment scripts must run `php artisan queue:restart` to flush stale job workers.

---

## 7. AI Assistant Prompt Template

> Copy and paste this prompt when starting a new session with an AI coding assistant:
>
> *"I am working on a Laravel project following the specification in `docs/PROJECT_SPEC.md`. We are currently on **[Milestone X, Task Y]**. Please act as my senior architect and coding mentor. Guide me through the implementation details and concepts so I can write the code myself. Adhere strictly to the project rules and policies."*