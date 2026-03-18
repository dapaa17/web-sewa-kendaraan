# Project Guidelines

## Code Style

- Follow `.editorconfig`: UTF-8, LF line endings, spaces with 4-space indentation, and 2 spaces for YAML.
- Keep changes minimal and consistent with existing Laravel 12, Blade, and Tailwind patterns.
- Use Indonesian for user-facing copy, status labels, validation messages, and email content unless the surrounding feature already uses English.

## Architecture

- This project is a Laravel 12 vehicle-rental app centered on `Booking`, `Vehicle`, `User`, and `Review` models.
- Customer and admin flows share controllers, but admin routes live under the `admin.` name prefix and `/admin` URL prefix in `routes/web.php`, protected by `auth` and `admin` middleware.
- Reuse domain logic from models and policies instead of duplicating raw state checks in controllers or Blade views. Booking state depends on both `status` and `payment_status`, plus time-sensitive helpers for `pickup_time`.
- Shared admin sidebar counters are composed in `app/Providers/AppServiceProvider.php`. Shared visual theme tokens live in `resources/views/layouts/app.blade.php` and some pages rely on `@section('css')`, so keep `@yield('css')` intact.

## Build and Test

- See `README.md` for full local setup and environment variables.
- Main development command: `composer run dev`.
- Run `php artisan schedule:work` in a separate terminal when working on unpaid booking cancellation, rental status sync, or booking reminder flows.
- Run `php artisan queue:listen --tries=1 --timeout=0` when working on mail or queued notifications if `composer run dev` is not already running.
- Use `php artisan test` or focused feature tests in `tests/Feature` for verification. Use `composer test` when you also want the config cache cleared first.
- Upload-related features require `php artisan storage:link`.

## Conventions

- Prefer existing Booking helpers such as `canBeCancelled()`, `canEnterPaymentFlow()`, `canUploadPaymentProof()`, `canBeVerified()`, `canBeCompleted()`, `getPaymentDeadline()`, `isPastDeadline()`, and `isExpiringSoon()` instead of duplicating payment-flow rules.
- Preserve shared availability logic through Booking scopes and vehicle helpers. New availability work should align with `overlappingRange`, `blockingAvailability`, `queueableAvailability`, and the browse/availability APIs so customer browse results and booking validation stay in sync.
- Time-aware booking behavior must respect both date and stored schedule time. Do not treat same-day bookings as started or finished from the date alone.
- Maintenance follow-up uses `maintenance_hold_at` and `maintenance_hold_reason` on bookings. Maintenance-held bookings surface in dedicated admin flows and admin rescheduling should preserve payment while clearing the hold.
- Return inspection data is stored directly on bookings. Changes to completion logic must keep maintenance escalation and downstream booking activation behavior aligned.
- Review flow is policy-driven. Use `Booking::canBeReviewed()`, `ReviewPolicy`, and the `review-submission` rate limiter instead of open-coded review eligibility checks.
- Tests run against SQLite by default. Guard MySQL-only schema operations so migrations remain SQLite-safe, and prefer `UploadedFile::fake()->create(..., 'image/jpeg')` over GD-dependent fake image helpers in tests.

## Key Reference Files

- `app/Models/Booking.php` for booking state, payment windows, availability scopes, and time-aware helpers.
- `routes/web.php` for customer/admin route structure and route naming conventions.
- `app/Providers/AppServiceProvider.php` for review throttling and shared admin view data.
- `README.md` for setup, scheduler commands, demo accounts, and payment environment variables.