# DOKUMENTASI TEKNIS SISTEM
## SIMPUS — Sistem Informasi Manajemen Perpustakaan Terpadu
**Lampiran Tugas Akhir / Proyek Pengembangan Perangkat Lunak**

---

## 1. Ringkasan Eksekutif & Spesifikasi Teknologi

SIMPUS (Sistem Informasi Manajemen Perpustakaan Terpadu) adalah aplikasi berbasis web yang dirancang khusus untuk memenuhi kebutuhan sirkulasi, inventarisasi, dan layanan mandiri siswa di lingkungan perpustakaan sekolah dasar. Aplikasi ini mengintegrasikan meja sirkulasi konvensional dengan **Terminal Kios Mandiri (Self-Service Kiosk)** ramah anak serta **Katalog Terbuka Publik (OPAC)** tanpa login.

### Spesifikasi Stack Teknologi:
| Komponen | Teknologi / Versi | Fungsi Utama |
|---|---|---|
| **Bahasa Pemrograman** | PHP 8.2+ (ZTS Visual C++ 2019 x64) | Bahasa server utama berkinerja tinggi |
| **Framework Backend** | CodeIgniter v4.7.4 | Arsitektur MVC, Middleware Filter, Service Container |
| **Basis Data** | MariaDB 10.4 / MySQL 8.x (Port 3307) | Relational Database Management System (InnoDB Engine) |
| **Frontend Styling** | Vanilla CSS + TailwindCSS Design System | Desain modern responsif dengan Mode Terang & Gelap |
| **Pustaka Ikon** | Lucide Icons v0.468.0 (Self-Hosted) | Ikon grafis konsisten dengan CDN Fallback |
| **Visualisasi Data** | Chart.js v4.4.7 | Grafik statistik transaksi dan tren peminjaman |
| **Dialog Interaktif** | SweetAlert2 v11.14.5 | Modal konfirmasi atomik dan alert interaktif |
| **Barcode Engine** | JsBarcode v3.11.5 | Render barcode CODE128 untuk kartu anggota & buku |
| **Ekspor Dokumen** | DomPDF v3.1 + Native HTML Generators | Ekspor laporan resmi ke format PDF, Excel, dan Word |
| **Testing Suite** | PHPUnit v10.5.64 + Custom Spark Runner | Unit test logika kritis dan QA regression master suite |

---

## 2. Entity Relationship Diagram (ERD) & Kamus Data

### Diagram Konseptual Relasi Basis Data (Mermaid)

