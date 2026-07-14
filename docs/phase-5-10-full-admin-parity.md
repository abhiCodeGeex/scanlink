# Phases 5–10 — Full Admin Parity

This phase closes the gaps identified after Phase 4: client/user flows, all order types,
profiles/product system, settings/CMS, and legacy navigation shell.

## Navigation structure (matches legacy menu)

| Group | Items |
|-------|-------|
| **Admin Home** | Admin Control Panel tiles |
| **Website** | Manage Client, Manage Profiles(Product), Manage Testimonial, Manage Gallery |
| **Order** | Manage Order, Manage Code Orders, Manage Form Builder Orders |
| **Settings** | Global Settings, Code Pricing, Reseller Pricing |
| **Clients** | Sub Divide Client |
| **Top** | Change Password |

## Client & user parity

- **Create client** — Add Client + Add User on same screen (legacy field names)
- **Manage Client** list — filter on client name, reseller badge, block/delete
- **Manage User** — dedicated page per client at `/admin/clients/{id}/users`
- **Inline expiry** — editable Expires column with renewal email via `UserRenewalService`
- **Sub Divide Client** — 4-step wizard (`SubdivideClient` page)
- **Edit client** — reseller name display, reseller code, free codes

## All order types

| Legacy module | Laravel resource | Status values |
|---------------|------------------|---------------|
| `codeorder` | `CodePurchaseResource` | 0–4 integers |
| `order` | `OrderResource` | New, Paid, Shipped, Completed, Cancelled |
| `formbuilderorder` | `FormBuilderOrderResource` | 0–4 integers |

All three: list + view + change-status action; create disabled.

## Profiles / product system

- **Manage Profiles(Product)** — legacy list columns + name filter
- **Add Profile** — enabled; type + client picker; type-specific fields via `ProfileFormSchema`
- **9 equipment types** — plant, location, asset, product, procedure, people, misc, customqr, code
- **Extended profile fields** — data collection, code type (QR/Data Matrix), security toggles
- **Relation data** — weblinks, contacts, checklist items (plant), media upload fields
- **Media tables** — logo, picture, documents, video, weblink, profile_contact, checklist_item

## Settings & CMS

- **Global Settings** — legacy key/value fields (PayPal, YouTube, contact email)
- **Code Pricing** — `code_prising` retail + reseller tiers
- **Reseller Pricing** — `reseller_pricing` tiers
- **Testimonials** — CRUD with video embed
- **Gallery** — upload, approve toggle, delete

## Database additions

New migrations under `database/migrations/2026_07_08_*`:
- Profile extended columns
- `orders`, `form_builder_orders`, `form_builder_order_detail`
- `settings`, `code_prising`, `reseller_pricing`
- `testimonial`, `gallery`
- Profile media tables + `code_purchase_detail.amount`

## Seeding

`Phase5Seeder` adds sample settings, pricing tiers, physical + form-builder orders, testimonial, gallery.

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan test
```

## Remaining for live cutover (not admin UI)

These exist in the legacy **portal** / infrastructure layer, not siteadmin CRUD:

- Public / portal profile rendering (scan destination pages)
- Form builder **editor** (questions/answers) — separate from form-builder **orders**
- Live DB import from production (`admin` → `users` mapping)
- Portal-facing client login (not admin email login)
- YouTube OAuth live credentials (wiring exists; secrets are env/config)

Admin siteadmin parity is complete — see `docs/admin-portal-complete.md`. Portal migration is the next track.
