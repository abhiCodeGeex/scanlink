# Admin Two-Factor Authentication & Brand Theme

## Two-factor authentication (app authenticator)

Filament built-in **App Authentication** (TOTP) is enabled for `/admin`.

### How admins enable 2FA

1. Sign in at `http://localhost:8000/admin/login`
2. Open the user menu (avatar) → **Profile**
3. Find **App authentication** / two-factor section
4. Click **Set up** — scan the QR code with Google Authenticator, Authy, 1Password, etc.
5. Enter the 6-digit code to confirm
6. **Save the recovery codes** shown (one-time use if you lose the device)

### After 2FA is enabled

- On next login: email/password → then enter the authenticator code (or a recovery code)
- Disable or regenerate recovery codes from the same Profile page

### Technical notes

- Columns: `users.app_authentication_secret`, `users.app_authentication_recovery_codes` (encrypted)
- Packages: `pragmarx/google2fa-qrcode`, `bacon/bacon-qr-code`
- 2FA is **optional** (not forced for every admin). To require it for all users, set the second argument of `multiFactorAuthentication(..., isRequired: true)` in `AdminPanelProvider`.

## Brand theme

| Mode | Behaviour |
|------|-----------|
| **Light** | Header (topbar) and sidebar nav use ScanLink green `#008C00`. Primary buttons use the same green. |
| **Dark** | Topbar/sidebar banner colours stay Filament default dark. Primary accent + logo still apply. |

Logo: `public/images/scanlink-logo.png` (legacy ScanLink mark with signal icon). Shown in sidebar, topbar, and login with a white pill for contrast.

CSS: `public/css/filament/scanlink-theme.css`
