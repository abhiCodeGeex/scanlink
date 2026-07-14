# Admin portal (siteadmin) — migration complete

**Scope:** Laravel Filament admin at `/admin` replaces legacy `siteadmin/`.  
**Out of scope:** client portal (`application/`), public scan pages, form-builder editor, mobile apps.

## Verdict

**Admin siteadmin parity is complete.** Nothing material remains for the admin portal CRUD/workflows that existed in `siteadmin`.

## Closed in this pass

| Legacy capability | Laravel |
|-------------------|---------|
| Code renew (single + multi) | Edit/View **Renew code** + list **Renew selected codes** via `CodeProfileRenewalService` |
| QR PNG download | **Download QR** |
| QR PDF download | **Download PDF** (`TCPDF`) |
| QR colour regenerate | **QR colour** action + `color_code` on code form |
| Bridge graphic remove | Logos relation **Remove logo** |
| CustomQR live preview | Create/edit CustomQR URL field with live QR preview |
| Gallery | Add / block / delete only (legacy had no working edit) |

## Intentionally not admin

- Client portal login & dashboard
- Public profile / bridge graphic rendering for scanners
- Form builder question editor (orders CRUD is in admin)
- Live production DB cutover / YouTube OAuth secrets (config + docs only)

## Verify

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan test
```
