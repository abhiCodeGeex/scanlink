# Client Portal Migration — Parity Checklist

**Branch:** `feature/client-portal-migration` (do not merge to `main` until UAT sign-off)  
**Panel:** `/portal` Filament (`ClientPortalPanelProvider`) — same ScanLink green theme as admin  
**Public scan:** `/{clientUrl}/{profileId}`

## Wired features

| Legacy area | Laravel | Status |
|-------------|---------|--------|
| Login / logout / password reset | Filament portal auth | Done |
| Forced password change | `EnsurePortalPasswordChanged` + `ForcePasswordChange` | Done |
| Register (client + primary user) | `App\Filament\Portal\Auth\Register` | Done |
| Register reseller code | Optional `client_reseller_code` on register → `client_users` | Done |
| Dashboard | `PortalDashboard` | Done |
| Edit account | `EditAccount` | Done |
| Team / sub-users + permissions | `TeamUsers` (full legacy flag set) | Done |
| Sub-user permission gates | Analytics, forms, visitor log, labels, download QR | Done |
| Code balance | `CodeBalance` | Done |
| Purchase codes | `PurchaseCodes` + `CodePurchaseService` | Done |
| Multiple code renewal (any type, 60-day window) | `MultipleCodeRenewal` + `CodeProfileRenewalService` | Done |
| Master code list + CRUD | Portal `ProfileResource` | Done |
| Profile list type tabs + row actions | `ListProfiles` tabs + `PortalProfilesTable` actions | Done |
| Media (logos/pics/docs/videos) | Admin relation managers + public scan render | Done |
| QR generate on create | `ProfileQrService` in CreateProfile | Done |
| Download QR / PDF on portal view & edit | `HasProfileQrActions` (gated by `access_download`) | Done |
| Form builder editor (options, reorder, activate) | `FormBuilder` | Done |
| Form library (apply / delete) | `FormLibrary` + `FormLibraryService` | Done |
| Form submissions (expandable answers) | `FormSubmissions` | Done |
| Form builder purchase / activate | `PurchaseFormBuilder` | Done |
| Participants | `ManageParticipants` | Done |
| Scan analytics (readable UI + CSV export) | `ScanAnalytics` + `AnalyticsApiService` | Done |
| Cumulative analytics (multi-profile scan list + CSV) | `CumulativeAnalytics` | Done |
| Visitor log (+ CSV export) | `VisitorLog` | Done |
| Order physical labels (price summary) | `OrderLabel` + `LabelOrderService` | Done |
| How-to tutorials | `GET /how-to` + legacy YouTube topic list | Done |
| PayPal IPN stub | `POST /notify/paypal` | Done |
| VOC dashboard (linked profiles + documents) | `VocDashboard` | Done |
| Public mobile scan (full profile, checklist, media) | `MobileProfileController` | Done |
| Marketing pages + contact mail | `/contact` + `MarketingController` | Done |
| Expiry / participant crons | `scanlink:send-*` scheduled in `routes/console.php` | Done |
| Portal feature tables | Migration `2026_07_14_150000_*` | Done |

## Remaining / UAT

| Area | Notes |
|------|-------|
| Galatech analytics API | Requires live `analytic_key` on profiles + API reachability for charts/maps |
| PayPal live checkout | IPN stub only; full payment flow needs UAT |
| VOC equipment type in DB | Tabs/filters include `voc` / `survey` when `equipment_types` rows exist |
| Branch merge to `main` | Blocked until UAT sign-off |

## Smoke verification

```bash
docker compose exec app php scripts/smoke-portal-parity.php
```

After live import, expect non-zero counts for `clients`, `profiles`, and related tables. The script also checks portal page classes and `scan.show` / marketing routes.

## Data import (existing pipeline)

Continue using `docs/db-migration-runbook.md` + `scanlink:adapt-live-import` / `sync-all-users` / `verify-import`. Portal uses the same `clients`, `client_users`, `profiles`, media, and form_builder_* tables.

## Out of scope (explicit)

- COVID / visitation APIs
- Native mobile apps
- Full Galatech Symfony rewrite (HTTP wrapper in Phase 5a)
- Merging this branch into `main` without UAT
- Profile type preview page (covered by `ListProfiles` equipment-type tabs)

## Tests

```bash
docker compose exec app php artisan test --filter=Portal
docker compose exec app php artisan test --filter=PortalAuthTest
docker compose exec app php artisan test
```
