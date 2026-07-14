# Deep E2E / Regression Report (Phases 0–4+)

Date: 2026-07-13  
Target: `http://localhost:8000/admin` (local Docker DB, **no DB wipe**)  
Seeded counts at start: users=1, clients=2, profiles=3, orders=1, codes=2, form-builder=1, gallery=1, testimonial=1

## Automated suite

```bash
docker compose exec -T app php artisan test
```

**Result: all green** (includes new `tests/Feature/Admin/E2E/FullAdminRegressionTest.php`)

Coverage in that suite:
- Auth pages + guest redirects (login/register/forgot/reset-without-token)
- Every core admin page Livewire load (home, clients, profiles, 3 order types, CMS, settings, subdivide, change password)
- Client create required validation + invalid URL regex + create with optional Add User
- Profile create validation, view, archive (soft delete) + list hiding
- Code/physical order list/view + change status; form-builder view; create routes 404
- Testimonial/gallery required validation; Global Settings rejects placeholder YouTube client id
- Support role cannot access Global Settings / cannot create clients; can view profiles

## Browser crawl (Playwright)

| Area | Result |
|------|--------|
| Login (admin@scanlink.com) | Pass |
| Admin Home tiles (12) + aria-labels | Pass (labels fixed) |
| Sidebar groups: Website / Order / Clients / Settings | Pass |
| Clients list / create / edit | Pass — create succeeded (E2E Client …) |
| Client edit: reseller/free codes/Users/Code profiles tabs | Pass (UI present) |
| Manage User page | Pass (heading + Add User button) |
| Profiles list / create / view / edit | Pass |
| Profile edit media sections (logo/pictures/docs/videos) | Pass |
| Code orders list/view | Pass |
| Physical orders list/view + postage $2.95 | Pass |
| Form builder orders list/view | Pass |
| Testimonials / Galleries create pages | Pass |
| Subdivide / Global Settings / Code Pricing / Reseller Pricing | Pass |
| Change Password page | Pass |
| Logout → guest redirect to login | Pass |
| Register page (guest) | Pass — “Sign up” |
| Forgot password page | Pass |
| Password reset without token | Pass — 403 |
| Nav feedback loader element present | Pass |
| No Whoops/500 on any crawled route | Pass |

## Bugs found and fixed during this pass

1. **Home tiles lacked accessible names** — added `aria-label` on each tile link.
2. **Browser autofill polluted Create Client** (admin email/password into client + Add User fields) — set `autocomplete="off"` / `new-password` on those inputs.
3. **Nav loader fired on every button** (including Create), making UI feel stuck — loader now only starts for real in-app navigation links.
4. **URL character validation** — confirmed via Livewire test (`bad url!!` rejected).

## Residual notes (not blockers for admin CRUD)

| Item | Notes |
|------|--------|
| Playwright `fill()` vs Livewire | Empty-submit error messages sometimes not visible unless values are typed slowly; **Livewire tests prove validation works**. |
| Manage Users “Add User” table action | Hard to drive via Livewire test API on Filament 5 ManageRelatedRecords; **create-with-optional-user path is covered**, page loads and lists users. |
| YouTube file upload | Needs real Google OAuth client id/secret/refresh token (see `docs/youtube-setup.md`). Linking by URL works without OAuth. |
| Invalid login toast | Filament may show notification vs field error; guest stays on login (verified). |

## How to re-run

```bash
# Full automated regression
docker compose exec -T app php artisan test

# E2E subset only
docker compose exec -T app php artisan test tests/Feature/Admin/E2E
```

Manual smoke: open `/admin/login` → sign in → click each Admin Home tile → create client with empty fields (expect errors) → create with good data → open Users / Profiles / each order type → Sign out.
