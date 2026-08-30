# SOUKELKOM — The Local Marketplace Where Everyone Wins

Multi-vendor marketplace for Lebanon & MENA. Platform owner takes a commission on every sale; sellers ship directly to buyers ("Ship by Seller"). One cart, multiple sellers, one payment — money is split automatically by the platform ledger.

## Tech Stack

- **Laravel 11** (PHP >= 8.2) · **MySQL/MariaDB 8**
- **Livewire 3 + Alpine.js + TailwindCSS** (mobile-first)
- **Breeze (Volt)** auth · **spatie/laravel-permission** roles
- **spatie/laravel-medialibrary** product images · **Scout** search (collection driver locally / Meilisearch in prod)
- **Horizon + Redis** queue in production (**database** driver on Windows dev)
- **Stripe Connect** ready (config-gated) + **Manual bank transfer** first-class
- **Pest** tests · **Pint** PSR-12

## Install (Windows / XAMPP)

```bash
# 1. Start MariaDB (XAMPP)
C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --standalone

# 2. Create the databases
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE soukelkom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE DATABASE soukelkom_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Install & configure
cd C:\dev\soukelkom
composer install
npm install
copy .env.example .env   # then set DB_DATABASE=soukelkom (already preconfigured)
php artisan key:generate

# 4. Migrate + seed demo data
php artisan migrate:fresh --seed

# 5. Media symlink + assets
php artisan storage:link
npm run build

# 6. Run
php artisan serve        # http://127.0.0.1:8000
```

### Local email (Mailpit)

Emails (approvals, orders, payouts) are captured locally by **Mailpit** so nothing leaves your machine during development.

```bash
# Mailpit is installed at C:\tools\mailpit\
C:\tools\mailpit\mailpit.exe --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025
```

Then open **http://127.0.0.1:8025** to read all outgoing mail live. The app is pre-configured
(`MAIL_MAILER=smtp`, `MAIL_PORT=1025`) to deliver there. For production, point `.env` at your
real SMTP provider and swap the config back via `php artisan config:cache`.

> All money distribution is **synchronous** (`DistributeEarnings::dispatchSync`), so seller
> balances and the ledger update the instant payment is confirmed — no queue worker required.
> The 48h auto-cancel sweep runs on a deferred job (`CancelUnshippedOrders`) and needs a worker
> in production (see Production Notes).

### Seller approval email flow

When an admin approves a seller, a `SellerApprovedMail` is queued/sent to the seller (store name
in the subject). Verify with the seeded seller `kareem@soukelkom.test` or any new store:
approve it in Admin → Sellers, then check Mailpit at `http://127.0.0.1:8025`.

### Demo accounts (password: `password`)

| Role | Email |
|---|---|
| Admin | admin@soukelkom.test |
| Seller | ahmed@soukelkom.test |
| Seller | maya@soukelkom.test |
| Buyer | buyer@soukelkom.test |

## Security (A+ hardening)

The app ships with an A+-oriented security posture out of the box:

- **Security headers** on every response (via `AddSecurityHeaders`):
  `Strict-Transport-Security` (HSTS preload), `X-Frame-Options: DENY`,
  `X-Content-Type-Options: nosniff`, `Referrer-Policy`,
  `Permissions-Policy` (all sensors off), `Cross-Origin-Opener-Policy`,
  and a **nonce-based Content-Security-Policy** (no `unsafe-inline` for scripts).
- **Rate limiting** (strict, admins included): auth routes 10/min, checkout 6/min,
  seller/admin panels 60/min.
- **Session hardening**: `SESSION_SAME_SITE=strict`, secure cookie enabled in production.
- **CSRF** enabled on all state-changing requests (Laravel default).
- **Escape by default**: Blade `{{ }}` output escaping; no `{!! raw !!}` left in views.
- **SQL injection**: 100% Eloquent/parameterized queries — zero raw SQL.
- **IDOR isolation**: order/cart/product scoping by owner, enforced by tests.
- **Email rule hardening**: all user-facing `email` rules use `email:rfc,filter`
  (mitigate CRLF header-injection advisory affecting Laravel 11).
- **`composer audit` / `npm audit`** — zero NEW vulnerabilities; two residual
  advisories documented: `phpunit/phpunit` (dev-only tooling, not deployed) and a
  Laravel-framework advisory fixed only in 12.x (the affected surface — signed URL
  + email rules — is mitigated and throttled in code).
- **TrustProxies** middleware ready for reverse-proxy/CDN (set `TRUSTED_PROXIES`).

Verify with `tests/Feature/{SecurityHeaders,RateLimit,IdorAccess,SqlInjectionSafety,XssOutputEscaping}Test.php`.

For an external **A+ grader** (SecurityHeaders.com / SSL Labs / OWASP ZAP): deploy on a real
domain with TLS, then open `.env.production.example` and follow the values (APP_DEBUG=false,
SESSION_SECURE_COOKIE=true, trusted proxies, real SMTP, Redis/Meilisearch).

## Tests

```bash
php artisan test        # uses soukelkom_test database (configured in phpunit.xml)
vendor/bin/pint --test  # PSR-12 style check
```

Covers all 6 QA scenarios: seller onboarding, product lifecycle (+rejection reason email), multi-seller checkout split ($50+$20 → commissions $5+$2), earnings distribution + auto-cancel refund clawback, payout flow ($100 → request $50 → paid → balance $50), and security isolation (403 cross-seller edits).

## Money Engine

- Commission resolution chain: `product.commission_rate` → `seller.commission_override` → `settings.global_commission_rate` (default 10%).
- On payment confirmation, `DistributeEarnings` credits each seller's balance and writes immutable ledger rows (earning + commission) to `transactions`.
- Earnings are held until `delivered_at + hold_days_after_delivery` (14 days); `ReleaseHeldEarnings` frees them daily.
- Sellers must ship within `ship_deadline_hours` (48h) or `CancelUnshippedOrders` cancels the item, refunds the buyer, and claws back the earning.
- Payouts require available balance ≥ `min_payout` ($50); one pending payout per seller; admin marks paid → balance deducted + ledger row + email.

## Production Notes

- Set `QUEUE_CONNECTION=redis`, run Horizon (`php artisan horizon`), schedule (`php artisan schedule:run`) for the release/auto-cancel jobs.
- Add Stripe keys to enable card checkout (manual transfer remains available). Stripe is not directly available in Lebanon — manual gateway is the primary MENA path.
- Laravel 11.x is pinned per spec; newer patch releases address published security advisories — keep `composer update` current before going live.
