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

### Demo accounts (password: `password`)

| Role | Email |
|---|---|
| Admin | admin@soukelkom.test |
| Seller | ahmed@soukelkom.test |
| Seller | maya@soukelkom.test |
| Buyer | buyer@soukelkom.test |

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
