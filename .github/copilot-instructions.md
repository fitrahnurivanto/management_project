# 🤖 Copilot/AI Agent Instructions for management_project

## Project Overview
- **Framework:** Laravel (PHP backend), Vite + Tailwind (frontend)
- **Domain:** Project, order, and notification management for client/employee/admin roles
- **Key Directories:**
  - `app/Models/` — Eloquent models (User, Project, Order, Notification, etc.)
  - `app/Http/Controllers/` — Route logic, business flows
  - `resources/views/` — Blade templates (UI)
  - `routes/web.php` — Main HTTP routes
  - `config/` — App, mail, logging, and service configs
  - `tests/` — Feature and unit tests

## Architecture & Patterns
- **Notifications:**
  - Centralized in `app/Notifications/` (see `NOTIFICATION_IMPLEMENTATION.md`)
  - Use Eloquent relationships for notification targets (client, employee, admin)
  - Test notifications via `php test_notification.php` (menu-driven)
- **Google OAuth:**
  - Config and troubleshooting in `GOOGLE_OAUTH_CONFIG.md`
  - User creation/role assignment on first login; updates on repeat login
- **Production Readiness:**
  - Checklist and deployment steps in `PRODUCTION_READY_CHECKLIST.md`
  - Caching, queue, and error monitoring: see section E/F in checklist

## Developer Workflows
- **Build frontend:** `npm run build` (uses Vite)
- **Dev frontend:** `npm run dev`
- **Run tests:** `phpunit` or via `tests/` directory
- **Test notifications:** `php test_notification.php`
- **Test email:** `php test_email.php`
- **Clear/optimize cache:**
  - `php artisan cache:clear`
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
- **Queue worker:** `php artisan queue:work --daemon`

## Conventions & Integration
- **Eloquent ORM:** All DB access via models; avoid raw SQL except for migrations/seeders
- **Null safety:** All notification/view logic checks for nulls (see checklist D)
- **Mail:** Configured for Gmail SMTP; test with `test_email.php`
- **Logging:** Default to `storage/logs/laravel.log`; set `LOG_CHANNEL` in `.env`
- **Security:** CSRF, XSS, SQLi handled via Laravel defaults; see checklist H

## Troubleshooting & Testing
- **Google OAuth errors:** See `GOOGLE_OAUTH_CONFIG.md` (section: ERROR YANG MUNGKIN TERJADI)
- **Notification issues:** See `NOTIFICATION_IMPLEMENTATION.md` for manual and flow-based tests
- **Production issues:** See `PRODUCTION_READY_CHECKLIST.md` for error monitoring and deployment

## Examples
- **Send notification to client:**
  - `$client->notify(new ProjectCreatedNotification($project));`
- **Test notification system:**
  - `php test_notification.php` (choose scenario)
- **Check logs:**
  - `tail -f storage/logs/laravel.log`

---
For more, see: `NOTIFICATION_IMPLEMENTATION.md`, `GOOGLE_OAUTH_CONFIG.md`, `PRODUCTION_READY_CHECKLIST.md`.
