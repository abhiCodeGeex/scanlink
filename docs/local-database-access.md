# Local database access (why phpMyAdmin may not show ScanLink)

## Why you do not see it in default phpMyAdmin

Laravel MySQL runs **inside Docker**, not in XAMPP’s MySQL.

| | XAMPP MySQL (default phpMyAdmin) | ScanLink Laravel Docker |
|--|--|--|
| Host | `127.0.0.1` | `127.0.0.1` |
| Port | **3306** | **3307** |
| Database | (your XAMPP DBs) | **`scanlink_laravel`** |
| User | `root` (often empty password) | `scanlink` / `scanlink` (or root/`root`) |

Default XAMPP phpMyAdmin only talks to port **3306**, so it never lists `scanlink_laravel`.

## How to open the Laravel DB in phpMyAdmin (XAMPP)

1. Edit XAMPP phpMyAdmin config, usually:
   `C:\xampp\phpMyAdmin\config.inc.php`
2. Add a second server (keep existing `$i` block, then increment `$i`):

```php
$i++;
$cfg['Servers'][$i]['verbose'] = 'ScanLink Laravel Docker';
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['port'] = '3307';
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['user'] = 'scanlink';
$cfg['Servers'][$i]['password'] = 'scanlink';
$cfg['Servers'][$i]['AllowNoPassword'] = false;
```

3. Restart Apache, open phpMyAdmin, pick server **ScanLink Laravel Docker** from the top dropdown.
4. Log in with user `scanlink` / password `scanlink` (or `root` / `root`).

## Optional: Docker phpMyAdmin

`docker-compose.yml` includes a `phpmyadmin` service on http://localhost:8080  
(If Docker Desktop hits an I/O error pulling the image, use the XAMPP method above instead.)

```bash
docker compose up -d phpmyadmin
# then open http://localhost:8080
```

## Live data import → one dummy row each

1. Export live DB (set env vars, then run):

```powershell
$env:LIVE_DB_HOST="..."
$env:LIVE_DB_USER="..."
$env:LIVE_DB_PASS="..."
$env:LIVE_DB_NAME="scanlink"
.\scripts\export-live-db.ps1
```

2. Import into Docker MySQL:

```powershell
.\scripts\import-local-db.ps1
```

3. Post-process + trim:

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan scanlink:import-postprocess --default-password="Admin@12345"
docker compose exec app php artisan scanlink:keep-one-dummy --force
```

Admin login after sync: `admin@scanlink.com` / `Admin@12345` (or synced legacy admin emails).
