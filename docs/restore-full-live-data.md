# Restore full live client list (local)

## Why Laravel showed only 1 client

We previously ran `scanlink:keep-one-dummy`, which **intentionally deleted** all but one row per table for a light test DB. That is why Manage Client showed only **Grant Ford**.

Live siteadmin (`beta.scanlink.com.au`) still has the full list — that was never wiped.

### Auto sync after live → Docker import

After importing into Docker, run post-process with host phpMyAdmin sync:

```powershell
docker compose exec -T app php artisan scanlink:import-postprocess --force --sync-phpmyadmin --skip-analytics
```

Or one-shot from live RDS (set `LIVE_DB_*` env vars first):

```powershell
.\scripts\import-live-to-local.ps1 -SyncPhpMyAdmin -SkipAnalytics
```

Copy Docker → WAMP only:

```powershell
.\scripts\sync-docker-to-wamp.ps1 -SkipAnalytics
```

> If WAMP MySQL is read-only (`innodb_force_recovery > 0`) or crashes on large imports, use phpMyAdmin server **Docker ScanLink (3307)** instead — that always shows the live Docker data with no copy.

## Docker MySQL vs phpMyAdmin on 3306

| | Laravel Docker | XAMPP phpMyAdmin (default) |
|--|--|--|
| Port | **3307** | **3306** |
| Database | `scanlink_laravel` | (your XAMPP DBs) |

Port **3306 is already used by XAMPP**, so Docker cannot bind to it. To see Laravel data in XAMPP phpMyAdmin, either:

### A) Point phpMyAdmin at Docker (recommended)

In `C:\xampp\phpMyAdmin\config.inc.php` add:

```php
$i++;
$cfg['Servers'][$i]['verbose'] = 'ScanLink Laravel (Docker 3307)';
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['port'] = '3307';
$cfg['Servers'][$i]['auth_type'] = 'cookie';
```

Login: `scanlink` / `scanlink` (or `root` / `root`).

### B) Import dump into XAMPP MySQL on 3306

```bat
cd C:\xampp\mysql\bin
mysql.exe -uroot -e "CREATE DATABASE IF NOT EXISTS scanlink_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql.exe -uroot scanlink_laravel < D:\projects\product\scanlink-laravel\storage\app\migration\scanlink-live.sql
```

Then open phpMyAdmin → database `scanlink_laravel`.

## Re-import FULL data into Laravel Docker (no trim)

Run these in PowerShell from `D:\projects\product\scanlink-laravel`:

```powershell
docker compose up -d mysql app
docker compose exec -T mysql mysql -uroot -proot -e "DROP DATABASE IF EXISTS scanlink_laravel; CREATE DATABASE scanlink_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON scanlink_laravel.* TO 'scanlink'@'%'; FLUSH PRIVILEGES;"

docker cp storage/app/migration/scanlink-live.sql scanlink-laravel-mysql:/tmp/scanlink-live.sql
docker compose exec -T mysql sh -c "mysql -uscanlink -pscanlink scanlink_laravel < /tmp/scanlink-live.sql"

docker compose exec -T app php artisan scanlink:adapt-live-import --force
docker compose exec -T app php artisan scanlink:import-postprocess --force --sync-phpmyadmin --skip-analytics
docker compose exec -T app php artisan scanlink:verify-import
```

Or use the helper script (dumps live RDS → Docker → post-process):

```powershell
$env:LIVE_DB_HOST="..."
$env:LIVE_DB_USERNAME="..."
$env:LIVE_DB_PASSWORD="..."
.\scripts\import-live-to-local.ps1 -SyncPhpMyAdmin -SkipAnalytics
```

**Do not** run `scanlink:keep-one-dummy` if you want all clients.

Expected verify counts (approx): clients ~459, profiles ~6062, client_users ~505.

Admin login: `admin@scanlink.com` / `Admin@12345`

If import exits with code **137**, Docker ran out of memory — raise Docker Desktop memory to 6–8 GB and retry.
