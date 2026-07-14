# Phase 0 and 1 Implementation (Admin-First)

## Phase 0 - Readiness and Safety Guardrails

### Scope freeze
- First delivery scope is platform admin replacement (`siteadmin` equivalent), not full public app.
- Any legacy schema changes during migration must be additive only.

### Delivery safety rules
- No direct write operations against live DB from local development.
- Every data import process must be repeatable and idempotent.
- Keep rollback path: legacy admin remains operational until parity sign-off.

### Environment topology
- `local` for development.
- `staging` for parity testing.
- `production` for cutover.

### Acceptance criteria for Phase 0
- Laravel project scaffolded.
- Admin framework selected and installed (Filament).
- Role/permission and module backlog identified.
- Initial migration runbook documented.

## Phase 1 - Local Foundation

### Tech baseline
- Laravel 13
- Filament 5 (admin panel foundation)
- MySQL 8.4
- Redis 7
- Mailpit for local SMTP testing

### Local run modes
- Docker mode: `docker compose up --build`
- Non-Docker mode: local PHP + MySQL + Redis

### Core setup outputs
- Docker services defined in `docker-compose.yml`.
- PHP runtime image with required extensions in `Dockerfile`.
- Local and Docker environment variable templates:
  - `.env.example`
  - `.env.docker.example`

### Phase 1 acceptance checklist
- App starts in Docker and serves on `http://localhost:8000`.
- MySQL service reachable on host port `3307`.
- Mailpit UI available on `http://localhost:8025`.
- Laravel migrations runnable.
- Filament package available for panel bootstrapping.

## Immediate Next Steps (Phase 2 preview)
- Create database baseline migration set from legacy ScanLink schema.
- Build import scripts for live DB snapshot to local/staging.
- Implement admin auth/roles and first 2-3 admin resources.
