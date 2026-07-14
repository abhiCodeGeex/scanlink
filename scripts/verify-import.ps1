param(
    [string]$DbHost = "127.0.0.1",
    [int]$DbPort = 3307,
    [string]$DbName = "scanlink_laravel",
    [string]$DbUser = "scanlink",
    [string]$DbPassword = "scanlink"
)

$tables = @(
    "clients",
    "client_users",
    "code_purchase",
    "code_purchase_detail",
    "equipment_types",
    "profiles",
    "orders",
    "form_builder_orders",
    "settings",
    "code_prising",
    "reseller_pricing",
    "testimonial",
    "gallery",
    "qrimage",
    "users",
    "admin"
)

Write-Host "ScanLink import verification"
Write-Host "Database: $DbName @ ${DbHost}:$DbPort"
Write-Host ""

foreach ($table in $tables) {
    $query = "SELECT COUNT(*) AS total FROM $table;"
    $result = mysql -h $DbHost -P $DbPort -u $DbUser -p$DbPassword -N -e $query $DbName 2>$null

    if ($LASTEXITCODE -ne 0) {
        Write-Host "[FAIL] $table - query failed"
        continue
    }

    Write-Host "[OK] $table rows: $result"
}

Write-Host ""
Write-Host "Done."
