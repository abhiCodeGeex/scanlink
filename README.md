# ScanLink Admin + minimal Client Portal

Laravel + Filament application with:

## Admin (`/admin`)

Full admin panel (clients, orders, products, settings, etc.).

## Client portal (minimal)

| Feature | URL |
|---------|-----|
| Landing / Home (login) | `/` |
| Register | `/portal/register` |
| Portal home (after login) | `/portal/home` |
| Edit profile | `/portal/account` |
| Login | form on `/` → `POST /portal-login` |
| Logout | portal sidebar Sign out → back to `/` |

**Not included:** code purchase UI beyond registration’s free-code grant, profiles CRUD, analytics, form builder, labels, marketing FAQ/pricing pages, etc.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB, then:
php artisan migrate
php artisan serve
```

- Public site / client login: `/`
- Admin: `/admin`
