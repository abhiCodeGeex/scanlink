# Phase 2 - Legacy Schema Baseline and First Admin Resources

## Scope delivered
- Legacy-compatible baseline tables for core admin workflows:
  - `clients`
  - `client_users` (maps to legacy `users` portal accounts)
  - `code_purchase`
  - `code_purchase_detail`
- Eloquent models with relationships and enums.
- Filament admin resources:
  - **Clients** (list, create, edit, block/unblock, sub-user management)
  - **Code orders** (list, view/edit status; create disabled to match legacy admin)
- Phase 2 sample data seeder.
- Automated tests for schema, seeders, and admin resources.

## Table naming notes
| Legacy table | Laravel local table | Notes |
|---|---|---|
| `clients` | `clients` | Same name |
| `users` (portal) | `client_users` | Avoids clash with Laravel admin `users` |
| `code_purchase` | `code_purchase` | Same name |
| `code_purchase_detail` | `code_purchase_detail` | Same name |
| `admin` | `users` | Filament admin auth uses Laravel `users` until live import mapping is applied |

On live DB import, use the runbook in `docs/db-migration-runbook.md` and the verification script in `scripts/verify-import.ps1`.

## Run locally (Docker)
```bash
docker compose up --build
```

Startup now runs:
1. `php artisan migrate --force`
2. `php artisan db:seed --force`
3. `php artisan filament:assets`

## Admin login
- URL: `http://localhost:8000/admin`
- Email: `admin@scanlink.com`
- Password: `Admin@12345`

## Sample data after seeding
- **Acme Inspections** (approved) with 1 primary user and 1 sub-user
- **Blocked Demo Client** (blocked)
- 2 code orders (1 completed paid order, 1 pending free-code order)

## Tests
```bash
docker compose exec app php artisan test
```

Coverage includes:
- Legacy model relationships and table names
- Phase 2 seeder output
- Filament client list/create (primary user auto-created)
- Filament code order list and disabled create route

## Next phase preview (Phase 3)
- Import sanitized live DB snapshot into staging
- Add profiles resource and parity checks
- Role/permission hardening for multi-admin scenarios
- Form builder and asset modules (portal-side)
