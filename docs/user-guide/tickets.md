# The Ticket Lifecycle

Tickets are the fundamental units of work in Trail. Whether tracking a critical software bug, a feature specification, or an operational task, each ticket follows a strict, auditable lifecycle.

---

## 1. Creating a Ticket

Click **"New Ticket"** in the top navigation or from inside any project view:

1. **Project:** Select the target workspace (e.g., `Platform Infrastructure`).
2. **Title:** A concise, actionable headline (e.g., `Fix OAuth token expiry loop on Safari`).
3. **Assignee:** The team member responsible for delivering the issue (or leave *Unassigned* for triage).
4. **Priority:** The urgency tier (`Low`, `Medium`, `High`, or `Urgent`).
5. **Due Date:** Optional deadline (defaults to 11:59 PM on the selected date).
6. **Description:** Steps to reproduce, expected vs. actual behavior, or definition of done.
7. **Attachments:** Upload supporting logs, screenshots, or documents (up to 10 files, max 10MB each).

---

## 2. Priority Levels

| Priority | Color Badge | Recommended Response Time | Use Case |
| :--- | :---: | :---: | :--- |
| **Urgent** | 🔴 Red | Immediate (< 4 hours) | System outages, data corruption, severe blockers. |
| **High** | 🟠 Amber | Same day / Sprint priority | Major bugs without workarounds, core deadlines. |
| **Medium** | 🟡 Amber/Blue | Normal sprint cycle | Standard features, non-critical bugs. |
| **Low** | ⚪ Slate | Backlog / As time permits | Minor polish, typos, nice-to-have requests. |

---

## 3. Status Transitions

Trail enforces standard state transitions to maintain clean audit logs:

```
               +----------+
               |   New    |
               +----------+
                    |
                    v
             +-------------+
        +--> | In Progress | <----+
        |    +-------------+      |
        |       |        |        |
        |       v        v        |
        |   +-------+ +---------+ |
        +-- |Waiting| | Blocked |-+ (Requires explanation reason!)
            +-------+ +---------+
                |          |
                +----+-----+
                     v
               +----------+
               |  Closed  |
               +----------+
```

### The 5 Core Statuses:
1. **New:** Freshly submitted, awaiting triage or start of work.
2. **In Progress:** Actively being worked on by the assignee.
3. **Waiting:** Work paused pending feedback, third-party responses, or customer reply.
4. **Blocked:** Work is impeded by an external factor.  
   *(Trail strictly requires a mandatory comment explaining what is blocking the ticket before this state can be saved).*
5. **Closed:** Work is completed, verified, and resolved.

---

## 4. Deleting & Restoring Tickets (Soft Deletes)

- **Accidental Deletion Protection:** Deleted tickets are **soft-deleted** rather than permanently purged.
- **Audit Preservation:** Deleted tickets appear in the **Deleted** tab.
- **Restoration:** Admins and Project Managers can click **"Restore Ticket"** at any time to bring the issue, its history, attachments, and comments back into active state.
