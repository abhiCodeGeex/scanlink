# ScanLink Laravel

Migration of the legacy Kohana **ScanLink** app into **Laravel + Filament**, targeting
**legacy parity** (behaviour and UI as close to the old app as possible).

## Read this first
- **[.claude/README.md](.claude/README.md)** — project knowledge: environment gotchas, how to
  run the tests, which tests are already red, and the day-by-day work log. Read before
  starting; several of these will cost you an hour each if you discover them the hard way.
- **[PROJECT_HISTORY.md](PROJECT_HISTORY.md)** — architecture, flows, key file map, known
  gaps, and how to run locally.

## Key paths

| Item | Path |
|------|------|
| This repo — the Laravel app | repo root (`nwitadmin/scanlink`, branch `develop`) |
| Legacy Kohana app — behavioural source of truth | `E:\abhishek-project\scanlink` *(this machine)* |
| Second, older checkout — see the warning below | `E:\abhishek-project\scanlink-laravel` *(this machine)* |
| Old live reference | `https://scanlink.com.au/...` |
| Staging | `http://13.237.243.130:8000` — **deployed by hand**, not from this repo automatically |

> **Two checkouts exist on the original dev machine and they diverge.** The local Docker stack
> bind-mounts `scanlink-laravel`, so `localhost:8000` may serve a *different* build from the
> one you are editing. Confirm which build is serving before trusting the browser.
> See `.claude/memory/scanlink-two-checkouts.md`.

## Hard rules
- **Never** run destructive DB commands on the Docker MySQL (it holds a full live import): no
  `migrate:fresh`, `db:wipe`, `TRUNCATE`, `DROP`, volume rm, or re-import overwrite without
  explicit approval in the same chat. Prefer **additive** migrations
  (`Schema::hasTable` / `hasColumn`), applied with `--path=`.
- Tests must use **sqlite `:memory:`**, never Docker MySQL.
- Match legacy behaviour by **reading the old code first**, then implement.
- Only commit when asked. Exclude one-off `scripts/browser-*`, `debug-*`, `probe-*` scripts,
  storage logs and screenshots.
- **No credentials in the repo** — not in code, not in docs, not in `.claude/`. One was
  committed and pushed once already; see `.claude/memory/scanlink-credential-in-pushed-history.md`.

## Working expectations
- Prove UI and JavaScript fixes in a real browser before committing — the test suite cannot
  reproduce several classes of Filament/Livewire rendering bug. See
  `.claude/memory/abhishek-verify-before-committing.md`.
- Attribute test failures against the known-red baseline in
  `.claude/memory/scanlink-test-baseline.md` before assuming your change caused them.

## Stack / local
- Laravel + Filament, Livewire, Docker (app / mysql / nginx / redis / mailpit).
  Portal at `/portal`, admin at `/admin`.
- PHP runs inside Docker: `docker compose exec -T app php artisan ...` (not on the Windows host PATH).
- Local URL `http://localhost:8000`; Mailpit `http://localhost:8025`.
- **This checkout has no `vendor/`** — running the test suite needs the container recipe in
  `.claude/memory/scanlink-running-tests.md`.
