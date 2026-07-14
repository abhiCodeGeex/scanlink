# Import live RDS → Docker local, then post-process (+ optional host phpMyAdmin sync)
# Usage (from project root):
#   .\scripts\import-live-to-local.ps1
#   .\scripts\import-live-to-local.ps1 -SyncPhpMyAdmin
#   .\scripts\import-live-to-local.ps1 -SyncPhpMyAdmin -SkipAnalytics

param(
    [string]$LiveHost = $env:LIVE_DB_HOST,
    [string]$LivePort = $(if ($env:LIVE_DB_PORT) { $env:LIVE_DB_PORT } else { "3306" }),
    [string]$LiveDatabase = $(if ($env:LIVE_DB_DATABASE) { $env:LIVE_DB_DATABASE } else { "scanlink_development" }),
    [string]$LiveUsername = $env:LIVE_DB_USERNAME,
    [string]$LivePassword = $env:LIVE_DB_PASSWORD,
    [switch]$SyncPhpMyAdmin,
    [switch]$SkipAnalytics
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not $LiveHost -or -not $LiveUsername -or -not $LivePassword) {
    Write-Host "Set LIVE_DB_HOST, LIVE_DB_USERNAME, LIVE_DB_PASSWORD (and optional LIVE_DB_DATABASE)." -ForegroundColor Yellow
    Write-Host "Example:"
    Write-Host '  $env:LIVE_DB_HOST="galadb1....rds.amazonaws.com"'
    Write-Host '  $env:LIVE_DB_USERNAME="..."'
    Write-Host '  $env:LIVE_DB_PASSWORD="..."'
    Write-Host '  .\scripts\import-live-to-local.ps1 -SyncPhpMyAdmin'
    exit 1
}

Write-Host "==> Ensuring Docker MySQL is up..." -ForegroundColor Cyan
docker compose up -d mysql redis mailpit phpmyadmin app | Out-Host

Write-Host "==> Dumping LIVE DB (read-only) into Docker..." -ForegroundColor Cyan
$ignore = @(
    "--ignore-table=$LiveDatabase.testing_clients",
    "--ignore-table=$LiveDatabase.testing_form_builder_answers",
    "--ignore-table=$LiveDatabase.testing_form_builder_question",
    "--ignore-table=$LiveDatabase.testing_profiles"
)

docker exec scanlink-laravel-mysql mysqldump `
    -h $LiveHost -P $LivePort -u $LiveUsername "-p$LivePassword" `
    --single-transaction --quick --lock-tables=false --no-tablespaces `
    --skip-triggers --hex-blob --set-gtid-purged=OFF --column-statistics=0 `
    @ignore $LiveDatabase -r /tmp/live_import.sql

if ($LASTEXITCODE -ne 0) { throw "Live mysqldump failed" }

Write-Host "==> Recreating local Docker database..." -ForegroundColor Cyan
docker exec scanlink-laravel-mysql mysql -uroot -proot -e "DROP DATABASE IF EXISTS scanlink_laravel; CREATE DATABASE scanlink_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON scanlink_laravel.* TO 'scanlink'@'%'; FLUSH PRIVILEGES;"

Write-Host "==> Importing into Docker scanlink_laravel..." -ForegroundColor Cyan
docker exec scanlink-laravel-mysql mysql -uroot -proot scanlink_laravel -e "SET FOREIGN_KEY_CHECKS=0; SET UNIQUE_CHECKS=0; SET AUTOCOMMIT=0; source /tmp/live_import.sql; COMMIT; SET FOREIGN_KEY_CHECKS=1; SET UNIQUE_CHECKS=1;"
if ($LASTEXITCODE -ne 0) { throw "Docker import failed" }

docker exec scanlink-laravel-mysql rm -f /tmp/live_import.sql

Write-Host "==> Post-process (adapt + user sync)..." -ForegroundColor Cyan
$postArgs = @("compose", "exec", "-T", "app", "php", "artisan", "scanlink:import-postprocess", "--force")
if ($SyncPhpMyAdmin) {
    $postArgs += "--sync-phpmyadmin"
    if ($SkipAnalytics) { $postArgs += "--skip-analytics" }
}
docker @postArgs
if ($LASTEXITCODE -ne 0) { throw "Post-process failed" }

Write-Host ""
Write-Host "Done." -ForegroundColor Green
Write-Host "  App:              http://localhost:8000/admin"
Write-Host "  Docker phpMyAdmin: http://localhost:8080  (scanlink / scanlink)"
Write-Host "  WAMP phpMyAdmin:   choose server 'Docker ScanLink (3307)'"
if ($SyncPhpMyAdmin) {
    Write-Host "  Host copy:         MySQL :3306 database scanlink_laravel (if sync succeeded)"
}
