# ScanLink Laravel — Project History & AI Handoff

> Generated for continuing work in another AI tool.  
> Branch: `feature/client-portal-migration`  
> Latest useful commit: `6751a86` — *Align portal purchase, renew, create-slot, and admin reseller-code flows with legacy parity.*  
> Date context: July 2026

---

## 1. What this project is

**Goal:** Rebuild / migrate the legacy Kohana ScanLink app into Laravel + Filament, with **legacy parity** (behavior + UI as close as possible).

| Item | Path / value |
|------|----------------|
| New app (this repo) | `E:\abhishek-project\scanlink-laravel` |
| Legacy app (source of truth for behavior) | `E:\abhishek-project\scanlink` |
| Client portal | `/portal` (Filament panel `portal`) |
| Admin panel | `/admin` (Filament panel `admin`) |
| Local URL | `http://localhost:8000` (nginx Docker) |
| Stack | Laravel + Filament v4-style, Livewire, Docker (app/mysql/nginx/redis/mailpit) |
| Git branch | `feature/client-portal-migration` |

**User expectation:** “Do it exactly like old application” — inspect legacy first, then implement. Pixel-perfect UI for key flows when requested.

---

## 2. Hard rules (must follow)

### Database
- Docker MySQL often has a **full live import** (hundreds of clients / thousands of profiles).
- **Never** run wipe/destructive commands without explicit approval in the same chat:
  - `migrate:fresh`, `db:wipe`, `TRUNCATE`, `DROP`, volume rm, re-import overwrite
- Prefer **additive** migrations (`Schema::hasTable` / `hasColumn`).
- Tests must use **sqlite `:memory:`** — never Docker MySQL.

### Legacy parity
- Old code: `E:\abhishek-project\scanlink`
- Old live reference: `https://scanlink.com.au/...` (e.g. `/profile/codeBalance`, `/profile/purchase`)
- Test portal login used in sessions: `anshgeex@gmail.com` / `12345678` (local + sometimes old site)

### Commits
- Only commit when asked.
- Commit only useful app files — exclude one-off `scripts/browser-*`, `debug-*`, `probe-*`, storage logs/screenshots.

---

## 3. Architecture snapshot

### Profiles / codes
- Purchased codes create **blank profile slots** (`profiles.update_or_not = '0'`).
- **Code Balance** lists open slots (`update_or_not = '0'`).
- **Master Code List** lists activated profiles (`update_or_not = '1'`).
- Create flow uses `CreateProfile` as `EditRecord` on a bound slot so Form Builder has `profile_id`.
- Slot binding service: `app/Services/ProfileDraftSlotService.php`
- **Important fix:** binding a template / Add New Code must **NOT** set `update_or_not=1` until **Save**. Balance must not drop on open-without-save.

### Purchase flow (portal)
1. `/portal/purchase-codes` — quantity / reseller tab, calculate, PURCHASE  
2. `/portal/purchase-billing` — billing details  
3. `/portal/purchase-order-summary` — terms + PROCEED → create `code_purchase` + open slots + invoice email  

Files:
- `app/Filament/Portal/Pages/PurchaseCodes.php`
- `PurchaseBilling.php`, `PurchaseOrderSummary.php`
- Views under `resources/views/filament/portal/pages/purchase-*.blade.php`

### Renew flow (portal)
1. Select codes → Renew Selected  
2. `/portal/renew-order-summary` — priced summary, invoice terms  
3. PROCEED → extend expiry + renew order + invoice email  

Files:
- `app/Filament/Portal/Pages/RenewCodeSummary.php`
- `app/Services/CodeProfileRenewalService.php` (legacy pricing: pre-expiry = original amount; post-expiry = qty-1 tier / reseller amount)

### Reseller codes (admin)
- No separate reseller table historically — code lives on `clients.reseller_code`.
- New column: `clients.reseller_code_active` (`'0'|'1'`, default active).
- Admin menu: **Reseller → Reseller Codes** (`/admin/reseller-codes`)
- Activate / Deactivate actions; show owning client.
- **Only active codes** apply in purchase, registration, lookups (`Client::findByResellerCode()`).

