# =======================================================
# SIMPUS SD - Scheduled Database Backup PowerShell Script
# =======================================================

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectDir = Resolve-Path "$ScriptDir\.."

Set-Location $ProjectDir

$PhpExe = "C:\xampp\php\php.exe"
if (-not (Test-Path $PhpExe)) {
    $PhpExe = "php"
}

Write-Host "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] Memulai eksekusi backup database..." -ForegroundColor Cyan
& $PhpExe spark db:backup --rotate=14
Write-Host "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] Proses backup selesai dengan exit code: $LASTEXITCODE" -ForegroundColor Green
