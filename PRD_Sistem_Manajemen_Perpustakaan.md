# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## SISTEM INFORMASI MANAJEMEN PERPUSTAKAAN TERPADU (SIMPUS)
**Perpustakaan SD Negeri 12 Sumbawa**

---

| Informasi Dokumen | Detail |
|---|---|
| **Nama Produk** | SIMPUS (Sistem Informasi Manajemen Perpustakaan Sekolah) |
| **Versi Dokumen** | Versi 1.0 (Production-Ready) |
| **Tanggal Efektif** | September 2026 |
| **Instansi Pemilik** | SD Negeri 12 Sumbawa |
| **Alamat Sekolah** | Jl. Sumbawa, Sungai Duri, Kec. Sungai Raya, Kabupaten Bengkayang, Kalimantan Barat 79271 |
| **Penanggung Jawab** | Dra. Hj. Sri Wahyuni, M.Pd (Kepala Sekolah) |
| **Arsitektur Teknis** | Monolithic MVC (CodeIgniter 4.7.4 + MariaDB + Tailwind CSS + Dompdf) |

---

## 1. Ringkasan Eksekutif (Executive Summary)

**SIMPUS** adalah aplikasi perpustakaan digital mandiri berbasis web yang dirancang khusus untuk memodernisasi tata kelola administrasi perpustakaan pada institusi pendidikan dasar (Sekolah Dasar). Sistem ini mencakup katalog terbuka daring (OPAC), manajemen inventaris buku, pendataan pemustaka, sirkulasi peminjaman dan perpanjangan, pengembalian buku dengan kalkulasi denda otomatis, pencetakan barcode/label, hingga rekapitulasi pelaporan eksekutif dalam berbagai format dokumen (PDF, Microsoft Excel, dan Microsoft Word).

### 1.1 Visi Produk
Mewujudkan ekosistem perpustakaan sekolah yang tertib, transparan, dan ramah anak dengan memangkas birokrasi pencatatan manual serta meningkatkan minat baca siswa melalui kemudahan akses katalog buku fisik secara digital.

### 1.2 Tujuan Bisnis & Sasaran Proyek (Objectives & Key Results)
- **Efisiensi Waktu Transaksi:** Mengurangi waktu tunggu proses peminjaman dan pengembalian buku dari rata-rata 3–5 menit manual menjadi kurang dari 20 detik per transaksi.
- **Akurasi Inventaris 100%:** Mengeliminasi disparitas antara data pencatatan buku fisik di rak dan buku yang sedang dipinjam melalui pelacakan stok `jumlah_eksemplar` vs `stok_tersedia` secara real-time.
- **Transparansi Rekonsiliasi Denda:** Menghapus kesalahan kalkulasi denda keterlambatan melalui otomasi penentuan hari terlambat berdasar kalender sistem.
- **Akreditasi Perpustakaan Sekolah:** Menyediakan rekapitulasi data sirkulasi berkala yang siap cetak untuk memenuhi standar akreditasi perpustakaan sekolah nasional.

---

## 2. Analisis Masalah & Solusi (Problem Statement)

| Masalah Operasional Lama (Manual) | Solusi yang Diterapkan pada SIMPUS |
|---|---|
| Siswa dan guru kesulitan mengetahui apakah suatu buku tersedia di perpustakaan tanpa datang langsung ke rak. | Menyediakan **OPAC (Online Public Access Catalog)** responsif tanpa perlu login, lengkap dengan pencarian judul, filter kategori, dan indikator stok ketersediaan. |
| Pencatatan buku pinjam pada buku besar kertas sering tercecer, rusak, atau terjadi selisih pencatatan. | Database relasional ACID (MariaDB InnoDB) dengan **Foreign Key Constraint** ketat yang menjamin integritas transaksi peminjaman. |
| Perhitungan denda keterlambatan dilakukan manual dan sering menimbulkan perdebatan. | Komputasi otomatis selisih hari jatuh tempo dikali tarif harian ramah anak (Rp 500/hari) dengan status pembayaran lunas/belum lunas. |
| Pembuatan kartu anggota dan label buku memakan waktu dan format tidak seragam. | Fitur cetak otomatis **Kartu Anggota ber-barcode** dan **Label Punggung Buku/Barcode** siap cetak kertas stiker. |
| Rekapitulasi laporan bulanan untuk kepala sekolah membutuhkan waktu berhari-hari. | Modul pelaporan sekali klik dengan filter tanggal dan ekspor langsung ke **PDF resmi**, **Excel (.xls)**, dan **Word (.doc)**. |