Files:
- `app/Filament/Resources/ResellerCodes/*`
- Migration: `database/migrations/2026_07_28_120000_add_reseller_code_active_to_clients_table.php`
- Enforcement: `PurchaseCodes`, `Register`, `CodePurchaseService`, `CodePurchaseResellerDetails`, `Client` model

### Form Builder
- Legacy iframe embed in portal profile editor.
- Services: `FormBuilderService` (includes `clearFormForCreate()`).
- Create must start with blank Form Builder (scrub questions on create mount).

### Label orders
- `LabelOrderService` must set legacy NOT NULL fields (e.g. `transaction_id = 'After new order set'`).
- Page: `OrderLabel.php` — catch throwables, soft-fail QR issues.

---

## 4. Work timeline (high level)

### Earlier (admin + portal foundation)
- Admin UX: back button (internal pages only, preserve filters), sign-out in sidebar, theme green, dark/light popups.
- Client CRUD: registration date not future / not prefilled, placeholders, user listing refresh, add-user modal scroll behavior.
- Filters, date filters, save-failure handling for legacy NOT NULL columns.
- Form Builder rebuild toward Kohana parity (editor, library, email/CSV/print, submissions).
- Marketing/frontend pages (privacy/policy etc.) and code profile form templates.
- Live import verification / parity docs in earlier commits.

### Recent session work (Jul 28 focus)

#### A. Order labels crash
- Bug: toast “Error while loading page…” on `/portal/order-labels?profile=…`
- Cause: missing NOT NULL `transaction_id` (and related fields) on `orders`
- Fix: `LabelOrderService` + safer `OrderLabel` error handling

#### B. Create profile prefilled / Form Builder dirty
- Cause: open slots / session drafts reused with leftover content; FB questions not cleared (table name typo earlier)
- Fix: `ProfileDraftSlotService::resetContentForCreate`, `scrubIfPollutedCreateDraft`, `FormBuilderService::clearFormForCreate`
- Seed templates marked claimed so they are not reused as blank slots

#### C. Purchase codes — legacy flow + UI
- Rebuilt multi-step purchase (codes → billing → order summary → proceed/invoice)
- Pixel-oriented Blade UI matching old `profile/purchase`
- Reseller tab + pricing tiers
- Fixed `code_purchase_detail.amount` NOT NULL on proceed

#### D. Code Balance vs Master Code List clarity
- Explained: create from template activates slot; list shows claimed profiles
- Gap found: create was claiming on page load (balance −1 without save)

#### E. Renew must not “just activate”
- Old app: summary + invoice (no card payment; invoice in 14 days)
- Implemented `RenewCodeSummary` and wired Code Balance / Master List / Multiple Renewal to it

#### F. Create must not spend balance until Save
- `assignType` keeps `update_or_not = 0`
- Stop inventing `free_code` open slots when inventory empty
- Master list filters `claimedSlot()` so unsaved drafts stay on Code Balance only

#### G. Location create/edit parity review
- Mostly wired (Words, assets, FB, QR, preview, slot binding)
- Remaining gaps noted (see §6)

#### H. Admin Reseller Codes listing
- Separate sidebar group, activate/deactivate, client owner column
- Active-only enforcement app-wide

#### I. Commit
- `6751a86` — useful app/views/migration/tests only  
- Left untracked: browser/debug/probe scripts under `scripts/`

---

## 5. Key files map (for next AI)

### Portal pages
- `app/Filament/Portal/Pages/CodeBalance.php`
- `app/Filament/Portal/Pages/PurchaseCodes.php`
- `app/Filament/Portal/Pages/PurchaseBilling.php`
- `app/Filament/Portal/Pages/PurchaseOrderSummary.php`
- `app/Filament/Portal/Pages/RenewCodeSummary.php`
- `app/Filament/Portal/Pages/OrderLabel.php`
- `app/Filament/Portal/Pages/ScanAnalytics.php`
- `app/Filament/Portal/Pages/CumulativeAnalytics.php`
- `app/Filament/Portal/Pages/ManageParticipants.php`

