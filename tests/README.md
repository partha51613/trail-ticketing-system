# Test Suite Guide — Team Ticket Tracker

This directory contains the automated test suites for the **Team Ticket Tracker** application, written using [Pest PHP](https://pestphp.com/).

---

## Running the Tests

To run the QA test suite covering the implemented Form Requests and Authorization Policies:

```bash
vendor/bin/pest tests/Unit/QaSuiteTest.php
```

Or using Artisan:

```bash
php artisan test tests/Unit/QaSuiteTest.php
```

---

## Test Suites Overview

### 1. `tests/Unit/QaSuiteTest.php`
A standalone, in-memory QA test suite that validates the core business rules without requiring an active database connection:

* **Form Requests Validation (Subtask 3.1):**
  * `StoreTicketRequest`: Checks required fields (`title`, `description`, `project_id`), priority enum (`low`, `medium`, `high`, `urgent`), and date formats.
  * `TransitionTicketStatusRequest`: Enforces the **Atomic Blocked Rule** (a comment is required if and only if status is `blocked`) and verifies custom error messaging.
  * `StoreCommentRequest` & `UpdateCommentRequest`: Verifies `body` is required.

* **Authorization Policies (Milestone 2):**
  * `TicketPolicy::update`: Verifies creators can edit open tickets, non-creators are blocked, and **closed tickets are locked** against metadata updates for everyone (including admins).
  * `TicketPolicy::reassign`: Verifies assigned users can reassign or unassign tickets.
  * `TicketPolicy::transition`: Verifies reopening guards (only creator, assignee, PM, or admin can reopen a closed ticket).
  * `CommentPolicy::update`: Enforces the **5-minute server-side edit window** for regular authors while allowing Admins to edit anytime.
  * `ProjectPolicy`: Verifies only Admins can create and manage projects.

---

## Code Quality & Formatting

Before finalizing any changes, format your code according to Laravel Pint:

```bash
vendor/bin/pint --dirty --format agent
```

