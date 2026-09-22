@echo off
title Aplikasi Manajemen Perpustakaan - Runner
color 0b

echo ================================================================
echo         SISTEM MANAJEMEN PERPUSTAKAAN DIGITAL MANDIRI
echo         CodeIgniter 4 + MariaDB + Tailwind CSS + Dompdf
echo ================================================================
echo.

:: 1. Memeriksa & Menjalankan MariaDB (Port 3307)
echo [1/3] Memeriksa status database MariaDB (Port 3307)...
netstat -ano | findstr :3307 >nul
if %errorlevel% neq 0 (
    echo [INFO] Menjalankan MariaDB pada port 3307...
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my_3307.ini" --standalone
    timeout /t 3 /nobreak >nul
) else (
    echo [OK] Database MariaDB sudah aktif pada port 3307.
)

:: 2. Memeriksa & Menjalankan Apache (Port 80) untuk phpMyAdmin
echo [2/3] Memeriksa status Apache HTTP Server (Port 80 / phpMyAdmin)...
netstat -ano | findstr :80 | findstr "LISTENING" >nul
if %errorlevel% neq 0 (
    echo [INFO] Menjalankan Apache HTTP Server...
    start /B "" "C:\xampp\apache\bin\httpd.exe" -d "C:\xampp\apache"
    timeout /t 2 /nobreak >nul
) else (
    echo [OK] Apache HTTP Server sudah aktif pada port 80.
)

:: 3. Menjalankan PHP Spark Serve
echo.
echo [3/3] Memulai server web CodeIgniter 4...
echo.
echo ================================================================
echo  Aplikasi siap diakses di browser Anda:
echo  - Web Perpustakaan  : http://localhost:8080/
echo  - Login Petugas     : http://localhost:8080/login
echo    Username: admin ^| Password: admin123
echo.
echo  - phpMyAdmin        : http://localhost/phpmyadmin/
echo  - Desainer ERD      : http://localhost/phpmyadmin/index.php?route=/database/designer^&db=perpustakaan
echo ================================================================
echo.
echo Tekan Ctrl+C di jendela ini untuk menghentikan server.
echo.

"C:\xampp\php\php.exe" spark serve --port 8080

pause