---

## 3. Profil Pengguna & Hak Akses (User Personas & Role Matrix)

### 3.1 Profil Pengguna (Personas)
1. **Admin (Kepala Perpustakaan / Koordinator IT):**
   - Kebutuhan: Akses menyeluruh ke sistem, monitoring sirkulasi, pengaturan konfigurasi denda & kuota, serta otorisasi staf perpustakaan.
2. **Staf (Pustakawan Harian):**
   - Kebutuhan: Mengoperasikan sirkulasi harian (pinjam, perpanjang, kembali), input data buku & anggota, serta cetak label/kartu.
3. **Anggota (Siswa & Guru Terdaftar):**
   - Kebutuhan: Memiliki kartu identitas perpustakaan, meminjam buku sesuai kuota, dan memeriksa batas waktu pengembalian.
4. **Publik / Pengunjung Umum:**
   - Kebutuhan: Menjelajahi katalog koleksi buku tanpa hak akses administratif.

### 3.2 Matriks Otorisasi Akses (Role-Based Access Control)

| Fitur / Modul | Pengunjung Publik | Staf Pustakawan | Kepala Perpustakaan (Admin) |
|---|:---:|:---:|:---:|
| Penelusuran OPAC (Katalog Terbuka) | ✅ Ya | ✅ Ya | ✅ Ya |
| Login ke Sistem Administrasi | ❌ Tidak | ✅ Ya | ✅ Ya |
| Manajemen Koleksi Buku (CRUD, Cover, Barcode) | ❌ Tidak | ✅ Ya | ✅ Ya |
| Manajemen Data Anggota (CRUD, Cetak Kartu) | ❌ Tidak | ✅ Ya | ✅ Ya |
| Transaksi Peminjaman & Perpanjangan | ❌ Tidak | ✅ Ya | ✅ Ya |
| Transaksi Pengembalian & Terima Denda | ❌ Tidak | ✅ Ya | ✅ Ya |
| Unduh Laporan (PDF, Excel, Word) | ❌ Tidak | ✅ Ya | ✅ Ya |
| Konfigurasi Parameter Perpustakaan (Pengaturan) | ❌ Tidak | ❌ Tidak | ✅ Ya |
| Manajemen Akun Staf (Tambah, Reset Password) | ❌ Tidak | ❌ Tidak | ✅ Ya |

---

## 4. Arsitektur Teknis (Technical Architecture)

```
[ Client Layer (Browser) ]
       │
       ▼ (HTTP Requests: Port 8080)
[ Web Presentation Layer: CodeIgniter 4.7.4 ]
   ├── Routing (app/Config/Routes.php)
   ├── Security Filters (AuthFilter, CSRF Filter)
   ├── Controllers (Buku, Anggota, Peminjaman, Pengembalian, Laporan, dll)
   └── Views (Tailwind CSS, Lucide Icons, Modern Vanilla CSS Components)
       │
       ▼ (Active Record / Query Builder)
[ Data Persistence Layer: MariaDB 10.4 (Port 3307) ]
   └── Engine: InnoDB
   └── Tables: admin, kategori_buku, buku, anggota, peminjaman, pengembalian, pengaturan
   └── Referential Integrity: Strict Foreign Keys (CASCADE/RESTRICT)
       │
       ▼ (Document Rendering Engine)
[ Dompdf 3.1 + HTML Spreadsheet Export ]
   └── PDF Reports (A4 Landscape / Portrait), Excel Spreadsheets, Word Documents
```

