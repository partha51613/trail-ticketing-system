# Trail Ticketing System — User Guide

Welcome to **Trail**, a streamlined, modern issue tracking and workspace collaboration system designed for engineering, product, and operations teams.

---

## 🚀 Quickstart (2-Minute Overview)

Trail organizes all work around two core concepts: **Workspaces (Projects)** and **Tickets**.

```
+--------------------------------------------------------------+
|                    Trail Workspace                           |
|                                                              |
|  [ Workspaces / Projects ] ---> [ Tickets & Issues ]         |
|  - Marketing Site (MKT)          - MKT-001: Fix Navbar       |
|  - Mobile App (APP)              - APP-042: Crash on login   |
|  - Infrastructure (INF)          - INF-012: Database upgrade |
+--------------------------------------------------------------+
```

### 1. Navigation at a Glance
- **Dashboard (`/dashboard`):** Your personal mission control. Shows tickets needing urgent attention, KPI metrics, and a live activity feed.
- **Tickets (`/tickets`):** The global register of all issues across the company. Easily switch between **Active**, **Closed**, and **Deleted** issues.
- **Projects (`/projects`):** Workspaces that group tickets, team managers, and project health indicators.
- **Workload (`/workload`):** Team capacity and assignment distribution.

### 2. User Roles & Capabilities
Trail uses clear role-based permissions:

| Capability | Admin | Project Manager | Developer / Member |
| :--- | :---: | :---: | :---: |
| Browse & Search Tickets | ✅ | ✅ | ✅ |
| Create New Tickets | ✅ | ✅ | ✅ |
| Comment & Attach Files | ✅ | ✅ | ✅ |
| Watch / Unwatch Tickets | ✅ | ✅ | ✅ |
| Reassign Tickets | ✅ | ✅ | Restricted |
| Change Ticket Priority | ✅ | ✅ | Restricted |
| Create / Edit Projects | ✅ | ✅ | ❌ |
| Soft-Delete / Restore Tickets | ✅ | ✅ (assigned project) | ❌ |

---

## 📚 Guide Sections

Explore the detailed guides below:

1. [**Workspaces & Projects**](./workspaces.md) — Managing workspaces, managers, and project status.
2. [**The Ticket Lifecycle**](./tickets.md) — Creating, assigning, priority levels, and status transitions.
3. [**Collaboration & Attachments**](./collaboration.md) — Discussion threads, mentions, and file uploads.
4. [**Search, Filters & Views**](./search-and-filter.md) — Finding tickets in seconds with filters and quick links.

---

## 💡 Quick Tips for Daily Productivity
- **Jump to a Ticket:** Type the ticket key (e.g., `ALP-001` or `PRJ-42`) directly into any search bar.
- **Press Esc:** Instantly close any open image lightbox or confirmation modal.
- **Dark Mode:** Trail automatically respects your system preference or toggles cleanly in your user profile.
