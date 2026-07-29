# ScanLink Laravel

Migration of the legacy Kohana **ScanLink** app into **Laravel + Filament**, targeting **legacy parity** (behavior + UI as close to the old app as possible).

## Read this first
- **[PROJECT_HISTORY.md](PROJECT_HISTORY.md)** — full task history, architecture, flows, key files, known gaps, and how to run locally. Read it at the start of every session before working on a feature.

## Key paths
| Item | Path |
|------|------|
| New app (this repo) | `E:\abhishek-project\scanlink-laravel` |
| Legacy app (source of truth for behavior — read before implementing) | `E:\abhishek-project\scanlink` |
| Old live reference | `https://scanlink.com.au/...` |

## Hard rules
- **Never** run destructive DB commands on the Docker MySQL (it holds a full live import): no `migrate:fresh`, `db:wipe`, `TRUNCATE`, `DROP`, volume rm, or re-import overwrite without explicit approval in the same chat. Prefer **additive** migrations (`Schema::hasTable` / `hasColumn`), applied with `--path=`.
- Tests must use **sqlite `:memory:`**, never Docker MySQL.
- Match legacy behavior by **reading the old code first**, then implement.
- Only commit when asked. Exclude one-off `scripts/browser-*`, `debug-*`, `probe-*` scripts, storage logs/screenshots.

## Stack / local
- Laravel + Filament, Livewire, Docker (app/mysql/nginx/redis/mailpit). Portal at `/portal`, admin at `/admin`.
- PHP runs inside Docker: `docker compose exec -T app php artisan ...` (not on Windows host PATH).
- Local URL `http://localhost:8000`; Mailpit `http://localhost:8025`.
- Branch: `feature/client-portal-migration`.
