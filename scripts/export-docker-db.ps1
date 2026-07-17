# Export the local Docker MySQL database to a portable backup file.
# Usage (from project root):
#   .\scripts\export-docker-db.ps1
#   .\scripts\export-docker-db.ps1 -Output ".\storage\app\backups\my-backup.sql.gz"

param(
    [string]$Output = "",
    [string]$Database = "scanlink_laravel",
    [string]$Container = "scanlink-laravel-mysql"
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not $Output) {
    $stamp = Get-Date -Format "yyyyMMdd-HHmmss"
    $Output = ".\storage\app\backups\scanlink-docker-full-$stamp.sql.gz"
}

$outDir = Split-Path $Output -Parent
if ($outDir) {
    New-Item -ItemType Directory -Force -Path $outDir | Out-Null
}

$absOutput = (Resolve-Path $outDir).Path + "\" + (Split-Path $Output -Leaf)
$tmpSql = "/tmp/scanlink-docker-export.sql"
$tmpGz = "/tmp/scanlink-docker-export.sql.gz"

Write-Host "Checking Docker MySQL container..." -ForegroundColor Cyan
$running = docker ps --filter "name=$Container" --filter "status=running" --format "{{.Names}}"
if (-not $running) {
    Write-Host "Starting Docker stack..." -ForegroundColor Yellow
    docker compose up -d mysql | Out-Host
    $attempts = 0
    while ($attempts -lt 60) {
        $health = docker inspect --format "{{if .State.Health}}{{.State.Health.Status}}{{else}}none{{end}}" $Container 2>$null
        if ($health -eq "healthy" -or $health -eq "none") { break }
        Start-Sleep -Seconds 2
        $attempts++
    }
}

Write-Host "Dumping database '$Database' from $Container ..." -ForegroundColor Cyan
docker exec $Container mysqldump `
    -uroot -proot `
    --single-transaction `
    --quick `
    --lock-tables=false `
    --no-tablespaces `
    --routines `
    --triggers `
    --hex-blob `
    --set-gtid-purged=OFF `
    --column-statistics=0 `
    --default-character-set=utf8mb4 `
    $Database -r $tmpSql

if ($LASTEXITCODE -ne 0) {
    throw "mysqldump failed"
}

docker exec $Container sh -c "gzip -c $tmpSql > $tmpGz"
if ($LASTEXITCODE -ne 0) {
    throw "gzip failed"
}

docker cp "${Container}:$tmpGz" $absOutput
docker exec $Container rm -f $tmpSql $tmpGz

Write-Host ""
Write-Host "Backup created:" -ForegroundColor Green
Get-Item $absOutput | Format-List FullName, Length, LastWriteTime
Write-Host "Copy this file with the project to restore on another PC:"
Write-Host "  .\scripts\restore-docker-db.ps1 -Input `"$absOutput`""
