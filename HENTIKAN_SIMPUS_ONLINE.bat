@echo off
title SIMPUS - Hentikan Server Online
color 0c
cd /d "%~dp0"

echo ===============================================================
echo            MENGHENTIKAN SERVER SIMPUS.ONLINE
echo ===============================================================
echo.

echo [1/3] Menghentikan Cloudflare Tunnel...
taskkill /F /IM cloudflared.exe >nul 2>&1

echo [2/3] Menghentikan Server PHP Spark (Port 8080)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8080') do (
    taskkill /F /PID %%a >nul 2>&1
)

echo [3/3] Selesai.
echo.
echo Server SIMPUS Online telah dihentikan.
echo.
pause
