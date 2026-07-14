# Sync Docker scanlink_laravel → WAMP/XAMPP MySQL on host :3306 (phpMyAdmin)
# Usage: .\scripts\sync-docker-to-wamp.ps1
#        .\scripts\sync-docker-to-wamp.ps1 -SkipAnalytics

param(
    [string]$HostMysql = "host.docker.internal",
    [int]$HostPort = 3306,
    [string]$HostUser = "root",
    [string]$HostPassword = "",
    [string]$Database = "scanlink_laravel",
    [switch]$SkipAnalytics
)

$ErrorActionPreference = "Stop"

$pwArgs = @()
if ($HostPassword -ne "") { $pwArgs = @("-p$HostPassword") }

Write-Host "Checking host MySQL $HostMysql`:$HostPort ..." -ForegroundColor Cyan
$check = docker exec scanlink-laravel-mysql mysql -h $HostMysql -P $HostPort -u $HostUser @pwArgs -N -e "SELECT @@innodb_force_recovery"
if ($LASTEXITCODE -ne 0) { throw "Cannot reach host MySQL. Is WAMP MySQL running?" }

$recovery = [int]("$check".Trim())
if ($recovery -gt 0) {
    Write-Host "Host MySQL is read-only (innodb_force_recovery=$recovery)." -ForegroundColor Yellow
    Write-Host "Use WAMP phpMyAdmin → server 'Docker ScanLink (3307)' (scanlink/scanlink) instead." -ForegroundColor Yellow
    Write-Host "Or set innodb_force_recovery=0 in my.ini, restart wampmysqld64, then re-run this script."
    exit 2
}

$ignore = @()
if ($SkipAnalytics) {
    $ignore = @(
        "--ignore-table=$Database.ana_item_analytics",
        "--ignore-table=$Database.form_builder_answers",
        "--ignore-table=$Database.analytics_datafirst",
        "--ignore-table=$Database.analytics_datafirstsecond"
    )
}

Write-Host "Dumping Docker DB..." -ForegroundColor Cyan
docker exec scanlink-laravel-mysql mysqldump -uscanlink -pscanlink `
    --single-transaction --quick --routines --triggers --hex-blob `
    --column-statistics=0 --set-gtid-purged=OFF --no-tablespaces `
    @ignore $Database -r /tmp/sync_to_wamp.sql
if ($LASTEXITCODE -ne 0) { throw "Dump failed" }

Write-Host "Recreating host database `$Database`..." -ForegroundColor Cyan
docker exec scanlink-laravel-mysql mysql -h $HostMysql -P $HostPort -u $HostUser @pwArgs -e `
    "DROP DATABASE IF EXISTS ``$Database``; CREATE DATABASE ``$Database`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

Write-Host "Raising host packet limits (best effort)..." -ForegroundColor Cyan
docker exec scanlink-laravel-mysql mysql -h $HostMysql -P $HostPort -u $HostUser @pwArgs -e `
    "SET GLOBAL max_allowed_packet=1073741824; SET GLOBAL net_read_timeout=600; SET GLOBAL net_write_timeout=600;" 2>$null

Write-Host "Importing into host phpMyAdmin MySQL (may take several minutes)..." -ForegroundColor Cyan
docker exec scanlink-laravel-mysql mysql -h $HostMysql -P $HostPort -u $HostUser @pwArgs $Database -e `
    "SET FOREIGN_KEY_CHECKS=0; SET UNIQUE_CHECKS=0; SET AUTOCOMMIT=0; source /tmp/sync_to_wamp.sql; COMMIT; SET FOREIGN_KEY_CHECKS=1; SET UNIQUE_CHECKS=1;"
if ($LASTEXITCODE -ne 0) { throw "Import failed — try -SkipAnalytics or use Docker ScanLink (3307) in phpMyAdmin" }

docker exec scanlink-laravel-mysql rm -f /tmp/sync_to_wamp.sql
Write-Host "Done. Open WAMP phpMyAdmin → MySQL → $Database" -ForegroundColor Green
