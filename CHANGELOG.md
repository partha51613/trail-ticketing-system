# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- **UI Component System**:
  - `<x-ticket.status-badge>` for unified, accessible status pill rendering across light and dark modes.
  - `<x-ticket.priority-badge>` for consistent priority indicators (Urgent, High, Medium, Low).
  - `<x-ticket.segmented-tabs>` supporting Active, Closed, and Deleted ticket filtering with live record counters.
  - `<x-ui.avatar>` for normalized initials and user avatar representations.
  - `<x-ui.kpi-card>` for standardized dashboard and workspace metric cards.
  - `<x-table.th>` for uniform table header styling with optional Alpine.js column sorting triggers.
  - `<x-file-upload-field>` consolidating client-side drag-and-drop validation, size limits (up to 50MB per file), and multi-file removals.
  - `<x-timeline.comment-card>` and `<x-timeline.activity-node>` for clear separation between user discussions and automated system audit trails.
- **Dedicated Alpine.js Store Modules**:
  - `fileCollector.js` managing file staging, drag-and-drop states, and validation alerts.
  - `ticketFilter.js` managing synchronized URL filter updates and tab state transitions.
  - `attentionTicket.js` driving the dashboard actionable attention drawer.
- **Comprehensive User Guide**:
  - Added full end-user documentation suite in `docs/` (`USER_MANUAL.md`, `user-guide/index.md`, `workspaces.md`, `tickets.md`, `collaboration.md`, and `search-and-filter.md`).

### Changed
- **Blade View Decomposition**:
  - Refactored `resources/views/tickets/index.blade.php` (~305 lines down to 44 lines) by extracting:
    - `tickets/partials/filter-toolbar.blade.php`: Segmented tabs, search, project/status/priority/assignee filters.
    - `tickets/partials/table.blade.php`: Ticket data table with `<x-table.th>` components and restore actions.
  - Refactored `resources/views/tickets/show.blade.php` (~1,060 lines down to 37 lines) into dedicated partials:
    - `header.blade.php`, `description.blade.php`, `timeline.blade.php`, `sidebar.blade.php`, and `lightbox.blade.php`.
  - Refactored `resources/views/projects/show.blade.php` (~582 lines down to 18 lines) into dedicated partials:
    - `header.blade.php`, `details-card.blade.php`, `kpi-row.blade.php`, and `tickets-table.blade.php`.
- **Backend Architecture & Query Optimization**:
  - Introduced Eloquent query scopes on `App\Models\Ticket` (`scopeSearch`, `scopeForTab`, `scopeFilterBy`).
  - Extracted project validation rules into dedicated Form Request classes: `StoreProjectRequest` and `UpdateProjectRequest`.
  - Streamlined `TicketController::index` and `ProjectController::show` to eliminate redundant query boilerplate.
- **Tailwind Palette & Dark Mode**:
  - Added dedicated dark mode slate shades (`slate-750`, `slate-850`) and high-contrast `brand-950` badge accents in `tailwind.config.js`.

---

## [1.0.1] - 2026-03-22

### Added
- Docker containerization support (`Dockerfile` and `docker-compose.yml`) for seamless local development and deployment.
- Enforced mandatory project codes for clear ticket referencing prefixes.
- Weekly workload interactive statistics with Chart.js visualizations and date dropdown filters.
- System-wide Dark Mode theme toggle with persistent user preferences.
- Redesigned password reset and forgot password authentication views.
- Comprehensive database seeders (`TicketSeeder`) for realistic test environments.

### Fixed
- Increased maximum file attachment size limit to 50MB.
- Eliminated screen flicker when navigating between pagination pages.
- Fixed HTTP 419 CSRF session expiration edge case during form interactions.
- Resolved crash scenario when processing nested arrays in request inputs.
- Hardened unauthenticated redirects to point cleanly to the login view.
- Added custom favicon and updated developer setup guides in `README.md`.

---

## [1.0.0] - 2026-03-10

### Added
- **Core Ticketing Engine**:
  - Full Ticket CRUD (Title, Description, Status, Priority, Assignee, Due Date).
  - Unique auto-incrementing reference codes with project prefixes (e.g. `PRJ-101`).
  - Strict status workflow with transition guards (Mandatory resolution comments required when transitioning to `blocked`).
  - Active, Closed, and Soft-Deleted ticket views with self-service Restore capability for authorized users.
- **Collaboration & Auditing**:
  - Rich comment threads with file attachments (up to 10 files per comment).
  - Built-in modal attachment image lightbox preview.
  - Automated activity logging tracking title, status, priority, and assignment changes.
  - Ticket watcher subscription mechanism with in-app notifications.
- **Project Workspaces**:
  - Project management with status tracking (Active, In Progress, Closed).
  - Member assignment and workspace-level metric summaries.
- **Role-Based Access Control**:
  - Fine-grained Laravel Authorization Policies (`TicketPolicy`, `ProjectPolicy`, `CommentPolicy`).
- **Dashboard & Workload Analytics**:
  - Structural column view of team tickets by status.
  - Overdue ticket detection tags with automated date boundary comparisons.
