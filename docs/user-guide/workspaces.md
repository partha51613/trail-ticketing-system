# Workspaces & Projects

Workspaces (internally called **Projects**) group related tickets, milestones, and team members under a single umbrella with a unique short code.

---

## 1. Project Anatomy

Every project has:
- **Project Name:** The human-readable title (e.g., `Customer Portal`).
- **Project Code:** A unique 2 to 6 character alphanumeric prefix (e.g., `CP`). Every ticket created in this workspace automatically inherits this prefix (e.g., `CP-001`, `CP-002`).
- **Description:** Scope, objectives, or helpful documentation links.
- **Assigned Managers:** Team leaders with administrative authority over tickets and settings within this workspace.

---

## 2. Workspace Lifecycle (Active vs. Closed)

```
       +--------------------+
       |  Active Workspace  | <---- Normal Operation (Create/Edit Tickets & Comments)
       +--------------------+
                 |
        [Close Project] (Admin / Manager)
                 v
       +--------------------+
       |  Closed Workspace  | <---- Read-Only Mode (Auditing & Reference)
       +--------------------+
                 |
        [Reopen Project]
                 v
       +--------------------+
       |  Active Workspace  |
       +--------------------+
```

### Active Workspaces
- Users can create, edit, assign, and transition tickets.
- Team members can post comments and upload attachments.

### Closed Workspaces (Read-Only Mode)
When a project reaches completion or is archived, an Admin or assigned Manager can toggle it to **Closed**:
- All tickets within the workspace become **read-only**.
- New ticket creation is disabled.
- Commenting and status changes are locked for audit preservation.
- A closed project can be reopened at any time with one click.

---

## 3. Project KPIs & Velocity

The project detail view (`/projects/{id}`) includes a real-time health indicator bar:
- **Total Tickets:** All non-deleted issues opened under the workspace.
- **Assigned vs. Unassigned:** Tracks unallocated work needing triage.
- **Blocked:** Tickets marked as blocked needing managerial intervention.
- **Closed:** Finished tickets contributing toward completion velocity.

---

## 4. Best Practices for Managers
> **Tip:** Keep project codes short and recognizable (e.g., `API`, `WEB`, `IOS`). This makes referencing tickets in Slack, Git commit messages (`git commit -m "API-014: resolve timeout"`), and meetings effortless.
