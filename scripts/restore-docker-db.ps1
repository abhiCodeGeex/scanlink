# Restore a Docker MySQL backup on a new machine (or reset local Docker DB).
# Usage (from project root):
#   .\scripts\restore-docker-db.ps1
#   .\scripts\restore-docker-db.ps1 -Input ".\storage\app\backups\scanlink-docker-full-20260717.sql.gz"

param(
    [string]$Input = ".\storage\app\backups\scanlink-docker-full-20260717.sql.gz",
    [string]$Database = "scanlink_laravel",
    [string]$Container = "scanlink-laravel-mysql",
    [switch]$SkipMigrate
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not (Test-Path $Input)) {
    Write-Error "Backup not found: $Input"
    exit 1
}

$absInput = (Resolve-Path $Input).Path
$tmpGz = "/tmp/scanlink-docker-restore.sql.gz"
$tmpSql = "/tmp/scanlink-docker-restore.sql"

Write-Host "==> Starting Docker stack..." -ForegroundColor Cyan
docker compose up -d mysql redis mailpit app nginx phpmyadmin | Out-Host

Write-Host "==> Waiting for MySQL..." -ForegroundColor Cyan
$attempts = 0
while ($attempts -lt 60) {
    $health = docker inspect --format "{{if .State.Health}}{{.State.Health.Status}}{{else}}none{{end}}" $Container 2>$null
    if ($health -eq "healthy" -or $health -eq "none") { break }
    Start-Sleep -Seconds 2
    $attempts++
}
if ($attempts -ge 60) {
    throw "MySQL did not become healthy in time"
}

Write-Host "==> Copying backup into container..." -ForegroundColor Cyan
docker cp $absInput "${Container}:$tmpGz"

Write-Host "==> Recreating database '$Database'..." -ForegroundColor Cyan
docker exec $Container mysql -uroot -proot -e @"
DROP DATABASE IF EXISTS $Database;
CREATE DATABASE $Database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON $Database.* TO 'scanlink'@'%';
FLUSH PRIVILEGES;
"@

Write-Host "==> Importing backup (may take several minutes)..." -ForegroundColor Cyan
if ($absInput -match '\.gz$') {
    docker exec $Container sh -c "gunzip -c $tmpGz > $tmpSql"
} else {
    docker exec $Container sh -c "cp $tmpGz $tmpSql"
}

docker exec $Container mysql -uroot -proot $Database -e "SET FOREIGN_KEY_CHECKS=0; SET UNIQUE_CHECKS=0; SET AUTOCOMMIT=0; source $tmpSql; COMMIT; SET FOREIGN_KEY_CHECKS=1; SET UNIQUE_CHECKS=1;"
if ($LASTEXITCODE -ne 0) {
    throw "Import failed"
}

docker exec $Container rm -f $tmpGz $tmpSql

if (-not $SkipMigrate) {
    Write-Host "==> Running additive Laravel migrations..." -ForegroundColor Cyan
    docker compose exec -T app php artisan migrate --force
    if ($LASTEXITCODE -ne 0) {
        throw "migrate failed"
    }
}

Write-Host "==> Verifying import..." -ForegroundColor Cyan
docker compose exec -T app php artisan scanlink:verify-import

Write-Host ""
Write-Host "Restore complete." -ForegroundColor Green
Write-Host "  App:        http://localhost:8000"
Write-Host "  Admin:      http://localhost:8000/admin"
Write-Host "  phpMyAdmin: http://localhost:8080  (user: scanlink / scanlink)"
Write-Host "  Login:      admin@scanlink.com / Admin@12345"
