# Changelog

All notable changes to Trail Ticketing System will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.1.0] - 2026-11-18

> **Summary:** Major codebase refactoring and maintainability update. Introduces a reusable UI component library, decomposes monolithic Blade templates, streamlines query architecture with Eloquent scopes, and adds complete end-user documentation.

### 📚 Documentation
- **End-User Guide Suite**: Added comprehensive manuals in `docs/` (`USER_MANUAL.md`, `workspaces.md`, `tickets.md`, `collaboration.md`, `search-and-filter.md`) ready for self-hosting or static site export.

### ✨ Added
- **UI Component Library**: Created reusable Blade components for status badges, priority tags, user avatars, segmented tabs, and KPI cards with consistent light/dark mode styling.
- **Unified File Uploader**: Added a shared drag-and-drop file upload component (`<x-file-upload-field>`) with multi-file management and client-side 50MB file size checks.
- **Modular Frontend Stores**: Separated Alpine.js logic into dedicated stores (`fileCollector.js`, `ticketFilter.js`, `attentionTicket.js`).
- **Query Scopes & Form Requests**: Added reusable `Ticket` scopes (`search`, `forTab`, `filterBy`) and dedicated `StoreProjectRequest` / `UpdateProjectRequest` classes.

### ⚡ Changed
- **Blade Template Decomposition**: Drastically simplified core views into maintainable partials:
  - **Ticket Details (`tickets/show`):** Reduced from 1,060 lines to 37 lines.
  - **Project View (`projects/show`):** Reduced from 582 lines to 18 lines.
  - **Ticket Listing (`tickets/index`):** Reduced from 305 lines to 44 lines.
- **Dark Mode Refinements**: Added custom high-contrast color steps (`slate-750`, `slate-850`, `brand-950`) to improve legibility in dark mode.

---

## [1.0.1] - 2026-03-22

> **Summary:** Quality-of-life improvements, developer tooling, and UI refinements.

### ✨ Added
- **Docker Support**: Containerized local environment with `Dockerfile` and `docker-compose.yml`.
- **Workload Analytics**: Chart.js weekly team workload visualizations with dynamic date dropdown filters.
- **Dark Mode**: System-wide dark mode with instant toggle.
- **Sample Seeders**: Added `TicketSeeder` with realistic demo data.

### 🐛 Fixed
- Increased max attachment size limit to 50MB.
- Eliminated page flicker during pagination transitions.
- Fixed session expiration (HTTP 419) edge cases.
- Ensured mandatory project prefix codes on workspace creation.

---

## [1.0.0] - 2026-03-10

> **Summary:** Initial stable release of Trail Ticketing System.

### ✨ Highlights
- **Core Ticketing Engine**: Full ticket lifecycle management with auto-generated reference codes (e.g., `PRJ-101`), priority ratings, and due date tracking.
- **Workflow Guards**: Enforced mandatory explanation comments when tickets are transitioned to `Blocked`.
- **Project Workspaces**: Multi-project segregation with role-based team assignments and metrics.
- **Collaboration Tools**: Threaded comments, image/document attachments with lightbox previews, automated audit log timeline, and watcher subscriptions.
- **Security & RBAC**: Laravel authorization policies for granular permission enforcement.
