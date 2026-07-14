# Phase 6 — Live DB Import, QR & Media

## Live database import workflow

1. Export live DB (read-only) using `scripts/export-live-db.example.ps1`
2. Import dump into local MySQL (`scripts/import-local-db.example.ps1`)
3. Run Laravel migrations for any additive tables not in the dump:
   ```bash
   docker compose exec app php artisan migrate --force
   ```
4. Post-process the import:
   ```bash
   docker compose exec app php artisan scanlink:import-postprocess --default-password="YourKnownPassword"
   ```
5. Verify row counts:
   ```bash
   docker compose exec app php artisan scanlink:verify-import
   # or on host:
   ./scripts/verify-import.ps1
   ```

### Admin → Laravel users mapping

Legacy `admin` table uses `username` + plain-text `password`. The sync command:

```bash
php artisan scanlink:sync-admin-users --default-password="Admin@12345"
```

- Maps `username` → `users.email` (`user@scanlink.local` if no `@` in username)
- Sets `admin_role` = `super_admin`
- Re-hashes passwords for Filament login

## QR code generation

On profile create/edit, `ProfileQrService`:

- Builds URL: `{SCANLINK_PORTAL_URL}/{client.url}/{profile.id}`
- Optionally shortens via `SCANLINK_SHORT_URL_API_TOKEN`
- Generates PNG to `storage/app/public/qrcode/CSQRIMG{id}.png` (or `dmcode/DMIMG{id}.png` for Data Matrix)
- Records path in legacy `qrimage` table

View profile → **Download QR** action.

### Environment

```env
SCANLINK_PORTAL_URL=https://scanlink.com.au
SCANLINK_SHORT_URL_API_TOKEN=
```

Run `php artisan storage:link` so `/storage/qrcode/...` is web-accessible.

## Profile media uploads

On save, `ProfileMediaService` persists Filament uploads to:

- `logo` table (company logo)
- `picture` table (gallery images)
- `documents` table (PDFs/docs)
- `video` table (from video repeater)

Files stored on the `public` disk under `profiles/logos`, `profiles/pictures`, `profiles/documents`.

## Commands reference

| Command | Purpose |
|---------|---------|
| `scanlink:verify-import` | Row counts for critical tables |
| `scanlink:sync-admin-users` | Map legacy `admin` → `users` |
| `scanlink:import-postprocess` | Verify + sync in one step |
