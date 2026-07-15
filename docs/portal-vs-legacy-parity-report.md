# Old Kohana vs Laravel Portal — Final Gap Closure Report

**Date:** 2026-07-15  
**Branch:** `feature/client-portal-migration` (not merged to `main`)  
**Live import:** `scanlink_development` → Docker `scanlink_laravel`

## Data verification (imported)

| Table | Rows |
|-------|------|
| clients | 458 |
| client_users | 504 |
| users (Laravel auth) | 1085 |
| profiles | 6061 |
| code_purchase | 1148 |
| code_purchase_detail | 11081 |
| form_builder_question | 162373 |
| form_builder_answers | 2984638 |
| form_builder_orders | 109 |
| weblink | 2408 |
| logo | 3025 |
| picture | 610 |
| documents | 508 |
| video | 262 |
| qrimage | 6616 |
| orders | 14 |
| voc_users | 6 |
| equipment_types | 12 |
| settings | 10 |

Pipeline: `scripts/import-live-to-local.ps1` → `scanlink:import-postprocess` (adapt users→client_users + sync identities).

Admin login after import: `admin@scanlink.com` / `Admin@12345`  
Legacy MD5 portal passwords: set to fallback `changeme` during sync (users must reset).

## Feature parity (menus / pages / flows)

| Legacy | Laravel | Status |
|--------|---------|--------|
| Login / logout / forgot | `/portal/login` + password reset | Wired |
| Forced password change | `ForcePasswordChange` + middleware | Wired |
| Register (+ reseller) | `/portal/register` | Wired |
| Dashboard | `/portal/dashboard` | Wired |
| Master code list + type tabs | `/portal/profiles` | Wired |
| Per-type CRUD (plant…voc) | Shared profile form by `equipment_types` | Wired |
| Media / weblinks / checklist | Portal form + public scan | Wired |
| QR download | Portal view/edit + list action | Wired |
| My Account | `/portal/account` | Wired |
| Team users + all access_* flags | `/portal/team-users` | Wired |
| Code balance | `/portal/code-balance` | Wired |
| Purchase codes | `/portal/purchase-codes` | Wired (invoice-later / pending) |
| Multi renew any type | `/portal/renew-codes` | Wired |
| Form builder + options + reorder | `/portal/form-builder` | Wired |
| Form library apply/delete | `/portal/form-library` | Wired |
| Form submissions detail | `/portal/form-submissions` | Wired |
| Participants | `/portal/participants` | Wired |
| Buy form builder | `/portal/purchase-form-builder` | Wired |
| Scan analytics + CSV | `/portal/scan-analytics` | Wired (Galatech API) |
| Cumulative analytics + CSV | `/portal/cumulative-analytics` | Wired |
| Visitor log + CSV | `/portal/visitor-log` | Wired |
| Order labels + price summary | `/portal/order-labels` | Wired |
| PayPal IPN | `POST /notify/paypal` | Stub (marks Paid) |
| VOC dashboard | `/portal/voc-dashboard` | Wired |
| Public scan full profile | `/{clientUrl}/{profileId}` | Wired |
| Marketing + how-to + contact | `/`, `/how-to`, `/contact`, … | Wired |
| Expiry / participant crons | scheduled artisan | Wired |

## Intentionally remaining (documented UAT, not missing modules)

1. **Live PayPal checkout UI** — orders created as Pending; IPN stub only.  
2. **Galatech chart polish** — data via API; depends on profile `analytic_key` + API reachability.  
3. **Cross-account “form from other user”** — legacy iframe edge case; library clone covers same-client reuse.  
4. **COVID / visitation APIs** — obsolete, excluded.  
5. **Merge to `main`** — blocked until your UAT sign-off.

## Smoke confirmation

```
scripts/smoke-portal-parity.php → all portal pages + routes OK
HTTP 200: /portal/login, /admin/login, /how-to, /pricing, /faq, /contact, /baulderstone/4
```

## Credentials note

Live RDS credentials were used only for this import via env vars / import script. They are **not** stored in the repository.
