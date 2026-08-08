# Citinet Billing System

A customer-facing voucher sales automation platform: browse packages → pay via Paystack →
webhook verifies payment → voucher auto-assigned inside a DB transaction → customer sees/downloads it.

## ⚠️ Build environment note

This project was hand-written in a sandbox that **cannot reach Packagist**, so `composer install`
was never run and the app has **not been booted or tested**. You'll need to do that on your own
machine or server (both have real internet access). Treat this as a strong, structurally-correct
first draft — read through the code, especially `PaymentFulfillmentService` and
`VoucherAssignmentService`, before trusting it with real money.

## Audit history

This project has been through three audit passes since the initial build.

**Round 3 (customer-side + voucher delivery focus):**
- `SendVoucherEmail::dispatch(...)` now uses `->afterCommit()` — defensive against a data
  race if the queue connection is ever switched from 'database' to something external
  (Redis/SQS), where a fast worker could try to process the job before the Order/Voucher
  rows are visible outside the transaction.
- "Login to Buy" previously linked to `/login?redirect_to=...` — a query parameter Breeze's
  login controller never reads, so it silently did nothing and customers landed on the
  generic dashboard instead of back on the package they wanted. Fixed with a small
  `/login-with-redirect` route that populates Laravel's actual `url.intended` session key
  (which Breeze's unmodified controller does read), with same-site validation to avoid
  turning it into an open redirect.
- `CheckoutController::initiate()` had no error handling around the Paystack API call —
  if Paystack's API failed or timed out, the customer got a raw crash page and the order
  was left as a zombie `pending` row forever. Now caught, logged, order marked `failed`,
  friendly message shown.
- The order receipt page only had messaging for `fulfilled`/`paid` orders — viewing a
  `pending` or `failed` order showed just a status badge and nothing else. Added proper
  messaging for both, and fixed the "Amount Paid" label showing on orders that were never
  actually paid.
- No submit-guard on the "Pay with Paystack" button — a double-click created two separate
  orders and two Paystack transactions for one purchase. Added a disable-on-submit guard.
- A stray `colspan="6"` on an empty-state table row that actually has 7 columns (dashboard
  purchase history) — cosmetic only, but fixed.
- Verified Laravel 12's `Mailable::build()` method (used by `VoucherPurchased`) is still
  fully supported and not deprecated — checked this explicitly rather than assuming, since
  a wrong assumption here would have silently broken every voucher email.

**Round 2:**
- A **critical concurrency bug**: `PaymentFulfillmentService` had `lockForUpdate()` outside a
  `DB::transaction()`, meaning it did nothing — Paystack's webhook and browser callback firing
  near-simultaneously could each assign a *different* voucher to the same order. Fixed by
  wrapping in a transaction so the second caller blocks and correctly no-ops.
- A **guaranteed 500 error on every admin route**: the `auth.admin` middleware alias had `:admin`
  baked into the alias mapping itself, which isn't how Laravel parses middleware parameters —
  it would have tried to resolve a class literally named `...RedirectIfNotAdmin:admin`. Fixed by
  moving the `:admin` parameter to where the middleware is actually used in `routes/web.php`.
- A **checkbox bug**: unchecking "Active" in the Package/Site admin forms never actually
  deactivated anything, because unchecked HTML checkboxes submit nothing and the `'boolean'`
  validation rule silently drops absent optional fields. Fixed with explicit `$request->boolean()`.
- Unhandled exceptions in the Paystack webhook handler that would 500 (and cause Paystack to
  retry-storm) on any reference it didn't recognize — e.g. Paystack's own "Test Webhook" button.
- CSV export silently ignoring the status/location filters shown in its own URL.
- Rate limiting added to admin login and checkout initiation (previously missing).
- Order number entropy increased (was ~9,000 possible values/day, now ~2.2 billion).

None of this has been run end-to-end against a real Laravel boot — see the build environment
note above. Treat "audited" as "read line-by-line for logic errors, three times over," not
"executed and verified." The next real milestone is booting it and running `php artisan route:list`.



## Setup

1. **Install dependencies** (needs real internet — this pulls Laravel 12, the Paystack SDK, Excel/PDF packages):
   ```bash
   composer install
   ```
2. **Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Fill in `DB_*`, `PAYSTACK_PUBLIC_KEY`, `PAYSTACK_SECRET_KEY`, and your `MAIL_*` (Resend) settings.
3. **Database**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   This creates a super admin (`admin@citinetwifi.com` / `ChangeMe123!` — **change this password
   immediately** via `php artisan tinker` or the DB, there's no admin "edit own profile" UI yet)
   and seeds the 7 sample packages from the spec (6 Hours, Daily, Weekly, Weekly 2 Devices, Gaming,
   Monthly, Monthly 2 Devices).
4. **Customer auth (register/login/forgot/reset/verify email)** — deliberately not hand-rolled.
   Run:
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install blade
   npm install && npm run build
   ```
   This generates `routes/auth.php`, appends the `require` line to `routes/web.php` automatically,
   and gives you tested login/register/password-reset/email-verification views and controllers —
   exactly what the spec asks for ("Use Laravel authentication").
5. **Paystack webhook**: in your Paystack dashboard, set the webhook URL to
   `https://yourdomain.com/webhooks/paystack`. Locally, use `php artisan serve` + ngrok or the
   Paystack CLI to tunnel it.
6. **Queue worker** (for voucher delivery emails):
   ```bash
   php artisan queue:work
   ```
   On cPanel shared hosting, set this up as a cron-triggered `queue:work --stop-when-empty` job
   (most shared hosts don't allow long-running processes) — same pattern used for your power bank
   app's cron jobs.

## What's implemented

- **Database**: users (customers), separate `admins` table/guard, sites (locations), packages,
  vouchers (site-scoped), orders (site-scoped), payments, settings, audit_logs
- **Core purchase flow**: `CheckoutController` → Paystack → `PaystackWebhookController` (HMAC
  signature verified) + browser callback, both funneling through the same idempotent
  `PaymentFulfillmentService` so retries/double-callbacks can never double-fulfil an order
- **Anti-double-sell voucher assignment**: `VoucherAssignmentService` uses `lockForUpdate()`
  inside a DB transaction — two concurrent payment confirmations for the same package physically
  cannot walk away with the same voucher
- **Out-of-stock handling**: if a customer pays and no voucher is left, the order is marked `paid`
  (not `fulfilled`), logged at `critical`, and surfaces in the admin "needs attention" dashboard
  widget — it does not fail silently
- **Customer dashboard**: active voucher, full order history, printable receipt view
- **Admin panel**: separate login/guard, revenue dashboard (today/week/month, most popular
  package, low-stock warnings), package CRUD, CSV voucher import (dedupes on package+username),
  voucher inventory browser, order list with CSV export
- **Email**: queued voucher-delivery mailable (optional per spec — voucher is always shown
  on-screen and in the dashboard regardless of email success)
- **Bootstrap-inspired brand theme** — navy gradient, orange CTAs, rounded white cards, pill
  badges, matching the existing CitiNET captive-portal style.css (not Bootstrap 5 anymore —
  updated after you shared your actual hotspot portal design)

## What's NOT implemented yet (next phase)

- Customer auth views themselves (see step 4 above — use Breeze)
- Form Requests / Policies as dedicated classes (validation currently inline in controllers —
  fine functionally, but you asked for Form Requests/Policies explicitly, so I'd split these out
  next)
- Events/Listeners (order fulfillment currently calls the email job directly rather than firing
  an `OrderFulfilled` event — same runtime behavior, less decoupled)
- Admin: settings UI (business name/logo/Paystack keys/SMTP editable from a screen — the
  `Setting` model and DB table exist, just no admin CRUD screen yet), audit log viewer UI (data is
  being recorded, just no screen to browse it), full REST API for the future mobile app (only a
  placeholder `/api/packages` route exists)
- Voucher expiry background job (marking `sold` vouchers `expired` once `expires_at` passes —
  currently only checked at query time via `where('expires_at', '>', now())`, never actually
  flips the DB status)

## Multi-site voucher stock

Citinet WiFi runs multiple physical hotspot locations (Citinet 1–4 today), each with its
own independent voucher stock. This is modeled as:

- **`sites` table** — one row per physical location. Adding a 5th (or 10th) location is
  **Admin → Locations → Add Location**, no code or deploy required.
- **`vouchers.site_id`** — every voucher belongs to exactly one site. The uniqueness
  constraint is `(site_id, package_id, username)`, so the same username could in
  principle exist at two different sites without conflict.
- **`orders.site_id`** — captured at checkout when the customer picks a location, and
  used to scope which voucher gets assigned.
- **Checkout flow** — the package detail page shows a location picker (radio buttons)
  populated only with sites that currently have stock for that package. `VoucherAssignmentService`
  locks and assigns strictly within `(site_id, package_id)`, so two customers buying the
  same package at *different* locations never contend for the same voucher — and two
  customers buying at the *same* location can't double-claim one either.
- **CSV import** — the format is now `username,password[,package_slug][,site_slug]`. The
  import form lets you pick a default location (and package) so a single-site CSV doesn't
  need those columns repeated on every row; a mixed CSV can still override per-row.
- **Admin dashboard** — stock is shown as a package × location matrix so you can see at a
  glance which specific location is about to run out of which package.

The seeded default is Citinet 1–4 (`database/seeders/DatabaseSeeder.php`) — edit that or
just add real ones from the admin UI after first deploy.

## Architecture notes for future MikroTik/RADIUS integration

Per the "Future Ready" section of your spec, nothing here assumes vouchers stay purely
informational. `Voucher::expires_at` is already computed from `Package::duration_minutes` at
sale time, so a MikroTik/RADIUS sync job would just need to read `vouchers` where
`status = 'sold'` and push username/password/expiry — no schema changes needed. Multi-site support
(like your CitiNET MikroTik setup) would mean adding a `site_id` column to `packages` and
`vouchers`, following the same pattern your Next.js backend already uses for the 3-site voucher
allocation.
