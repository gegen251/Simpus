# 📚 SIMPUS — Sistem Informasi Manajemen Perpustakaan Terpadu
### Solusi Otomasi Perpustakaan Sekolah Terpadu dengan Terminal Kios Mandiri & OPAC Terbuka

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Framework](https://img.shields.io/badge/Framework-CodeIgniter%204.7.4-red.svg)](https://codeigniter.com)
[![Database](https://img.shields.io/badge/Database-MariaDB%2010.4-orange.svg)](https://mariadb.org)
[![PHPUnit Tests](https://img.shields.io/badge/Tests-25%20Passed%20%7C%20100%25%20Green-brightgreen.svg)](docs/UAT_AKHIR_SIGN_OFF.md)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 🌟 Tentang Aplikasi

**SIMPUS** adalah sistem manajemen perpustakaan modern berbasis web yang dirancang khusus untuk memenuhi kebutuhan sirkulasi, inventarisasi koleksi, dan peningkatan literasi di lingkungan sekolah dasar. Dilengkapi dengan **Terminal Kios Mandiri Siswa (Self-Service Kiosk)** ramah anak serta **Katalog Terbuka Publik (OPAC)**, SIMPUS menghadirkan pengalaman perpustakaan yang cepat, transparan, dan terotomasi penuh.

---

## ✨ Fitur-Fitur Unggulan

1. **Terminal Kios Mandiri Siswa (Self-Service Kiosk)**:
   - Antarmuka ramah anak dengan layar sentuh dan pemindai barcode kartu NISN.
   - Peminjaman dan pengembalian mandiri instan dengan struk digital.
   - Pembatasan kuota otomatis (maksimal 2 buku) dan edukasi keterlambatan.
2. **Katalog Terbuka Publik (OPAC)**:
   - Akses pencarian judul, penulis, penerbit, dan ISBN tanpa perlu login.
   - Multi-tier query cache dengan waktu respon kilat (< 120 ms).
   - Zero-PII Leakage: Identitas peminjam terlindungi secara aman dari publik.
3. **Meja Sirkulasi Pustakawan**:
   - Peminjaman buku berbasis barcode scanner USB.
   - Perpanjangan masa pinjam (maksimal 1x per transaksi).
   - Pengembalian buku dengan kalkulasi denda otomatis per hari terlambat.
   - Pengurangan dan penambahan stok buku secara atomik tanpa race condition.
4. **Manajemen Koleksi & Barcode Stiker**:
   - Pencatatan buku lengkap dengan cover visual dan lokasi rak.
   - Generator Barcode CODE128 siap cetak ke format label stiker 3x5 cm.
   - Import data buku massal via spreadsheet CSV.
5. **Manajemen Kartu Anggota Digital**:
   - Registrasi siswa dan guru dengan nomor anggota dan NISN unik.
   - Cetak kartu anggota perpustakaan digital ber-barcode siap scan.
6. **Ekspor Laporan Multi-Format**:
   - Ekspor rekapitulasi sirkulasi dan denda ke **Microsoft Excel (.xls)**.
   - Ekspor laporan ber-kop dinas ke **Microsoft Word (.doc)**.
   - Ekspor pratinjau dokumen resmi ke format **PDF**.
7. **Keamanan & Pemeliharaan Terjadwal**:
   - Otentikasi berjenjang (Role-Based Access Control: Administrator vs Petugas).
   - Backup otomatis basis data ke format `.sql` terkompresi.
   - Rotasi dan kompresi log aplikasi gzip dengan efisiensi hemat ruang hingga 97%.

---

## 📂 Dokumentasi Lengkap Proyek

Seluruh dokumentasi teknis dan panduan operasional tersedia secara terstruktur pada direktori [`docs/`](docs/):

| Dokumen | Deskripsi | Tautan |
|---|---|:---:|
| **Dokumentasi Teknis & ERD** | Arsitektur MVC, ERD Mermaid 9 tabel, kamus data detail, 5 alur bisnis proses, dan panduan instalasi setup dari nol. | [Buka Dokumen](docs/DOKUMENTASI_TEKNIS.md) |
| **Panduan Pengguna Staf** | Buku manual operasional meja sirkulasi, manajemen buku, cetak barcode/kartu, denda, dan laporan untuk pustakawan. | [Buka Dokumen](docs/PANDUAN_PENGGUNA_STAF.md) |
| **Panduan Kios Siswa** | Panduan visual ramah anak langkah-demi-langkah peminjaman dan pengembalian mandiri di terminal kios. | [Buka Dokumen](docs/PANDUAN_KIOS_SISWA.md) |
| **UAT Akhir & Lembar Pengesahan** | Matriks 20 kasus uji penerimaan pengguna beserta berita acara serah terima resmi (Sign-off). | [Buka Dokumen](docs/UAT_AKHIR_SIGN_OFF.md) |

---

## 🚀 Panduan Ringkas Menjalankan Proyek (Quick Start)

### 1. Prasyarat Sistem
- PHP 8.2+ dengan ekstensi `mysqli`, `intl`, `mbstring`, `curl`, `gd`, `sqlite3`.
- MariaDB 10.4 / MySQL (Port `3307`).
- Composer 2.5+.

### 2. Instalasi & Setup Basis Data
```bash
# 1. Unduh pustaka dependensi
composer install

# 2. Salin environment
copy env .env

# 3. Jalankan migrasi dan seeder data awal
php spark migrate
php spark db:seed MainSeeder
```

### 3. Akun Bawaan Sistem
- **Administrator Utama**: `admin` / `admin123` (Akses Penuh)
- **Petugas Pustaka**: `petugas` / `petugas123` (Meja Sirkulasi)

### 4. Menjalankan Server Lokal
```bash
# Menjalankan server lokal port 8080
php spark serve --port=8080
```
Buka browser Anda:
- **Katalog OPAC**: [http://localhost:8080/katalog](http://localhost:8080/katalog)
- **Terminal Kios Siswa**: [http://localhost:8080/kiosk](http://localhost:8080/kiosk)
- **Portal Login Staf**: [http://localhost:8080/login](http://localhost:8080/login)

### 5. Membuka Review Publik Sementara

Klik dua kali `MULAI_REVIEW_PUBLIK.bat`. Launcher akan menyalakan MariaDB, server SIMPUS, dan Cloudflare Quick Tunnel secara otomatis. Tautan HTTPS publik akan dibuka di browser serta disalin ke clipboard.

> Quick Tunnel hanya untuk demo/UAT. Laptop dan koneksi internet harus tetap aktif. Tautan berubah setelah tunnel dihentikan atau laptop dinyalakan ulang.

---

## 🧪 Pengujian Otomatis (Automated Testing)

SIMPUS dilengkapi dengan rangkaian pengujian komprehensif:

```bash
# 1. Menjalankan PHPUnit unit & feature test suite
php spark test

# 2. Menjalankan dengan format laporan TestDox
php spark test --testdox

# 3. Menjalankan Master QA Regression Suite (13 Modul)
php scripts/run_all_qa.php
```

> Status Hasil: **25 Unit Tests Passed (100% Green)** dan **13 Modul QA Passed (All Tests Green)**.

---

## 📜 Lisensi
Aplikasi ini dilisensikan di bawah lisensi terbuka [MIT License](LICENSE).
Dikembangkan untuk mendukung program literasi dan digitalisasi perpustakaan sekolah dasar.
