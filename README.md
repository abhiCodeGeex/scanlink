# ScanLink Admin (Client module only)

Trimmed Laravel + Filament admin app containing only:

- **Admin Home**
- **Client** module: Add Client, Manage Client, Sub Divide Client

No client portal, orders, products, settings pages, gallery, testimonials, or public scan routes.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB, then:
php artisan migrate
php artisan serve
```

Open `/admin` (or `/` which redirects there).