### 4.1 Spesifikasi Lingkungan (Tech Stack Specifications)
- **Backend Framework:** CodeIgniter 4.7.4 (PHP 8.2+)
- **Database Management System:** MariaDB 10.4.32 / MySQL 8.x
- **Koneksi Database:** MySQLi Driver (Host: `127.0.0.1`, Port: `3307`, User: `root`)
- **Frontend & Styling:** Tailwind CSS modern utility classes + Google Fonts (Inter, Outfit) + Lucide Icons SVG
- **Mesin Ekspor Dokumen:** Dompdf v3.1 (A4 Layout rendering)
- **Keamanan:** Bcrypt Password Hashing, CSRF Tokens otomatis pada seluruh formulir POST, XSS Clean filtering, Prepared Statements melawan SQL Injection.

---

## 5. Kebutuhan Fungsional (Functional Requirements)

### Modul 1: Autentikasi & Keamanan (Authentication)
- **FR-01:** Sistem menyediakan halaman login aman (`/login`) dengan verifikasi kata sandi berbasis hash BCRYPT.
- **FR-02:** Sistem menerapkan mekanisme proteksi `AuthFilter` untuk memblokir akses URL privat bagi pengguna yang belum terautentikasi.
- **FR-03:** Sistem memiliki fitur toggle "Lihat Sandi" (Eye icon) dan animasi pemuatan (*loading spinner*) untuk mencegah klik ganda formulir.
- **FR-04:** Sistem menyediakan fungsi logout aman yang membersihkan seluruh data sesi pengguna.

### Modul 2: Katalog Terbuka Publik (OPAC)
- **FR-05:** Halaman utama publik (`/katalog`) dapat diakses oleh siswa dan guru tanpa memerlukan login akun.
- **FR-06:** Tersedia fitur penelusuran buku *live search* berdasarkan kata kunci judul, penulis, penerbit, atau ISBN.
- **FR-07:** Tersedia filter kategori buku berbasis *dropdown* untuk mempersempit pencarian.
- **FR-08:** Setiap kartu buku menampilkan sampul (*cover*), status ketersediaan, sisa stok di rak, serta modal rincian informasi lengkap.

### Modul 3: Manajemen Master Koleksi Buku & Kategori
- **FR-09:** Petugas dapat menambah, melihat, memperbarui, dan menonaktifkan data buku (`kode_buku`, `judul`, `penulis`, `penerbit`, `kategori`, `tahun_terbit`, `rak`, `stok`).
- **FR-10:** Sistem mendukung pengunggahan file gambar sampul buku (JPG, JPEG, PNG, WEBP) dengan batas ukuran dan sanitasi nama berkas unik.
- **FR-11:** Sistem mendukung import data buku masal melalui file CSV dengan format template standar yang dapat diunduh.
- **FR-12:** Sistem menyediakan fitur cetak label buku dan barcode fisik untuk ditempelkan pada punggung buku.
- **FR-13:** Pengelolaan data kategori buku (nama kategori dan deskripsi klasifikasi).

### Modul 4: Manajemen Master Pemustaka (Anggota)
- **FR-14:** Petugas dapat mengelola data anggota perpustakaan mencakup NISN/NIP, Nama Lengkap, Tipe Anggota (Siswa/Guru/Staf), Kelas/Rombel, Gender, dan Nomor WhatsApp.
- **FR-15:** Sistem menyediakan fungsi impor data anggota secara masal melalui format CSV.
- **FR-16:** Sistem mampu mencetak Kartu Anggota Perpustakaan digital ber-barcode siap cetak dengan tata letak resmi sekolah dasar.

