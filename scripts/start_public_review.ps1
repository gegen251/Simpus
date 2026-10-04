param([switch]$NoBrowser)

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$phpPath = 'C:\xampp\php\php.exe'
$mysqlPath = 'C:\xampp\mysql\bin\mysqld.exe'
$mysqlConfig = 'C:\xampp\mysql\bin\my_3307.ini'
$cloudflaredPath = 'C:\Program Files (x86)\cloudflared\cloudflared.exe'
$envPath = Join-Path $projectRoot '.env'
$logDirectory = Join-Path $projectRoot 'writable\logs'
$tunnelLog = Join-Path $logDirectory 'cloudflared-review.log'

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

function Test-PublicUrl {
    param([string]$BaseUrl)

    if ([string]::IsNullOrWhiteSpace($BaseUrl)) { return $false }
    try {
        $response = Invoke-WebRequest -Uri ($BaseUrl.TrimEnd('/') + '/katalog') -UseBasicParsing -TimeoutSec 10
        return $response.StatusCode -eq 200
    } catch {
        return $false
    }
}

function Read-SharedTextFile {
    param([string]$Path)

    $stream = $null
    $reader = $null
    try {
        $stream = [IO.File]::Open($Path, [IO.FileMode]::Open, [IO.FileAccess]::Read, [IO.FileShare]::ReadWrite)
        $reader = [IO.StreamReader]::new($stream)
        return $reader.ReadToEnd()
    } finally {
        if ($reader) { $reader.Dispose() }
        elseif ($stream) { $stream.Dispose() }
    }
}

function Get-ConfiguredPublicUrl {
    if (-not (Test-Path -LiteralPath $envPath)) { return $null }
    $envContent = [IO.File]::ReadAllText($envPath)
    $match = [regex]::Match($envContent, '(?m)^app\.baseURL\s*=\s*[''"](?<url>https://[^''"]+\.trycloudflare\.com)/?[''"]')
    if ($match.Success) { return $match.Groups['url'].Value }
    return $null
}

function Set-PublicBaseUrl {
    param([string]$BaseUrl)

    $envContent = [IO.File]::ReadAllText($envPath)
    $replacement = "app.baseURL = '$($BaseUrl.TrimEnd('/'))/'"
    $updatedContent = [regex]::Replace($envContent, '(?m)^app\.baseURL\s*=.*$', $replacement)
    [IO.File]::WriteAllText($envPath, $updatedContent, [Text.UTF8Encoding]::new($false))
}

function Show-ReviewDetails {
    param([string]$PublicUrl)

    Write-Host ''
    Write-Host '===============================================================' -ForegroundColor Cyan
    Write-Host ' SIMPUS SIAP DIREVIEW' -ForegroundColor Green
    Write-Host '===============================================================' -ForegroundColor Cyan
    Write-Host " Tautan  : $PublicUrl" -ForegroundColor Yellow
    Write-Host ' Username : admin'
    Write-Host ' Password : admin123'
    Write-Host '===============================================================' -ForegroundColor Cyan
    Write-Host 'Biarkan laptop, database, server SIMPUS, dan koneksi internet tetap aktif.'
    Write-Host 'Tautan akan berubah setelah tunnel dihentikan atau laptop dinyalakan ulang.'
    Write-Host ''

    Set-Clipboard -Value $PublicUrl
    if (-not $NoBrowser) {
        Start-Process $PublicUrl
    }
}

foreach ($requiredPath in @($phpPath, $mysqlPath, $mysqlConfig, $cloudflaredPath, $envPath)) {
    if (-not (Test-Path -LiteralPath $requiredPath)) {
        throw "Komponen tidak ditemukan: $requiredPath"
    }
}

New-Item -ItemType Directory -Force -Path $logDirectory | Out-Null

Write-Host '[1/3] Memeriksa database MariaDB...' -ForegroundColor Cyan
if (-not (Test-PortListening -Port 3307)) {
    Start-Process -FilePath $mysqlPath `
        -ArgumentList @("--defaults-file=$mysqlConfig", '--standalone') `
        -WindowStyle Hidden | Out-Null
    if (-not (Wait-ForPort -Port 3307)) { throw 'MariaDB gagal aktif di port 3307.' }
}
Write-Host '      Database aktif di port 3307.' -ForegroundColor Green

Write-Host '[2/3] Memeriksa server SIMPUS...' -ForegroundColor Cyan
if (-not (Test-PortListening -Port 8080)) {
    Start-Process -FilePath $phpPath `
        -ArgumentList @('spark', 'serve', '--host', '127.0.0.1', '--port', '8080') `
        -WorkingDirectory $projectRoot `
        -WindowStyle Hidden | Out-Null
    if (-not (Wait-ForPort -Port 8080)) { throw 'Server SIMPUS gagal aktif di port 8080.' }
}
Write-Host '      SIMPUS aktif di http://localhost:8080.' -ForegroundColor Green

Write-Host '[3/3] Menyiapkan tautan publik...' -ForegroundColor Cyan
$configuredUrl = Get-ConfiguredPublicUrl
if ($configuredUrl -and (Test-PublicUrl -BaseUrl $configuredUrl)) {
    Write-Host '      Tunnel yang aktif digunakan kembali.' -ForegroundColor Green
    Show-ReviewDetails -PublicUrl $configuredUrl
    exit 0
}

[IO.File]::WriteAllText($tunnelLog, '', [Text.UTF8Encoding]::new($false))
Start-Process -FilePath $cloudflaredPath `
    -ArgumentList @('tunnel', "--logfile=`"$tunnelLog`"", '--url', 'http://127.0.0.1:8080') `
    -WindowStyle Hidden | Out-Null

$publicUrl = $null
$deadline = (Get-Date).AddSeconds(35)
while ((Get-Date) -lt $deadline) {
    Start-Sleep -Milliseconds 500
    $logs = Read-SharedTextFile -Path $tunnelLog
    $match = [regex]::Match($logs, 'https://[a-z0-9-]+\.trycloudflare\.com')
    if ($match.Success) {
        $publicUrl = $match.Value
        break
    }
}

if (-not $publicUrl) { throw "Tautan publik gagal dibuat. Periksa log: $tunnelLog" }
Set-PublicBaseUrl -BaseUrl $publicUrl

$ready = $false
for ($attempt = 0; $attempt -lt 30; $attempt++) {
    if (Test-PublicUrl -BaseUrl $publicUrl) {
        $ready = $true
        break
    }
    Start-Sleep -Seconds 1
}
if (-not $ready) { throw 'Tunnel dibuat tetapi halaman SIMPUS belum dapat dijangkau.' }

Show-ReviewDetails -PublicUrl $publicUrl
