# Client Portal Migration — Parity Checklist

**Branch:** `feature/client-portal-migration` (do not merge to `main` until UAT sign-off)  
**Panel:** `/portal` Filament (`ClientPortalPanelProvider`) — same ScanLink green theme as admin  
**Public scan:** `/{clientUrl}/{profileId}`

## Wired features

| Legacy area | Laravel | Status |
|-------------|---------|--------|
| Login / logout / password reset | Filament portal auth | Done |
| Register (client + primary user) | `App\Filament\Portal\Auth\Register` | Done |
| Dashboard | `PortalDashboard` | Done |
| Edit account | `EditAccount` | Done |
| Team / sub-users + permissions | `TeamUsers` | Done |
| Code balance | `CodeBalance` | Done |
| Purchase codes | `PurchaseCodes` + `CodePurchaseService` | Done |
| Multiple code renewal | `MultipleCodeRenewal` + `CodeProfileRenewalService` | Done |
| Master code list + CRUD | Portal `ProfileResource` | Done |
| Media (logos/pics/docs/videos) | Admin relation managers on portal profiles | Done |
| QR generate on create | `ProfileQrService` in CreateProfile | Done |
| Form builder editor | `FormBuilder` | Done |
| Form submissions | `FormSubmissions` | Done |
| Form builder purchase / activate | `PurchaseFormBuilder` | Done |
| Participants | `ManageParticipants` | Done |
| Scan analytics | `ScanAnalytics` + `AnalyticsApiService` | Done |
| Visitor log | `VisitorLog` | Done |
| Order physical labels | `OrderLabel` + `LabelOrderService` | Done |
| PayPal IPN stub | `POST /notify/paypal` | Done |
| VOC login entry | `/voclogin` → portal login; `VocDashboard` | Done |
| Public mobile scan + password + forms + visitors | `MobileProfileController` | Done |
| Marketing pages | `/`, `/contact`, `/pricing`, `/faq`, `/privacy`, `/terms` | Done |
| Expiry / participant crons | `scanlink:send-*` scheduled in `routes/console.php` | Done |
| Portal feature tables | Migration `2026_07_14_150000_*` | Done |

## Data import (existing pipeline)

Continue using `docs/db-migration-runbook.md` + `scanlink:adapt-live-import` / `sync-all-users` / `verify-import`. Portal uses the same `clients`, `client_users`, `profiles`, media, and form_builder_* tables.

## Out of scope (explicit)

- COVID / visitation APIs
- Native mobile apps
- Full Galatech Symfony rewrite (HTTP wrapper in Phase 5a)
- Merging this branch into `main` without UAT

## Tests

```bash
docker compose exec app php artisan test --filter=Portal
docker compose exec app php artisan test
```