### Profile editor
- `app/Filament/Portal/Resources/Profiles/Pages/CreateProfile.php`
- `.../EditProfile.php`, `ListProfiles.php`
- `.../Concerns/HasLegacyProfileEditorLayout.php`
- `.../Concerns/HasLegacyFormBuilderSidebar.php`
- `.../Schemas/PortalProfileForm.php`
- `app/Filament/Resources/Profiles/Schemas/ProfileFormSchema.php` (type-specific Words fields)

### Services
- `ProfileDraftSlotService.php` — bind open slot; finalize on save
- `CodeProfileRenewalService.php` — quote + renew
- `CodePurchaseService.php`
- `FormBuilderService.php`
- `LabelOrderService.php`
- `CumulativeAnalyticsBuilder.php`
- `AnalyticsApiService.php`
- `PortalProfilePreview.php` (preview iframe `ask_for_location=no`)

### Admin
- `app/Filament/Resources/ResellerCodes/*`
- `app/Filament/Resources/Clients/*`
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/ClientPortalPanelProvider.php`

### Models / flags
- `profiles.update_or_not` — `'0'` open / `'1'` claimed (enum; use scopes `openSlot` / `claimedSlot`)
- `clients.reseller_code` + `reseller_code_active`
- Live MySQL often uses ENUM `'0'|'1'` — use `MysqlEnumBoolean` / `LegacyZeroOne` casts carefully

---

## 6. Known gaps / next candidates

### Location (and likely other types) create/edit
1. **`analytic_key` not set on first create** (legacy Galatech `item/addurl`) → new codes may lack Scanalytics key  
2. **Expired edit not blocked** (legacy shows message-only)  
3. Create redirect: legacy → type list; new → edit page (acceptable but not identical)  
4. Upload UX differs (Filament vs legacy iframes) — functional mostly OK  

### General
- Many one-off scripts under `scripts/` are local probes — do not commit unless asked  
- Full migrate of older pending migrations may fail on live import DBs (column already exists); prefer `--path=` for new additive migrations  
- Unit tests using RefreshDatabase sometimes crash in Docker env (“Premature end of PHP process”) — verify carefully  

### Product intent reminders from user
- No gap between old and new purchase/renew flows  
- Code balance must not decrease until profile is saved  
- Reseller codes inactive ⇒ not usable anywhere  

---

## 7. How to run locally

```bash
# From repo root
docker compose ps
docker compose exec -T app php artisan migrate --path=database/migrations/YYYY_MM_DD_....php
# App: http://localhost:8000/portal
# Admin: http://localhost:8000/admin
# Mailpit: http://localhost:8025
```

PHP is inside Docker (`docker compose exec -T app php ...`), not necessarily on host PATH (Windows).

---

## 8. Prompt starter for the next AI

Copy/paste:

```
You are continuing ScanLink Laravel migration (scanlink-laravel) from legacy Kohana (E:\abhishek-project\scanlink).

Branch: feature/client-portal-migration. Read PROJECT_HISTORY.md first.

Hard rules: never wipe Docker MySQL live import; match legacy behavior by reading old code; portal at /portal, admin at /admin.

Recent focus done: purchase multi-step, renew summary+invoice, create-slot does not claim until save, admin reseller codes activate/deactivate with active-only enforcement.

Next: continue legacy parity. Start by inspecting legacy then Laravel for the requested feature. Prefer additive migrations.
```

---

## 9. Useful legacy URLs / creds used in testing

- Old Code Balance: https://scanlink.com.au/profile/codeBalance  
- Old Purchase: https://scanlink.com.au/profile/purchase  
- Test account used: `anshgeex@gmail.com` / `12345678`  

---

## 10. Commit note

Useful work bundled in `6751a86` (71 files). Untracked junk intentionally left out (`scripts/browser-*`, `debug-*`, `probe-*`, etc.).
