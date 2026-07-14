# Export live ScanLink MySQL → local file (credentials via env, not committed).
# Usage (PowerShell):
#   $env:LIVE_DB_HOST="..."
#   $env:LIVE_DB_USER="..."
#   $env:LIVE_DB_PASS="..."
#   $env:LIVE_DB_NAME="scanlink"
#   .\scripts\export-live-db.ps1

param(
    [string]$Output = ".\storage\app\migration\scanlink-live.sql"
)

$HostName = $env:LIVE_DB_HOST
$User = $env:LIVE_DB_USER
$Pass = $env:LIVE_DB_PASS
$Database = if ($env:LIVE_DB_NAME) { $env:LIVE_DB_NAME } else { "scanlink" }

if (-not $HostName -or -not $User -or -not $Pass) {
    Write-Error "Set LIVE_DB_HOST, LIVE_DB_USER, LIVE_DB_PASS (and optional LIVE_DB_NAME) before running."
    exit 1
}

New-Item -ItemType Directory -Force -Path (Split-Path $Output) | Out-Null
$abs = (Resolve-Path (Split-Path $Output)).Path
$file = Split-Path $Output -Leaf

Write-Host "Dumping $Database from $HostName ..."

docker run --rm mysql:8.4 `
  mysqldump --single-transaction --routines --triggers --set-gtid-purged=OFF `
  --default-character-set=utf8mb4 `
  -h $HostName -P 3306 -u $User -p"$Pass" $Database `
  | Set-Content -Encoding utf8 "$abs\$file"

Write-Host "Wrote $abs\$file"
Get-Item "$abs\$file" | Format-List Name, Length, LastWriteTime