### Modul 5: Sirkulasi Peminjaman & Perpanjangan Buku
- **FR-17:** Petugas dapat menginput transaksi peminjaman buku dengan memilih anggota aktif dan buku yang stoknya tersedia (`stok_tersedia > 0`).
- **FR-18:** Sistem memvalidasi kuota peminjaman anggota agar tidak melebihi batas maksimal (`max_pinjam_buku`, default: 2 buku).
- **FR-19:** Sistem otomatis mengurangi `stok_tersedia` pada master buku sebanyak 1 unit setiap kali transaksi peminjaman berhasil disimpan.
- **FR-20:** Sistem otomatis menghitung `tanggal_jatuh_tempo` berdasarkan parameter durasi pinjam sekolah (`durasi_pinjam_default`, default: 5 hari).
- **FR-21:** Sistem mendukung fitur **Perpanjangan Pinjaman (1x)** dengan memvalidasi bahwa buku belum pernah diperpanjang dan status belum kadaluarsa, lalu memperpanjang tanggal jatuh tempo secara otomatis.

### Modul 6: Pengembalian Buku & Otomasi Denda
- **FR-22:** Petugas dapat memproses pengembalian buku berdasarkan kode transaksi peminjaman.
- **FR-23:** Sistem membandingkan `tanggal_kembali` aktual dengan `tanggal_jatuh_tempo`. Jika terlambat, sistem otomatis mengkalkulasi selisih hari.
- **FR-24:** Sistem menghitung denda keterlambatan dengan formula: `Jumlah Hari Terlambat × Tarif Denda Harian (Rp 500)`.
- **FR-25:** Petugas dapat mencatat status pelunasan denda (`lunas` atau `belum_lunas`).
- **FR-26:** Sistem otomatis menambahkan kembali 1 unit ke `stok_tersedia` pada tabel master buku setelah pengembalian terkonfirmasi.

### Modul 7: Pelaporan & Ekspor Multi-Format
- **FR-27:** Sistem menyediakan dashboard rekapitulasi data sirkulasi dengan filter rentang tanggal (*start date* s/d *end date*).
- **FR-28:** Laporan dapat diekspor ke dokumen **PDF Resmi** dengan kop sekolah, nomor NIP kepala perpustakaan, dan tanda tangan legalisir.
- **FR-29:** Laporan dapat diekspor ke dokumen **Microsoft Excel (.xls)** untuk pengolahan angka dan statistik lanjutan.
- **FR-30:** Laporan dapat diekspor ke dokumen **Microsoft Word (.doc)** untuk kemudahan penyuntingan teks narasi laporan.

### Modul 8: Konfigurasi Parameter Sekolah (Pengaturan)
- **FR-31:** Administrator dapat memperbarui identitas sekolah (Nama Perpustakaan, Alamat Lengkap, Nama Kepala Sekolah, dan NIP).
- **FR-32:** Administrator dapat mengubah kebijakan peminjaman secara dinamis (Durasi Pinjam Default, Tarif Denda Per Hari, Maksimal Kuota Pinjam).

---

## 6. Logika Bisnis & Algoritma Utama (Business Rules)

### 6.1 Algoritma Validasi Peminjaman
```
IF anggota.status != 'aktif' THEN
    REJECT "Keanggotaan pemustaka sedang dinonaktifkan."
END IF

pinjaman_aktif = COUNT(peminjaman WHERE anggota_id = ID AND status IN ('dipinjam', 'terlambat'))
IF pinjaman_aktif >= pengaturan.max_pinjam_buku THEN
    REJECT "Anggota telah mencapai batas maksimal kuota peminjaman (" + max_pinjam_buku + " buku)."
END IF

IF buku.stok_tersedia <= 0 THEN
    REJECT "Buku tidak dapat dipinjam. Stok fisik di rak saat ini kosong."
END IF

// Simpan Transaksi & Mutasi Stok
INSERT INTO peminjaman (...)
UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id = buku_id
```

