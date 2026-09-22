@echo off
REM =======================================================
REM SIMPUS SD - Scheduled Database Backup Script
REM Digunakan untuk Windows Task Scheduler atau Cron
REM =======================================================

set PROJECT_DIR=%~dp0..
cd /d "%PROJECT_DIR%"

set PHP_EXE=C:\xampp\php\php.exe
if not exist "%PHP_EXE%" (
    set PHP_EXE=php
)

echo [%date% %time%] Memulai eksekusi backup terjadwal...
"%PHP_EXE%" spark db:backup --rotate=14
echo [%date% %time%] Eksekusi selesai dengan status code: %ERRORLEVEL%
