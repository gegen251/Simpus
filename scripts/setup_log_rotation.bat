@echo off
REM =========================================================================
REM SIMPUS — Setup Log Rotation via Windows Task Scheduler
REM =========================================================================
REM Script ini membuat tugas terjadwal yang menjalankan rotasi log
REM secara otomatis setiap hari pukul 02:00 WIB.
REM
REM Jalankan sebagai Administrator:
REM   scripts\setup_log_rotation.bat
REM =========================================================================

echo.
echo ╔══════════════════════════════════════════════════════════╗
echo ║  SIMPUS - Setup Rotasi Log Otomatis (Task Scheduler)    ║
echo ╚══════════════════════════════════════════════════════════╝
echo.

REM Deteksi direktori project
set "PROJECT_DIR=%~dp0.."
set "PHP_PATH=php"
where %PHP_PATH% >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\xampp\php\php.exe" (
        set "PHP_PATH=C:\xampp\php\php.exe"
    ) else if exist "C:\laragon\bin\php\php.exe" (
        set "PHP_PATH=C:\laragon\bin\php\php.exe"
    ) else (
        echo [ERROR] PHP tidak ditemukan di PATH maupun C:\xampp\php\php.exe.
        echo         Pastikan PHP terinstall dan terkonfigurasi.
        echo.
        pause
        exit /b 1
    )
)

echo [INFO] Project Dir  : %PROJECT_DIR%
echo [INFO] PHP Path     : %PHP_PATH%
echo.

REM Buat task scheduler untuk rotasi log harian jam 02:00
schtasks /create ^
    /tn "SIMPUS_LogRotation" ^
    /tr "\"%PHP_PATH%\" \"%PROJECT_DIR%\spark\" logs:rotate --days=30" ^
    /sc daily ^
    /st 02:00 ^
    /rl HIGHEST ^
    /f

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [OK] Task "SIMPUS_LogRotation" berhasil dibuat!
    echo      Jadwal   : Setiap hari pukul 02:00
    echo      Retensi  : 30 hari ^(log ^> 30 hari dihapus otomatis^)
    echo      Kompres  : Log ^> 7 hari dikompres gzip
    echo.
    echo [INFO] Untuk melihat task:
    echo        schtasks /query /tn "SIMPUS_LogRotation"
    echo.
    echo [INFO] Untuk menghapus task:
    echo        schtasks /delete /tn "SIMPUS_LogRotation" /f
) else (
    echo.
    echo [ERROR] Gagal membuat task scheduler.
    echo         Coba jalankan script ini sebagai Administrator.
)

echo.
pause