```mermaid
erDiagram
    KATEGORI_BUKU ||--o{ BUKU : "mengelompokkan"
    BUKU ||--o{ PEMINJAMAN_DETAIL : "dipinjam_dalam"
    BUKU ||--o{ PEMINJAMAN : "direferensikan"
    ANGGOTA ||--o{ PEMINJAMAN : "meminjam"
    PETUGAS ||--o{ PEMINJAMAN : "melayani"
    PETUGAS ||--o{ PENGEMBALIAN : "menerima"
    PETUGAS ||--o{ AUDIT_LOGS : "mencatat_aktivitas"
    PEMINJAMAN ||--o| PENGEMBALIAN : "diselesaikan_oleh"
    PEMINJAMAN ||--o{ PEMINJAMAN_DETAIL : "memiliki_item"

    KATEGORI_BUKU {
        int id PK "Auto Increment"
        string kode_kategori "UNIQUE"
        string nama_kategori
        text deskripsi "NULLable"
        datetime created_at
        datetime updated_at
    }

    BUKU {
        int id PK "Auto Increment"
        string kode_buku "UNIQUE"
        string judul
        string penulis
        string penerbit
        string isbn "NULLable"
        int kategori_id FK "References kategori_buku(id)"
        year tahun_terbit
        int jumlah_eksemplar "Total inventaris"
        int stok_tersedia "Stok siap pinjam"
        string lokasi_rak
        enum status "'aktif', 'rusak', 'hilang'"
        string cover "NULLable"
        text deskripsi "NULLable"
        datetime created_at
        datetime updated_at
    }

    ANGGOTA {
        int id PK "Auto Increment"
        string nomor_anggota "UNIQUE"
        string no_identitas "NISN/NIP, UNIQUE"
        string nama_lengkap
        enum jenis_anggota "'siswa', 'guru', 'karyawan'"
        string kelas "NULLable (mis. 4-A)"
        enum jenis_kelamin "'L', 'P'"
        string kontak "NULLable"
        text alamat "NULLable"
        date tanggal_daftar
        date tanggal_kadaluarsa
        enum status "'aktif', 'nonaktif', 'banned'"
        datetime created_at
        datetime updated_at
    }

    PETUGAS {
        int id PK "Auto Increment"
        string username "UNIQUE"
        string password "Hashed Password"
        string nama_lengkap
        string email "NULLable"
        enum role "'admin', 'staf'"
        enum status "'aktif', 'nonaktif'"
        datetime last_login "NULLable"
        datetime created_at
        datetime updated_at
    }

    PEMINJAMAN {
        int id PK "Auto Increment"
        string kode_transaksi "UNIQUE"
        int anggota_id FK "References anggota(id)"
        int buku_id FK "References buku(id)"
        int admin_id FK "References petugas(id)"
        date tanggal_pinjam
        date tanggal_jatuh_tempo
        enum status "'dipinjam', 'dikembalikan', 'terlambat'"
        int perpanjangan_count "Batas maks 1x"
        text catatan "NULLable"
        datetime created_at
        datetime updated_at
    }

    PEMINJAMAN_DETAIL {
        int id PK "Auto Increment"
        int peminjaman_id FK "References peminjaman(id)"
        int buku_id FK "References buku(id)"
        enum status "'dipinjam', 'dikembalikan'"
    }

    PENGEMBALIAN {
        int id PK "Auto Increment"
        int peminjaman_id FK "References peminjaman(id)"
        date tanggal_kembali
        int jumlah_hari_terlambat "Default 0"
        decimal denda "Default 0.00"
        enum status_denda "'tidak_ada', 'lunas', 'belum_lunas'"
        int admin_id FK "References petugas(id)"
        text catatan "NULLable"
        datetime created_at
        datetime updated_at
    }

    PENGATURAN {
        int id PK "Auto Increment"
        string kunci "UNIQUE"
        text nilai
        string keterangan "NULLable"
        datetime updated_at
    }

    AUDIT_LOGS {
        int id PK "Auto Increment"
        string action "Kategori aksi"
        text description "Keterangan detail"
        int admin_id FK "NULLable (Tamu = NULL)"
        string ip_address
        string user_agent
        datetime created_at
    }
```

---

## 3. Kamus Data Rinci (Data Dictionary)

### 3.1. Tabel `buku`
Menyimpan data master koleksi pustaka sekolah:
- **`id`**: INT(11) UNSIGNED, Primary Key, Auto Increment.
- **`kode_buku`**: VARCHAR(50), NOT NULL, UNIQUE (Format: `BK-XXXX`).
- **`judul`**: VARCHAR(255), NOT NULL, Judul buku lengkap.
- **`penulis`**: VARCHAR(150), NOT NULL, Nama pengarang buku.
- **`penerbit`**: VARCHAR(150), NOT NULL, Penerbit buku.
- **`isbn`**: VARCHAR(30), NULL, Nomor ISBN resmi 10 atau 13 digit.
- **`kategori_id`**: INT(11) UNSIGNED, Foreign Key ke `kategori_buku(id)`.
- **`tahun_terbit`**: YEAR(4), NOT NULL, Tahun terbitan.
- **`jumlah_eksemplar`**: INT(11), NOT NULL, Default: 1, Total buku fisik yang dimiliki.
- **`stok_tersedia`**: INT(11), NOT NULL, Default: 1, Jumlah buku yang sedang ada di rak dan siap dipinjam.
- **`lokasi_rak`**: VARCHAR(50), NOT NULL, Kode rak simpan (mis. `R-01-A`).
- **`status`**: ENUM('aktif', 'rusak', 'hilang'), Default: 'aktif'.
- **`cover`**: VARCHAR(255), NULL, Nama file gambar cover di folder `public/uploads/covers/`.

