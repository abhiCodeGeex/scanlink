# Phase 4 - Legacy Parity Alignment

Goal: make the migrated admin modules a like-for-like copy of the legacy Kohana
`siteadmin` flow. Only the technology (Laravel + Filament) and the storage
engine change - fields, labels, statuses, and workflows match the old system.

## Code Orders (legacy `codeorder`)

### Status values (breaking change vs. earlier phases)
Legacy stores order status as an integer. This is now enforced end-to-end via
`App\Enums\CodeOrderStatus`:

| Value | Label | Legacy meaning |
|---|---|---|
| 0 | New | Freshly created order |
| 1 | Renew | Renewal order |
| 2 | Invoice Send | Invoice emailed |
| 3 | Paid | Payment received |
| 4 | Cancelled | Cancelled order |

- `code_purchase.status` migrated from `string` to `unsignedTinyInteger` (default `0`).
- Added `total_amount` and `transaction_id` columns to match legacy schema.

### List (`Manage Code Orders`)
Columns match the legacy list exactly:
`Code ID`, `Date` (`d/m/Y H:i:s`), `Full Name`, `Order Type` (status), `No. Codes`, plus a row `View` action.

Filter: **View Orders by Status** with options `New`, `Renew`, `Invoice Send`, `Paid`
(matches legacy - `Cancelled` is intentionally not a filter option).

### View page (legacy `codeorder/view`)
Read-only detail using an infolist: Order (status/id/date), Order Detail
(codes, price per code, grand total in AUD), Billing Detail, and Reseller Detail
(only shown when a reseller is linked).

**Change Order Status** header action mirrors the legacy dropdown, offering only
`New`, `Invoice Send`, `Paid`, with a confirmation step.

Create/Edit routes are disabled - legacy admins can only view and change status.

## Clients (legacy `client`)

### List (`Manage Client`)
Columns match legacy: `Client Name` (with a green **R** badge when a reseller
code is set), `Address`, `Telephone`, `Contact Person`, `Registration Date`,
`Email`, `URL`. Row options: `Users`, `Edit`, `Block`/`Unblock` (approve toggle),
`Delete` - each with the same confirmation prompts as legacy.

### Add / Edit form
Fields mirror legacy add/edit: Client Name, Address, Telephone, Contact Person,
Registration date, Email, Password, URL.
- **URL** is editable on add and **read-only on edit** (legacy behaviour).
- **Reseller Email** only appears on the edit screen.

### Edit-only actions (legacy separate forms)
- **Add Reseller code** - saves `reseller_code`, rejecting duplicates with the
  legacy "You entered duplicate reseller code." message.
- **Add free codes** - creates a free (`per_code_amount = 0`) `New` code order
  for the client, mirroring the legacy "Add Codes" form.

## Users (legacy `user` under a client)
Sub-users are managed on the client's **Users** tab, matching how legacy reaches
`user/index/{clientId}` from the client row.

Columns: `Email Address`, `Created`, `Expires` (highlighted red when past),
`Active`, `Video`. Actions:
- **Renew account** (+1 year) - shown when expiry is null or within 2 months (legacy rule).
- **Set expiry** - inline expiry change (legacy inline edit + save).
- **Video permission** toggle.
- **Block**/**Unblock** (status toggle), **Edit**, **Delete**.
Passwords only change when a new value is entered.

## Testing notes
- `code_purchase.status` is asserted to persist as legacy integers.
- Filament action-form fill in tests uses `->set('mountedActions.0.data.<field>', ...)`
  because the `fillForm`/`callAction(data:)` helper does not populate mounted
  action schemas reliably in this Filament build.

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan test
```

## Known remaining parity gaps (tracked for later phases)
- Standalone cross-client user index screen (currently client-scoped tab).
- Additional legacy modules not yet migrated (checklists, custom QR, form builder,
  reseller pricing screens, reports).
- Live-data import mapping for `admin` -> `users` and legacy status values.
