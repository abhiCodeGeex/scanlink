# ScanLink Laravel (Migration Foundation)

This repository contains Phase 0 and Phase 1 foundation work for migrating ScanLink to Laravel with an admin-first approach.

## Implemented
- Laravel application scaffold.
- Filament admin package added (to match the Filament demo style baseline: [Filament Demo](https://demo.filamentphp.com/)).
- Docker-based local development stack:
  - PHP app container
  - MySQL 8.4
  - Redis 7
  - Mailpit
- Non-Docker run path documented.
- Migration docs for phase planning and DB import runbook.
- **Phase 2:** legacy schema baseline, Clients and Code Orders admin resources, seed data, and automated tests.
- **Phase 3:** code profiles, equipment types, admin roles, and profile admin resource with tests.
- **Phase 4:** legacy parity alignment - integer code-order statuses (0-4), legacy list columns/filters, read-only order view with status change, client reseller-code/free-codes actions, and client-scoped user management.
- **Phases 5–10:** full admin parity — all 3 order types, complete client/user flows (manage users page, subdivide wizard, renewal emails), profiles/product with type-specific forms and media tables, settings/pricing/testimonials/gallery, legacy navigation and admin home.
- **Phase 6:** live DB import commands (`scanlink:import-postprocess`), admin→users sync, QR generation on profile save, media upload persistence.

## Phase Docs
- [Phase 0 and 1 admin-first implementation](docs/phase-0-1-admin-first.md)
- [Phase 2 legacy schema and admin resources](docs/phase-2-legacy-schema-and-admin-resources.md)
- [Phase 3 profiles and admin roles](docs/phase-3-profiles-and-admin-roles.md)
- [Phase 4 legacy parity alignment](docs/phase-4-legacy-parity.md)
- [Phases 5–10 full admin parity](docs/phase-5-10-full-admin-parity.md)
- [Phase 6 import, QR & media](docs/phase-6-import-qr-media.md)
- [DB migration runbook](docs/db-migration-runbook.md)

## Prerequisites

### Docker path
- Docker Desktop with Compose

### Non-Docker path
- PHP 8.4.1+ (required by installed dependencies)
- Composer 2.x
- MySQL 8.x
- Redis 7.x
- Node 20+ and npm

## Run with Docker
1. Copy environment:
   - `copy .env.docker.example .env.docker`
2. Start stack:
   - `docker compose up --build`
3. Open:
   - App: `http://localhost:8000`
   - Mailpit: `http://localhost:8025`
   - MySQL host port: `3307`

## Run without Docker
1. Copy environment:
   - `copy .env.example .env`
2. Set DB/Redis credentials in `.env`.
3. Install dependencies:
   - `composer install`
   - `npm install`
4. Initialize app:
   - `php artisan key:generate`
   - `php artisan migrate`
5. Start services:
   - `php artisan serve`
   - `php artisan queue:listen`
   - `npm run dev`

## Notes
- If local PHP is below 8.4.1, use Docker mode until PHP is upgraded.
- Admin login: `admin@scanlink.com` / `Admin@12345`
- Run tests: `docker compose exec app php artisan test`