### 3.2. Tabel `anggota`
Menyimpan identitas anggota perpustakaan (siswa, guru, tenaga kependidikan):
- **`id`**: INT(11) UNSIGNED, Primary Key, Auto Increment.
- **`nomor_anggota`**: VARCHAR(50), NOT NULL, UNIQUE (Format: `AG-XXXX`).
- **`no_identitas`**: VARCHAR(50), NOT NULL, UNIQUE (NISN untuk siswa, NIP untuk guru).
- **`nama_lengkap`**: VARCHAR(150), NOT NULL, Nama lengkap anggota.
- **`jenis_anggota`**: ENUM('siswa', 'guru', 'karyawan'), Default: 'siswa'.
- **`kelas`**: VARCHAR(20), NULL, Jenjang kelas (mis. `1-A`, `4-B`).
- **`status`**: ENUM('aktif', 'nonaktif', 'banned'), Default: 'aktif'.
- **`tanggal_kadaluarsa`**: DATE, NOT NULL, Batas berlaku kartu keanggotaan.

### 3.3. Tabel `peminjaman` & `pengembalian`
Menyimpan jejak sirkulasi peminjaman dan penyelesaian:
- **`peminjaman.kode_transaksi`**: VARCHAR(50), UNIQUE (Format: `TRX-YYYYMMDD-XXXX`).
- **`peminjaman.tanggal_pinjam`**: DATE, Tanggal resmi buku dipinjam.
- **`peminjaman.tanggal_jatuh_tempo`**: DATE, Batas akhir pengembalian (default: `tanggal_pinjam + durasi_pinjam`).
- **`peminjaman.perpanjangan_count`**: INT(2), Default: 0, Maksimal 1 kali perpanjangan (durasi tambahan 7 hari).
- **`pengembalian.jumlah_hari_terlambat`**: INT(11), Selisih hari antara `tanggal_kembali` dengan `tanggal_jatuh_tempo`.
- **`pengembalian.denda`**: DECIMAL(10,2), Hasil kali `jumlah_hari_terlambat * tarif_denda_per_hari`.
- **`pengembalian.status_denda`**: ENUM('tidak_ada', 'lunas', 'belum_lunas').

---

## 4. Alur Bisnis Sistem (Business Workflows)

### 4.1. Alur Peminjaman Buku di Meja Petugas
```mermaid
sequenceDiagram
    autonumber
    actor S as Siswa/Peminjam
    actor P as Pustakawan/Staf
    participant SYS as Aplikasi SIMPUS
    participant DB as MariaDB

    S->>P: Menyerahkan Kartu Anggota & Buku
    P->>SYS: Input/Scan Nomor Anggota (NISN/AG)
    SYS->>DB: Validasi Status Anggota & Limit Pinjam (Maks 2)
    alt Anggota Nonaktif / Pinjaman Maksimal
        SYS-->>P: Tampilkan Alert Penolakan
        P-->>S: Edukasi batas pinjam / aktifkan kartu
    else Anggota Valid & Kuota Tersedia
        P->>SYS: Input/Scan Barcode Buku (BK-XXXX)
        SYS->>DB: Cek Stok Tersedia (stok_tersedia > 0)
        alt Stok Habis (0)
            SYS-->>P: Peringatan Buku Sedang Dipinjam
        else Stok Ada
            SYS->>DB: Begin Transaction:
            Note over SYS,DB: 1. INSERT peminjaman<br/>2. UPDATE buku SET stok_tersedia = stok_tersedia - 1<br/>3. INSERT audit_log
            SYS->>DB: Commit Transaction
            SYS-->>P: Transaksi Sukses & Tampilkan Struk
            P-->>S: Menyerahkan Buku & Mengingatkan Jatuh Tempo
        end
    end
```

