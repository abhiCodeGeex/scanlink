# ScanLink Admin

Laravel + Filament **admin-only** application.

There is **no client portal**, marketing site, or public scan UI in this repo.

## Included (admin)

- Admin Home
- Clients (add / manage / subdivide) and client users
- Products / code profiles
- Orders: physical labels, code purchases, form-builder orders
- Settings: global, code pricing, reseller pricing
- Gallery & testimonials (CMS)
- Reports (e.g. email submission log)
- Change password

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB, then:
php artisan migrate
php artisan serve
```

Open `/admin` (or `/`, which redirects there).

`SCANLINK_PORTAL_URL` in `.env` is only used when generating QR destination URLs pointing at an external portal host — this app does not serve the portal.
