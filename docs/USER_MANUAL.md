# Trail Ticketing System — Complete User Manual
*Production-Ready Documentation & Operational Guide*

---

## Table of Contents
1. [Introduction & Quickstart](#1-introduction--quickstart)
2. [User Roles & Permissions](#2-user-roles--permissions)
3. [Workspaces (Projects)](#3-workspaces-projects)
4. [Ticket Lifecycle & Workflows](#4-ticket-lifecycle--workflows)
5. [Collaboration, Comments & Attachments](#5-collaboration-comments--attachments)
6. [Search, Filtering & Saved Views](#6-search-filtering--saved-views)
7. [Frequently Asked Questions (FAQ)](#7-frequently-asked-questions-faq)

---

## 1. Introduction & Quickstart

**Trail** is a fast, modern issue tracking and workspace collaboration system. It provides teams with a frictionless workflow for capturing bugs, managing sprints, organizing tasks, and auditing changes.

### Key Concepts
- **Workspace (Project):** A designated workspace with a unique code prefix (e.g. `APP`, `WEB`).
- **Ticket:** An individual task, bug report, or feature ticket (e.g. `APP-042`).
- **Timeline:** A unified chronological stream recording team comments and system audit logs.

---

## 2. User Roles & Permissions

| Feature | Administrator | Project Manager | Developer / Contributor |
| :--- | :---: | :---: | :---: |
| Browse & Search Tickets | ✅ | ✅ | ✅ |
| Create & Comment on Tickets | ✅ | ✅ | ✅ |
| Upload Files (10MB limit) | ✅ | ✅ | ✅ |
| Watch / Unwatch Tickets | ✅ | ✅ | ✅ |
| Reassign Ticket Assignee | ✅ | ✅ | Restricted |
| Change Ticket Priority | ✅ | ✅ | Restricted |
| Create / Edit / Close Projects | ✅ | ✅ | ❌ |
| Soft-Delete & Restore Tickets | ✅ | ✅ (assigned workspace) | ❌ |

---

## 3. Workspaces (Projects)

Workspaces group tickets by product, team, or repository.

### Project Code Prefix
Every project is assigned a unique 2 to 6 character code (e.g., `MKT`). When issues are filed within that workspace, they automatically generate readable keys (e.g., `MKT-001`, `MKT-002`).

### Active vs. Closed Workspaces
- **Active:** Normal daily operations (creating, editing, and transitioning issues).
- **Closed (Read-Only):** When a milestone or project concludes, managers can close the workspace. All tickets become immutable read-only records for compliance and audit preservation. A closed project can be reopened at any time.

---

## 4. Ticket Lifecycle & Workflows

### Creating a Ticket
1. Click **"New Ticket"** in the top navigation.
2. Select your **Workspace** and enter an actionable **Title**.
3. Set an **Assignee**, **Priority**, and optional **Due Date**.
4. Provide context in the **Description** and attach any logs or screenshots.

### Priority Matrix
- **Urgent (Red):** Severe production blockers or outages. Needs immediate attention.
- **High (Rose/Amber):** Major feature blockers or sprint priorities.
- **Medium (Amber/Blue):** Standard sprint tasks and non-blocking bugs.
- **Low (Slate):** Backlog polish, documentation updates, or minor tweaks.

### Status Transitions
- **New:** Initial submission awaiting triage.
- **In Progress:** Actively underway by the designated assignee.
- **Waiting:** Work paused pending customer, client, or external review.
- **Blocked:** Impeded by an external dependency.  
  *(Note: Trail mandates a written reason explaining what is blocking the ticket before this state can be saved).*
- **Closed:** Work is completed, verified, and resolved.

---

## 5. Collaboration, Comments & Attachments

### Threaded Timeline
Each ticket page features a real-time timeline with convenient filter pills:
- **All:** Full historical playback (comments + system field changes).
- **Comments:** Discussion-only view.
- **History:** Pure audit trail showing who changed which field, old vs. new values, and timestamps.

### File Attachments & Lightbox
- **Supported Files:** Images (PNG, JPG, WebP, GIF), PDFs, Office docs, Text, CSV, Logs, and ZIP archives.
- **Capacity:** Up to 10 files per comment, max **10MB per file**.
- **Lightbox:** Click any thumbnail to open the zero-clutter image preview modal. Press <kbd>Esc</kbd> or click outside to dismiss.

---

## 6. Search, Filtering & Saved Views

### Global Search
Type directly into the filter bar:
- Search by reference key: `CP-015`
- Search by ticket ID: `15`
- Search by keyword in title or description: `database migration error`
- Search by author or assignee: `Sarah Connor`

### Segmented Tabs
Instantly toggle between:
- **Active Tickets:** Current open issues.
- **Closed Tickets:** Completed issues.
- **Deleted Tickets:** Soft-deleted issues preserved for audit.

---

## 7. Frequently Asked Questions (FAQ)

**Q: Can I restore a ticket if it was deleted by mistake?**  
A: Yes. Tickets in Trail are never permanently deleted by standard users. Go to the **Deleted** tab and click **Restore** to reactivate the issue with its entire history intact.

**Q: Why can't I mark a ticket as Blocked without entering a comment?**  
A: To prevent silent blockers, Trail enforces an atomic requirement: a blocker comment must be provided so the team knows exactly who or what is impeding progress.

**Q: What is the maximum attachment size?**  
A: Each file can be up to 10MB. You can attach up to 10 files in a single post.