### 4.2. Alur Peminjaman Mandiri di Terminal Kios Siswa
```mermaid
sequenceDiagram
    autonumber
    actor S as Siswa
    participant K as Terminal Kios
    participant API as Kiosk Controller
    participant DB as MariaDB

    S->>K: Tempelkan Barcode Kartu Anggota
    K->>API: POST /kiosk/cek-anggota {identifier: 'NISN-XXXX'}
    API->>DB: Verifikasi status aktif & jumlah pinjaman berjalan
    alt Tidak Valid / Kuota Penuh
        API-->>K: Respon Error Ramah Anak
        K-->>S: Pesan: "Buku pinjamanmu masih ada yang belum kembali ya!"
    else Valid
        API-->>K: Data Siswa (Nama, Foto/Avatar, Kuota Sisa)
        K-->>S: Layar Konfirmasi Siswa & Aktifkan Scanner Buku
        S->>K: Tempelkan Barcode Buku (BK-XXXX)
        K->>API: POST /kiosk/proses-pinjam
        API->>DB: Transaksi Atomik (Stok -1, Insert Peminjaman)
        API-->>K: Konfirmasi Berhasil + Nomor Transaksi
        K-->>S: Tampilan Struk Digital & Animasi Sukses
    end
```

### 4.3. Alur Pengembalian Buku & Perhitungan Denda
```mermaid
flowchart TD
    A[Buku Diterima Petugas / Di-scan di Kios] --> B{Buku Tercatat dalam Status 'dipinjam'?}
    B -- Tidak --> C[Tampilkan Pesan: Buku Tidak Sedang Dipinjam]
    B -- Ya --> D[Bandingkan Tanggal Kembali dengan Tanggal Jatuh Tempo]
    D --> E{Tanggal Kembali > Jatuh Tempo?}
    E -- Tidak (Tepat Waktu / Awal) --> F[Hari Terlambat = 0<br/>Denda = Rp 0<br/>Status Denda = 'tidak_ada']
    E -- Ya (Terlambat) --> G[Hitung Selisih Hari Terlambat<br/>Kalkulasi: Hari * Tarif Denda<br/>Status Denda = 'belum_lunas' / 'lunas']
    F --> H[Mulai Transaksi Basis Data Database:]
    G --> H
    H --> I[1. INSERT pengembalian]
    I --> J[2. UPDATE peminjaman SET status = 'dikembalikan']
    J --> K[3. UPDATE buku SET stok_tersedia = stok_tersedia + 1]
    K --> L[4. CacheInvalidator::invalidateKatalog]
    L --> M[5. Catat Audit Log]
    M --> N[Selesai: Stok Bertambah & Transaksi Tertutup]
```

### 4.4. Alur Perpanjangan Masa Pinjam (Maksimal 1 Kali)
Siswa diizinkan memperpanjang masa peminjaman dengan syarat:
1. Peminjaman belum melewati tanggal jatuh tempo (tidak berstatus `terlambat`).
2. Transaksi belum pernah diperpanjang sebelumnya (`perpanjangan_count = 0`).
3. Siswa tidak memiliki tunggakan denda atau penangguhan akun.
4. Tanggal jatuh tempo diperpanjang selama durasi standar (7 hari kalender) dan `perpanjangan_count` diperbarui menjadi `1`.

