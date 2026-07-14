# DB Migration Runbook (Live ScanLink DB -> Laravel Local/Staging)

## 1) Preconditions
- Obtain read-only credentials for live DB.
- Confirm database version and character set.
- Ensure local MySQL is empty for first import.

## 2) Export from live DB (read-only)
- Use `mysqldump` with consistent options:
  - `--single-transaction`
  - `--routines --triggers --events` (if used)
  - `--default-character-set=utf8mb4`

## 3) Sanitize dump for non-production usage
- Remove or mask sensitive user fields:
  - passwords (or replace with known hash)
  - personally identifiable information where required
  - API keys/secrets/tokens

## 4) Import into local/staging
- Create target DB.
- Import dump.
- Run Laravel baseline migrations for additive structures only.

## 5) Verification checks
- Row-count validation for critical tables:
  - users
  - profiles
  - form_builder_question
  - form_builder_answers
  - orders/payments tables
- FK consistency checks.
- Spot-check role mappings and sample admin screens.

## 6) Delta sync (pre-cutover rehearsal)
- Take a fresh export.
- Re-import into staging.
- Re-run verification scripts.

## 7) Cutover readiness criteria
- No schema mismatch errors in Laravel logs.
- Admin parity checks passed.
- Rollback instructions documented and rehearsed.
