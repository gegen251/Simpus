@echo off
title Database MySQL - Sistem Perpustakaan Digital
color 0b
echo ================================================================
echo        KONSOL DATABASE MYSQL (PORT 3307) - PERPUSTAKAAN
echo ================================================================
echo Terhubung ke: 127.0.0.1:3307 ^| Database: perpustakaan ^| User: root
echo.
echo Contoh perintah yang bisa Anda ketik:
echo   - SHOW TABLES;                           (melihat semua tabel)
echo   - SELECT * FROM buku;                    (melihat seluruh data buku)
echo   - SELECT * FROM peminjaman;              (melihat transaksi pinjam)
echo   - DESCRIBE buku;                         (melihat struktur kolom tabel)
echo   - exit                                   (keluar dari konsol)
echo ================================================================
echo.
"C:\xampp\mysql\bin\mysql.exe" -u root -P 3307 -h 127.0.0.1 perpustakaan
pause