### 4.5. Alur Otentikasi & Pembatasan Hak Akses (RBAC)
```mermaid
flowchart TD
    Req[Pengguna Mengakses URL] --> IsPublic{Apakah Rute Publik?<br/>/katalog, /kiosk}
    IsPublic -- Ya --> Permitted[Izinkan Akses Langsung]
    IsPublic -- Tidak --> AuthCheck{Sesi logged_in == true?}
    AuthCheck -- Tidak --> RedirectLogin[Redirect ke /login dengan Alert Error]
    AuthCheck -- Ya --> RoleCheck{Perlu Hak Akses Khusus?<br/>filter: role:admin}
    RoleCheck -- Tidak (Rute Bersama: Sirkulasi/Laporan) --> AllowStaff[Izinkan Staf & Admin]
    RoleCheck -- Ya --> IsAdmin{user_role == 'admin'?}
    IsAdmin -- Ya --> AllowAdmin[Izinkan Akses Penuh]
    IsAdmin -- Tidak (Staf Mencoba Rute Admin) --> DenyAdmin[Redirect /dashboard + HTTP 403 / Alert Akses Ditolak]
```

---

## 5. Panduan Instalasi & Setup dari Nol (Reproducible Guide)

Dokumentasi ini menjamin pihak ketiga atau penilai tugas dapat menjalankan proyek ini dari awal secara lancar.

### 5.1. Kebutuhan Sistem Minimum
- Sistem Operasi: Windows 10/11, Linux (Ubuntu/Debian), atau macOS.
- Web Server: Apache 2.4+ (XAMPP / Laragon).
- PHP Version: PHP 8.2 atau lebih baru.
- Ekstensi PHP Wajib:
  - `php_mysqli`
  - `php_pdo_mysql`
  - `php_intl`
  - `php_mbstring`
  - `php_curl`
  - `php_gd`
  - `php_sqlite3` (untuk testing suite)
- Composer: Versi 2.5 atau lebih baru.

### 5.2. Langkah Setup Proyek

#### Langkah 1: Kloning / Unduh Repositori
Ekstrak atau letakkan repositori di direktori kerja, contoh:
```bash
d:\BELAJAR 6 SEPT 2026\Management-Perpustakaan
```

#### Langkah 2: Instalasi Dependensi Pustaka
Jalankan composer pada folder root:
```bash
composer install
```

#### Langkah 3: Konfigurasi Basis Data (`.env`)
Salin file `env` menjadi `.env` jika belum tersedia, lalu sesuaikan:
```ini
CI_ENVIRONMENT = production
app.baseURL = 'http://localhost:8080/'

# Konfigurasi Koneksi Database
database.default.hostname = 127.0.0.1
database.default.database = perpustakaan
database.default.username = root
database.default.password = ""
database.default.DBDriver = MySQLi
database.default.port = 3307

# Konfigurasi Testing In-Memory
database.tests.DBDriver = SQLite3
database.tests.database = :memory:
```

#### Langkah 4: Impor Skema Basis Data
Buka basis data MariaDB pada port 3307 dan buat skema:
```sql
CREATE DATABASE IF NOT EXISTS perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Jalankan migrasi dan seeder bawaan melalui Spark:
```bash
php spark migrate
php spark db:seed MainSeeder
```

#### Langkah 5: Akun Bawaan untuk Pengujian
| Role | Username | Password Bawaan | Hak Akses |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Akses penuh (Manajemen Staf, Pengaturan, Sirkulasi, Log) |
| **Pustakawan/Staf** | `petugas` | `petugas123` | Meja Sirkulasi, Katalog Buku, Anggota, Laporan |

#### Langkah 6: Menjalankan Server Aplikasi
Jalankan server pengembangan:
```bash
php spark serve --port=8080
```
Akses aplikasi melalui browser:
- **Katalog OPAC Terbuka**: [http://localhost:8080/katalog](http://localhost:8080/katalog)
- **Terminal Kios Siswa**: [http://localhost:8080/kiosk](http://localhost:8080/kiosk)
- **Portal Login Petugas**: [http://localhost:8080/login](http://localhost:8080/login)

#### Langkah 7: Menjalankan Uji Otomatis
Untuk memverifikasi keandalan seluruh fungsi sistem:
```bash
php spark test
```
Seluruh 25 test cases akan dieksekusi dengan status hijau (PASS).
