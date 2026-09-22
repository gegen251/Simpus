@echo off
title Buka phpMyAdmin - Desainer Relasi Database Perpustakaan
color 0b
echo ================================================================
echo   MEMBUKA PHPMYADMIN ^& RELASI DESAINER DATABASE PERPUSTAKAAN
echo ================================================================
echo.

:: 1. Pastikan MariaDB aktif pada port 3307
netstat -ano | findstr :3307 >nul
if %errorlevel% neq 0 (
    echo [INFO] Menyalakan MariaDB port 3307...
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my_3307.ini" --standalone
    timeout /t 2 /nobreak >nul
)

:: 2. Pastikan Apache aktif pada port 80
netstat -ano | findstr :80 | findstr "LISTENING" >nul
if %errorlevel% neq 0 (
    echo [INFO] Menyalakan Apache web server...
    start /B "" "C:\xampp\apache\bin\httpd.exe"
    timeout /t 2 /nobreak >nul
)

echo [OK] Membuka Desainer Database di browser Anda...
start http://localhost/phpmyadmin/index.php?route=/database/designer^&db=perpustakaan
echo.
echo URL phpMyAdmin: http://localhost/phpmyadmin/
echo Selesai!
timeout /t 3 >nul
