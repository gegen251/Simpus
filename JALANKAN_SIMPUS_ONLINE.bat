@echo off
title SIMPUS - Server Online (simpus.online)
color 0b
cd /d "%~dp0"

echo ===============================================================
echo            MENYALAKAN SERVER SIMPUS.ONLINE
echo ===============================================================
echo.

powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0scripts\start_simpus_online.ps1"

if errorlevel 1 (
    echo.
    echo [ERROR] Terjadi kesalahan saat menyalakan server.
    echo Periksa pesan di atas.
)

echo.
pause
