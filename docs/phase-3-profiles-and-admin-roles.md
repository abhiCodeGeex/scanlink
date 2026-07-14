# Phase 3 - Code Profiles, Equipment Types, and Admin Roles

## Scope delivered
- Legacy-compatible tables:
  - `equipment_types`
  - `profiles`
  - `users.admin_role` for panel access control
- Eloquent models with relationships and active/deleted scope on profiles.
- Filament admin resource:
  - **Code profiles** (list, view, edit, archive; create disabled to match legacy admin read-focused workflow)
  - Profiles relation tab on **Clients**
- Admin roles enum: `super_admin`, `admin`, `support`.
- Phase 3 seeder with equipment types and sample profiles.
- Automated tests for seeder, profiles resource, and admin roles.

## Table naming notes
| Legacy table | Laravel table | Notes |
|---|---|---|
| `equipment_types` | `equipment_types` | Same name |
| `profiles` | `profiles` | Same name |
| `admin` | `users` + `admin_role` | Filament auth until live import mapping |

## Sample data after seeding
- 10 equipment types (asset, product, people, plant, etc.)
- 2 active profiles for Acme Inspections
- 1 archived profile (hidden from default list)

## Tests
```bash
docker compose exec app php artisan test
```

## Next phase preview (Phase 4)
- Live DB import rehearsal on staging
- Settings and code pricing admin screens
- Form builder orders visibility
- Portal-side modules (form builder UI, asset editor)