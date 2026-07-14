# Admin-First Backlog (Phase 0 Output)

## Legacy to New Panel Mapping
- Legacy area: `siteadmin/`
- New area: `/admin` (Filament panel)

## Progressive module order
1. Admin authentication and session management
2. Roles and permissions
3. Client accounts
4. Code profiles (list, status, ownership)
5. Order/payment visibility and support tools
6. Settings and integration controls

## Safety acceptance per module
- Feature parity test passed.
- Role-based access verified.
- No destructive schema modifications.
- Error and audit logs visible for operations.

## Cutover strategy
- Run new admin in shadow mode while legacy admin stays active.
- Enable module by module using feature toggles.
- Keep rollback to legacy panel until UAT sign-off is complete.
