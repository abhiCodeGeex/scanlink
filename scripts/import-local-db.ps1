# Import SQL dump into Docker MySQL (port 3307).
# Usage:
#   .\scripts\import-local-db.ps1
#   .\scripts\import-local-db.ps1 -Input ".\storage\app\migration\scanlink-live.sql"

param(
    [string]$Input = ".\storage\app\migration\scanlink-live.sql",
    [string]$Database = "scanlink_laravel"
)

if (-not (Test-Path $Input)) {
    Write-Error "Dump not found: $Input"
    exit 1
}

Write-Host "Importing $Input into Docker MySQL database $Database ..."

Get-Content -Raw $Input | docker compose exec -T mysql mysql -uscanlink -pscanlink $Database

if ($LASTEXITCODE -ne 0) {
    Write-Error "Import failed"
    exit $LASTEXITCODE
}

Write-Host "Import finished. Run:"
Write-Host "  docker compose exec app php artisan migrate --force"
Write-Host "  docker compose exec app php artisan scanlink:import-postprocess --default-password=`"Admin@12345`""
Write-Host "  docker compose exec app php artisan scanlink:keep-one-dummy --force"
