param(
    [string]$Host = "127.0.0.1",
    [int]$Port = 3307,
    [string]$Database = "scanlink_laravel",
    [string]$User = "scanlink",
    [string]$Input = ".\\storage\\app\\migration\\scanlink-live.sql"
)

Write-Host "Importing snapshot into local MySQL ..."
Write-Host "Example:"
Write-Host "mysql -h $Host -P $Port -u $User -p $Database < $Input"
