# Take a full-page screenshot of portal create location for visual QA.
$edge = "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
if (-not (Test-Path $edge)) {
    $edge = "C:\Program Files\Microsoft\Edge\Application\msedge.exe"
}
$outDir = "E:\abhishek-project\scanlink-laravel\storage\app\screenshots"
New-Item -ItemType Directory -Force -Path $outDir | Out-Null
$out = Join-Path $outDir "create-location.png"
$userData = Join-Path $outDir "edge-profile"
New-Item -ItemType Directory -Force -Path $userData | Out-Null

# Headless screenshot (may land on login — caller should use authenticated session if needed)
& $edge --headless=new --disable-gpu --window-size=1400,2200 --user-data-dir="$userData" --screenshot="$out" "http://localhost:8000/portal/profiles/create?type=location"
Write-Output "SHOT $out exists=$(Test-Path $out)"
if (Test-Path $out) { (Get-Item $out).Length }