### 6.2 Algoritma Pengembalian & Komputasi Denda
```
hari_terlambat = DATEDIFF(tanggal_kembali, tanggal_jatuh_tempo)

IF hari_terlambat > 0 THEN
    total_denda = hari_terlambat * pengaturan.tarif_denda_per_hari
    status_peminjaman = 'terlambat'
ELSE
    hari_terlambat = 0
    total_denda = 0.00
    status_peminjaman = 'dikembalikan'
END IF

// Simpan Pengembalian & Kembalikan Stok
INSERT INTO pengembalian (peminjaman_id, tanggal_kembali, jumlah_hari_terlambat, denda, ...)
UPDATE peminjaman SET status = 'dikembalikan' WHERE id = peminjaman_id
UPDATE buku SET stok_tersedia = stok_tersedia + 1 WHERE id = buku_id
```

---

## 7. Kebutuhan Non-Fungsional (Non-Functional Requirements)

- **Keamanan (Security):** Seluruh password terenkripsi via BCRYPT. Seluruh rute dilindungi CSRF token. Parameter query menggunakan parameterized statements.
- **Integritas Data (Referential Integrity):** Menggunakan InnoDB Foreign Key Constraints dengan aturan `RESTRICT` pada relasi riwayat untuk mencegah data yatim (*orphaned records*).
- **Kinerja (Performance):** Waktu respon rata-rata halaman < 350 milidetik pada penelusuran katalog dan sirkulasi lokal.
- **Portabilitas & Kemudahan Deployment:** Dilengkapi script *standalone* runner (`start_server.bat` dan `buka_phpmyadmin.bat`) tanpa ketergantungan instalasi server eksternal yang rumit.
- **Desain Ramah Pengguna (UX):** Palet warna kurasi profesional modern (Slate, Navy, Indigo, Emerald), kontras teks tinggi ramah anak, dan tipografi jelas.

---

## 8. Status Pengembangan & Roadmap (Release Status)

1. **Modul Utama & Sirkulasi (Core System & Circulation):** ✅ **SELESAI (Production-Ready)**
   - Autentikasi, OPAC Katalog Terbuka, Master Buku & Anggota, Sirkulasi Peminjaman & Pengembalian, Kalkulasi Denda, dan Ekspor Laporan Multi-format (PDF, Excel, Word).
2. **Revamp Cetak Label Buku:** ✅ **SELESAI (Aktif)**
   - Opsi seleksi multi-select buku, pemilihan format label (Lengkap, Punggung DDC saja, Barcode saja), pilihan jumlah salinan (1 per judul vs per eksemplar fisik), dan standardisasi grid presisi A4 stiker.
3. **Layanan Mandiri Siswa (Self-Service Circulation Station):** ✅ **SELESAI (Aktif di `/kiosk`)**
   - Stasiun peminjaman dan pengembalian mandiri siswa berdesain resmi SIMPUS SD, mendukung scanner barcode fisik & layar sentuh, struk digital, dan umpan balik suara audio chime.
4. **Pusat Notifikasi WhatsApp Sirkulasi:** ✅ **SELESAI (Aktif di `/notifikasi`)**
   - Integrasi pesan WhatsApp otomatis (click-to-chat `api.whatsapp.com` dan direct API), pengingat jatuh tempo H-1 s/d H-3, serta pemberitahuan keterlambatan dan rincian denda langsung ke wali murid.
5. **Koleksi Buku Digital / E-Book Reader:** 🔜 *Tahap Berikutnya*
   - Penambahan fitur baca buku cerita elektronik (PDF e-book) langsung pada katalog OPAC.
6. **Sinkronisasi Dapodik Kemdikbud:** 🔜 *Tahap Berikutnya*
   - Integrasi import dan sinkronisasi data siswa otomatis dari server Data Pokok Pendidikan (Dapodik).
