# YouTube upload setup (admin profile videos)

Legacy ScanLink used **YouTube Data API v2 + ClientLogin** (username/password). Google has removed that flow. The Laravel admin uses **YouTube Data API v3 + OAuth 2.0** instead.

## Keys to configure

| Key | Where to store | Required for |
|-----|----------------|--------------|
| `youtube_client_id` | Global Settings **or** `.env` `YOUTUBE_CLIENT_ID` | OAuth + uploads |
| `youtube_client_secret` | Global Settings **or** `.env` `YOUTUBE_CLIENT_SECRET` | OAuth + uploads |
| `youtube_refresh_token` | Global Settings **or** `.env` `YOUTUBE_REFRESH_TOKEN` | Uploading new videos |
| `youtube_developer_key` | Global Settings **or** `.env` `YOUTUBE_DEVELOPER_KEY` | Optional API quota helper |
| `youtube_application_id` | Global Settings | Legacy label only (optional) |
| `youtube_username` / `youtube_password` | Global Settings | **Legacy only — not used for upload** |

Optional `.env` entries (override settings when set):

```env
YOUTUBE_CLIENT_ID=
YOUTUBE_CLIENT_SECRET=
YOUTUBE_REFRESH_TOKEN=
YOUTUBE_DEVELOPER_KEY=
```

## Step 1 — Google Cloud project

1. Open [Google Cloud Console](https://console.cloud.google.com/).
2. Create or select a project.
3. Enable **YouTube Data API v3** (APIs & Services → Library).

## Step 2 — OAuth consent screen

1. APIs & Services → **OAuth consent screen**.
2. Choose **External** (or Internal if Workspace).
3. Add app name, support email, developer contact.
4. Add scope: `https://www.googleapis.com/auth/youtube.upload`.
5. Add your Google account as a **test user** while the app is in Testing mode.

## Step 3 — OAuth client credentials

1. APIs & Services → **Credentials** → **Create credentials** → **OAuth client ID**.
2. Application type: **Web application**.
3. Authorized redirect URI (must match your app URL):

   ```
   http://localhost:8000/oauth/youtube/callback
   ```

   For production, also add:

   ```
   https://your-domain.com/oauth/youtube/callback
   ```

4. Copy **Client ID** and **Client secret**.

## Step 4 — Save credentials in admin

1. Log in to `/admin` as super admin.
2. Open **Global Settings**.
3. Paste:
   - `youtube_client_id`
   - `youtube_client_secret`
   - `youtube_developer_key` (from Credentials → API key, optional)
4. Save.

## Step 5 — Generate refresh token

Inside Docker:

```bash
docker compose exec app php artisan youtube:authorize
```

1. Open the URL printed in the terminal.
2. Sign in with the **YouTube channel** account that should own uploaded videos.
3. Approve access — browser redirects to `/oauth/youtube/callback` and stores `youtube_refresh_token`.

Alternative (manual code):

```bash
docker compose exec app php artisan youtube:authorize "PASTE_CODE_FROM_REDIRECT_URL"
```

## Step 6 — Use in admin

1. Edit any profile → **Videos** tab.
2. Actions available (legacy parity):
   - **Add YouTube link** — paste watch URL or 11-char ID
   - **Select existing video** — pick from client library
   - **Upload to YouTube** — appears when OAuth is configured
3. **Delete** removes the DB row and deletes from YouTube only when no other profile shares the same video ID (legacy rule).

## Troubleshooting

| Issue | Fix |
|-------|-----|
| Upload button hidden | Set client id, secret, and refresh token |
| `redirect_uri_mismatch` | Add exact callback URL in Google OAuth client |
| No `refresh_token` returned | Revoke app access at [Google Account permissions](https://myaccount.google.com/permissions) and authorize again |
| Upload fails with 403 | Channel not verified or quota exceeded — check YouTube Studio |

## Admin roles

| Role | Access |
|------|--------|
| Super admin | Full access including Global Settings |
| Admin | Create/edit/delete clients, profiles, orders |
| Support | View-only (lists + detail pages, no create/edit/delete) |
