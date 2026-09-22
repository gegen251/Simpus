@echo off
REM =======================================================
REM Mendaftarkan Windows Scheduled Task untuk Backup Harian
REM Dijalankan setiap hari pukul 02:00 WIB
REM =======================================================

set TASK_NAME=SIMPUS_Database_Daily_Backup
set SCRIPT_PATH=%~dp0backup_db.bat

echo Mendaftarkan Scheduled Task: %TASK_NAME% ...
schtasks /create /tn "%TASK_NAME%" /tr "\"%SCRIPT_PATH%\"" /sc daily /st 02:00 /f

if %ERRORLEVEL% equ 0 (
    echo [SUKSES] Scheduled Task berhasil didaftarkan! Backup akan berjalan otomatis setiap hari pukul 02:00.
) else (
    echo [PERHATIAN] Gagal mendaftarkan task secara otomatis. Pastikan Anda menjalankan Command Prompt sebagai Administrator.
)
pause
