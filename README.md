# Trail Ticketing System

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS%20v4-38B2AC.svg)](https://tailwindcss.com)
[![Tests Passing](https://img.shields.io/badge/Tests-56%20Passed-brightgreen.svg)](tests/)

**Trail** is a modern, lightweight, and audit-focused issue and ticket tracking system built with **Laravel 12**, **Blade**, and **Tailwind CSS**. Designed for teams needing clear project scoping, full activity history, file attachments, team workload distribution, and clean email notifications—without the bloat of complex enterprise suites.

---

## ✨ Features

- **Project & Ticket Lifecycle Management:**
  - Multi-project scoping with unique reference codes (e.g., `APP-001`, `DES-002`).
  - Ticket priority, due dates with timezone support (`Asia/Kolkata`), and overdue indicators.
  - State machine transitions (`new` ➔ `in_progress` ➔ `blocked` ➔ `resolved` ➔ `closed`).
  - Atomic blocked safeguards: transitioning a ticket to `blocked` requires a mandatory reason logged immediately as a comment.
- **Closed Project Safeguards:**
  - Archiving/closing a project automatically locks ticket mutations, assignments, and new comments.
- **Attachments with Auto-Thumbnails:**
  - Upload images and documents directly to tickets and comments.
  - Automatic thumbnail generation for image files with secure streaming.
- **Audit Trail & Activity Logging:**
  - Complete history tracking every field changed (status, priority, assignee, due date, description) with actor timestamps.
- **Workload Analytics & Team Distribution:**
  - Dedicated Workload Overview showing active, overdue, and completed ticket distributions per team member.
- **Smart Notification Engine:**
  - **Assignment alerts:** Direct notifications to newly assigned members.
  - **Status & Comment updates:** Broadcasted to ticket creators and watchers while intelligently suppressing notification emails to the action author.
- **Role-Based Access Control:**
  - Roles: `Admin`, `Manager`, and `User`.
  - Fine-grained policies controlling ticket edits, project CRUD, and comment time windows (5-minute edit window).

---

## 🛠️ Tech Stack & Requirements

- **PHP:** 8.2 or 8.3+
- **Laravel Framework:** 12.x
- **Database:** MariaDB, MySQL, SQLite, or PostgreSQL
- **Frontend:** Laravel Blade, Alpine.js, Tailwind CSS v4, Vite
- **Testing:** Pest PHP (56 Feature & Unit tests)

---

## 🚀 Quickstart & Installation

### 1. Clone the repository
```bash
git clone git@github.com:partha51613/trail-ticketing-system.git
cd trail
```

### 2. Install PHP & Node dependencies
```bash
composer install
npm install
```

### 3. Environment configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database and mail settings in `.env`:
```env
APP_NAME=Trail

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trail
DB_USERNAME=your_username
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
MAIL_MAILER=smtp
```

### 4. Run Migrations & Seeders
```bash
php artisan migrate --seed
```
This generates default roles, permissions, sample projects, tickets, and default users.

### 5. Build assets & create storage symlink
```bash
php artisan storage:link
npm run build
```

### 6. Run the local development server
```bash
php artisan serve
```
And in a separate terminal (for queued notifications and emails):
```bash
php artisan queue:work
```

---

## 🧪 Running Tests

The application has comprehensive automated test coverage covering authorization policies, closed-project safeguards, file attachments, and notification flows:

```bash
php artisan test
# Or with Pest directly
vendor/bin/pest
```

---

## 👥 Default Demo Credentials

When seeded with `php artisan db:seed`, Trail provides the following demo accounts (default password: `password`):

| Role | Email | Capabilities |
|---|---|---|
| **System Admin** | `admin@example.com` | Full project & ticket management, user assignment, global restore |
| **Project Manager** | `manager1@example.com` | Project oversight, reassignment, ticket transitions |
| **Team Member** | `user1@example.com` | Create tickets, comment, attach files, watch tickets |

---

## 🤝 Contributing

Contributions are welcome! If you'd like to help improve Trail:
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Ensure all tests pass (`vendor/bin/pest` and `vendor/bin/pint --test`)
5. Push to the Branch (`git push origin feature/AmazingFeature`)
6. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See [`LICENSE`](LICENSE) for more information.
