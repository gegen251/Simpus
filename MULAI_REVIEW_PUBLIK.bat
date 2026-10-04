@echo off
title SIMPUS - Server Review Publik
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0scripts\start_public_review.ps1"

if errorlevel 1 (
    echo.
    echo Gagal menyalakan server review. Baca pesan kesalahan di atas.
)

echo.
pause
