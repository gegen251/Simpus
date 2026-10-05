param([switch]$NoBrowser)

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$phpPath = 'C:\xampp\php\php.exe'
$mysqlPath = 'C:\xampp\mysql\bin\mysqld.exe'
$mysqlConfig = 'C:\xampp\mysql\bin\my_3307.ini'
$cloudflaredPath = 'C:\Program Files (x86)\cloudflared\cloudflared.exe'
$configFile = Join-Path $PSScriptRoot 'cloudflared-config.yml'

function Test-PortListening {
    param([int]$Port)
    return $null -ne (Get-NetTCPConnection -State Listen -LocalPort $Port -ErrorAction SilentlyContinue | Select-Object -First 1)
}

function Wait-ForPort {
    param([int]$Port, [int]$TimeoutSeconds = 20)
    $deadline = (Get-Date).AddSeconds($TimeoutSeconds)
    while ((Get-Date) -lt $deadline) {
        if (Test-PortListening -Port $Port) { return $true }
        Start-Sleep -Milliseconds 500
    }
    return $false
}

Write-Host ''
Write-Host '===============================================================' -ForegroundColor Cyan
Write-Host '   MEMULAI SERVER SIMPUS ONLINE (https://simpus.online)' -ForegroundColor Green
Write-Host '===============================================================' -ForegroundColor Cyan
Write-Host ''

# 1. Database MariaDB (Port 3307)
Write-Host '[1/3] Memeriksa Database MariaDB (Port 3307)...' -ForegroundColor Cyan
if (-not (Test-PortListening -Port 3307)) {
    Start-Process -FilePath $mysqlPath `
        -ArgumentList @("--defaults-file=$mysqlConfig", '--standalone') `
        -WindowStyle Hidden | Out-Null
    if (-not (Wait-ForPort -Port 3307)) { throw 'MariaDB gagal aktif di port 3307.' }
}
Write-Host '      MariaDB aktif di port 3307.' -ForegroundColor Green

# 2. Server SIMPUS PHP (Port 8080)
Write-Host '[2/3] Memeriksa Server SIMPUS CodeIgniter (Port 8080)...' -ForegroundColor Cyan
if (-not (Test-PortListening -Port 8080)) {
    Start-Process -FilePath $phpPath `
        -ArgumentList @('spark', 'serve', '--host', '127.0.0.1', '--port', '8080') `
        -WorkingDirectory $projectRoot `
        -WindowStyle Hidden | Out-Null
    if (-not (Wait-ForPort -Port 8080)) { throw 'Server SIMPUS gagal aktif di port 8080.' }
}
Write-Host '      SIMPUS aktif di http://127.0.0.1:8080.' -ForegroundColor Green

# 3. Cloudflare Tunnel simpus.online
Write-Host '[3/3] Menghubungkan ke Cloudflare Tunnel (simpus.online)...' -ForegroundColor Cyan

Write-Host ''
Write-Host '===============================================================' -ForegroundColor Cyan
Write-Host '   SIMPUS TELAH ONLINE & SIAP DIAKSES DARI INTERNET!          ' -ForegroundColor Green
Write-Host '===============================================================' -ForegroundColor Cyan
Write-Host ' Domain Resmi : https://simpus.online' -ForegroundColor Yellow
Write-Host ' Akses Lokal  : http://localhost:8080' -ForegroundColor White
Write-Host ' Login Admin  : Username: admin | Password: admin123' -ForegroundColor White
Write-Host '===============================================================' -ForegroundColor Cyan
Write-Host ' CATATAN PENTING:' -ForegroundColor Yellow
Write-Host ' - Jangan tutup jendela ini agar web tetap bisa diakses online.' -ForegroundColor Gray
Write-Host ' - Tekan Ctrl + C di jendela ini untuk menghentikan server.' -ForegroundColor Gray
Write-Host '===============================================================' -ForegroundColor Cyan
Write-Host ''

if (-not $NoBrowser) {
    Start-Process 'https://simpus.online'
}

# Run tunnel in foreground so the terminal stays active
& $cloudflaredPath tunnel --config $configFile run simpus
