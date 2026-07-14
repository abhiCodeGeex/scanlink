param(
    [string]$Host = "127.0.0.1",
    [int]$Port = 3306,
    [string]$Database = "scanlink_live",
    [string]$User = "readonly_user",
    [string]$Output = ".\\storage\\app\\migration\\scanlink-live.sql"
)

Write-Host "Exporting live DB snapshot (read-only) ..."
Write-Host "Use mysqldump from your local MySQL client tools."
Write-Host "Example:"
Write-Host "mysqldump --single-transaction --routines --triggers --events --default-character-set=utf8mb4 -h $Host -P $Port -u $User -p $Database > $Output"
