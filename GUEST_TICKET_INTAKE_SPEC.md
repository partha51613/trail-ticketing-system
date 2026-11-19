# Architecture & Specification: Public Guest Ticket Intake (No-Email Environment)

> [!WARNING]
> **Temporary Planning Document:** This file is created on the `add/guest-ticket-intake` branch for review. It will be deleted before this branch is finalized or merged into `dev`/`main`.

---

## 1. Feature Overview & Goals

- **Objective:** Allow an administrator to enable a public URL on a specific project (e.g. `https://your-domain.com/p/PRJ-CODE/ask`) and share it on a public community forum.
- **Audience:** Forum guests who do not have staff accounts and cannot be forced to register.
- **Hosting Constraint:** The server **cannot send outgoing emails** (no SMTP or external mail provider available).
- **Core Requirement:** Guests must receive a ticket reference number and be able to view responses, reply, and add attachments without any risk of other forum members guessing or accessing their ticket.

---

## 2. Access & Authentication Model

### A. Primary Access: Cryptographic Secret Access Token (Bearer URL)
1. When a guest submits a question, the server generates:
   - A sequential reference code (e.g., `PRJ-104`).
   - A 256-bit entropy random token (`Str::random(64)`), stored as `guest_access_token` in `tickets`.
2. The user is redirected to their private URL:
   `https://your-domain.com/desk/ticket/PRJ-104?token=wK8x9pL2vM4q...`
3. Because the 64-character token cannot be brute-forced (over $62^{64}$ combinations), knowing `PRJ-104` grants **zero** access to unauthorized humans or web crawlers.

### B. Fallback Access: Dual-Credential Rate-Limited Lookup
Because no email can be sent if the user closes their browser without saving the link:
- A lookup screen at `/desk/lookup` allows recovery **only** if the user provides **both**:
  1. `Ticket Reference` (e.g. `PRJ-104`)
  2. `Email Address` entered during submission.
- **Strict Rate Limiting:** Enforce a maximum of 5 attempts per 15 minutes per IP via Laravel's `RateLimiter`.
- **Anti-Enumeration:** Generic response for failures (*"No matching ticket found for this code and email combination"*). Never disclose whether the email or the ticket code exists independently.

### C. Client-Side Persistence
- Save the guest's recent ticket tokens in browser `localStorage` on form submission.
- Returning to `/p/{code}/ask` automatically displays a *"Your Recent Inquiries on this Device"* drawer.

---

## 3. Database Schema Changes (5 Tables Total)

To prevent database integrity errors and null pointer exceptions across the entire lifecycle (ticket $\to$ comments $\to$ uploads $\to$ activity logs):

### 1. `projects`
- `is_public` (`boolean`, default: `false`) — Per-project toggle. Only projects with `is_public = true` expose a public intake URL.
- `public_title` (`string`, nullable) — Friendly heading (e.g., *"Developer Community Helpdesk"*).
- `public_description` (`text`, nullable) — Optional instructions or FAQ links shown above the form.

### 2. `tickets`
- `created_by_id` (`foreignId`, nullable) — Made nullable so guest tickets don't require dummy user records.
- `guest_name` (`string`, nullable) — The name provided by the guest.
- `guest_email` (`string`, nullable) — The email provided by the guest (used for the dual-lookup fallback).
- `guest_access_token` (`string`, 64 chars, nullable, indexed) — Secret access token.

### 3. `comments`
- `user_id` (`foreignId`, nullable) — Made nullable so guests can post replies without an authenticated user account.
- `is_guest` (`boolean`, default: `false`) — Identifies whether the comment author is a guest.

### 4. `attachments`
- `user_id` (`foreignId`, nullable) — Made nullable so attachments uploaded by guests do not trigger foreign key constraint violations.

### 5. `ticket_activity_logs`
- `user_id` (`foreignId`, nullable) — Made nullable so actions taken by guests can be logged without throwing foreign key errors.

---

## 4. Edge Cases, Risks & Mitigations

### 1. Spam Bots & Flooding
- **Risk:** Public URLs posted in forums are crawled by spam bots submitting advertisements or phishing links.
- **Mitigation:**
  - Invisible Honeypot field in the Blade form.
  - IP-based rate limiting on submission (`throttle:3,10` — max 3 tickets per 10 mins).
  - Minimum description character length (e.g. at least 20 characters).

### 2. Storage DOS / Attachment Abuse
- **Risk:** Malicious users uploading large 50MB files repeatedly to exhaust shared server storage.
- **Mitigation:**
  - Restrict guest uploads to **max 2 files** (internal users have up to 10).
  - Cap guest file size to **5MB or 10MB** per file (internal users have 50MB).
  - Whitelist file extensions: `jpg, jpeg, png, webp, pdf, txt, zip, log`. Reject executables (`.exe, .sh, .php, .js`).

### 3. Secret Token Leakage via HTTP Referrer
- **Risk:** If staff posts an external link in a comment (e.g. `https://external-docs.com`), clicking it causes the external web server to see the full secret URL in the `Referer` header.
- **Mitigation:**
  - Send the header `Referrer-Policy: no-referrer` on all guest desk routes.
  - Include `<meta name="referrer" content="no-referrer">` in the guest layout.
  - Include `X-Robots-Tag: noindex, nofollow, noarchive` to keep search engines from indexing tickets.

### 4. Codebase Null-Safety Fixes Required
- **`TicketObserver`:** Guard against null `created_by_id` during watcher sync.
- **`AttachmentService::storeAttachment`:** Change `$userId` type from `int` to `?int = null`.
- **`CommentController` / Views:** Update references from `$ticket->creator->name` to safe navigation:
  `$ticket->creator?->name ?? $ticket->guest_name ?? __('Guest')`.

### 5. Role & Permission Boundaries
- **Guests Cannot:**
  - Change ticket status (`new`, `in_progress`, `closed`, `blocked`).
  - Change priority, assignee, or due dates.
  - Delete tickets or comments.
  - View internal staff-only notes (if added).
- **Guests Can:**
  - View their ticket, staff replies, and attachments.
  - Post comments and upload reply attachments.
  - Optionally click *"Mark as Resolved"* to close their own inquiry.

### 6. Closed / Trashed Projects
- If an admin closes or deletes the project, public submission routes return a `404` or a read-only message: *"This project is no longer accepting new inquiries."*

---

## 5. Proposed Route Architecture

```text
# Public Guest Intake (Unauthenticated)
GET  /p/{project:code}/ask                     -> PublicTicketController@create
POST /p/{project:code}/ask                     -> PublicTicketController@store

# Guest Ticket Desk & Tracking
GET  /desk/ticket/{ticket:reference_code}      -> GuestDeskController@show (validates ?token=...)
POST /desk/ticket/{ticket:reference_code}/reply-> GuestDeskController@reply

# Dual-Credential Recovery (No-Email Environment)
GET  /desk/lookup                              -> GuestDeskController@lookupForm
POST /desk/lookup                              -> GuestDeskController@lookupSubmit (rate-limited)
```

---

## 6. Cleanup Reminder Before Merging
- [ ] Implement and test feature on `add/guest-ticket-intake`.
- [ ] Review documentation against implemented code.
- [ ] **Delete this file (`GUEST_TICKET_INTAKE_SPEC.md`) before merging into `dev` or `main`.**
