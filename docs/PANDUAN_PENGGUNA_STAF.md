# BUKU PANDUAN PENGGUNA (STAF & PUSTAKAWAN)
## SIMPUS — Sistem Informasi Manajemen Perpustakaan Sekolah Terpadu

---

## 1. Pendahuluan & Akses Sistem

Buku panduan ini disusun sebagai acuan operasional bagi Pustakawan dan Staf Administrasi dalam mengelola sirkulasi, inventarisasi buku, keanggotaan siswa, serta pelaporan di perpustakaan sekolah dasar.

### 1.1. Membuka Aplikasi
1. Buka peramban web (Google Chrome, Microsoft Edge, atau Mozilla Firefox).
2. Ketik alamat URL: `http://localhost:8080/login`
3. Masukkan **Username** dan **Password** yang telah didaftarkan.
4. Klik tombol **"Masuk ke Sistem"**.

### 1.2. Tingkatan Hak Akses
- **Administrator Utama**: Mengelola seluruh modul, termasuk menambah akun staf pustaka, mengatur tarif denda & durasi pinjam, melakukan backup data, dan memantau audit log.
- **Petugas / Staf**: Mengelola sirkulasi peminjaman/pengembalian, inventaris buku, data anggota siswa, dan mencetak laporan sirkulasi harian/bulanan.

---

## 2. Navigasi Antarmuka Utama

1. **Sidebar Menu (Navigasi Kiri)**:
   - **Dashboard**: Statistik ringkas (total buku, anggota aktif, buku sedang dipinjam, keterlambatan).
   - **Koleksi Buku**: Daftar inventaris, tambah buku baru, import CSV, dan cetak barcode label rak.
   - **Kategori Buku**: Klasifikasi subjek (Pelajaran, Dongeng, Sains, dll).
   - **Data Anggota**: Direktori siswa dan guru, cetak kartu anggota ber-barcode.
   - **Sirkulasi Peminjaman**: Transaksi peminjaman buku di meja layanan.
   - **Sirkulasi Pengembalian**: Transaksi pengembalian dan pelunasan denda.
   - **Laporan Perpustakaan**: Rekapitulasi transaksi dan ekspor ke Excel/Word/PDF.
   - **Pengaturan & Staf** *(Khusus Admin)*: Konfigurasi sistem dan manajemen akun.
2. **Topbar (Bagian Atas)**:
   - Tombol toggle **Mode Terang / Gelap** (Dark Mode).
   - Lencana Notifikasi peminjaman yang mendekati jatuh tempo.
   - Tombol cepat menuju **Katalog Publik (OPAC)** dan **Terminal Kios**.
   - Menu profil pengguna dan tombol Logout.

---

## 3. Modul Manajemen Koleksi Buku

### 3.1. Menambah Buku Baru
1. Masuk ke menu **Koleksi Buku** -> Klik tombol **"+ Tambah Buku"**.
2. Isi formulir dengan lengkap:
   - **Kode Buku**: Dibuat otomatis atau ketik format unik (contoh: `BK-0025`).
   - **Judul Buku**: Judul lengkap sesuai sampul.
   - **Penulis & Penerbit**: Nama pengarang dan penerbit.
   - **Tahun Terbit & ISBN**: Tahun cetak dan nomor identitas internasional buku.
   - **Kategori**: Pilih kategori yang sesuai (misalnya: *Buku Pelajaran SD*).
   - **Jumlah Eksemplar**: Jumlah total buku fisik yang dimiliki perpustakaan.
   - **Lokasi Rak**: Posisi lemari/rak penyimpanan (contoh: `Rak A-02`).
   - **Unggah Cover**: Pilih gambar sampul buku (format JPG/PNG maks 2MB).
3. Klik **"Simpan Data Buku"**. Sistem akan otomatis meng-invalidasi cache katalog publik agar buku baru langsung tampil.

### 3.2. Mencetak Label & Barcode Buku
1. Pada tabel buku, klik ikon **"Cetak Label"** pada buku yang diinginkan (atau centang beberapa buku untuk cetak massal).
2. Sistem akan membuka pratinjau cetak yang memuat:
   - Logo resmi sekolah.
   - Kode Buku dan Barcode CODE128 siap scan.
   - Call number / Lokasi Rak.
3. Klik tombol **"Cetak"** (gunakan printer label stiker ukuran standar 3x5 cm).
4. Tempelkan stiker pada punggung buku bagian bawah dan sampul belakang buku.

### 3.3. Import Data Buku Massal (CSV)
1. Klik tombol **"Import CSV"** di sudut kanan atas halaman buku.
2. Unduh template CSV resmi yang disediakan sistem.
3. Buka file CSV di Microsoft Excel, isi baris data buku sesuai kolom, lalu simpan.
4. Unggah kembali file CSV ke sistem -> Klik **"Mulai Import"**.

---

## 4. Modul Manajemen Keanggotaan Siswa

### 4.1. Mendaftarkan Anggota Baru
1. Masuk ke menu **Data Anggota** -> Klik **"+ Tambah Anggota"**.
2. Isi data siswa:
   - **Nomor Anggota**: Kode kartu unik (misal: `AG-1050`).
   - **NISN**: Nomor Induk Siswa Nasional (kritis untuk scanner kios).
   - **Nama Lengkap & Kelas**: Identitas lengkap siswa (misal: `Ahmad Pratama`, `Kelas 4-A`).
   - **Tanggal Kadaluarsa**: Tanggal akhir berlakunya kartu (otomatis 1 tahun).
3. Klik **"Simpan Anggota"**.

### 4.2. Mencetak Kartu Anggota Ber-Barcode
1. Pada baris data anggota, klik tombol **"Cetak Kartu"**.
2. Halaman cetak kartu memuat:
   - Identitas Sekolah & Kartu Perpustakaan Digital.
   - Nama Siswa, NISN, Kelas.
   - Barcode NISN resolusi tinggi yang dapat dipindai langsung oleh scanner barcode USB.
3. Cetak menggunakan kertas tebal/kartu PVC untuk dibagikan kepada siswa.

---

## 5. Meja Sirkulasi Peminjaman Buku

1. Masuk ke menu **Sirkulasi Peminjaman** -> Klik **"+ Transaksi Pinjam"**.
2. **Pilih Anggota**: Pindai barcode kartu siswa dengan scanner (atau ketik nama/NISN).
   - *Catatan*: Sistem akan otomatis memeriksa kuota pinjam. Jika siswa masih meminjam 2 buku atau memiliki denda belum lunas, sistem akan menolak transaksi.
3. **Pilih Buku**: Pindai barcode buku yang akan dipinjam.
   - *Catatan*: Jika stok buku habis (`stok_tersedia = 0`), transaksi akan dicegat.
4. Tentukan **Tanggal Pinjam** dan **Tanggal Jatuh Tempo** (default: 7 hari).
5. Klik **"Proses Peminjaman"**.
   - Sistem secara atomik mengurangi `stok_tersedia` buku sebanyak 1 eksemplar.
   - Transaksi tercatat dengan kode unik `TRX-YYYYMMDD-XXXX`.
6. Tawarkan struk bukti peminjaman kepada siswa.

---

## 6. Meja Sirkulasi Pengembalian & Denda

1. Masuk ke menu **Sirkulasi Pengembalian**.
2. Cari peminjaman aktif dengan memindai kode transaksi atau barcode buku yang dikembalikan.
3. Klik tombol **"Proses Kembali"**.
4. Sistem otomatis membandingkan tanggal hari ini dengan tanggal jatuh tempo:
   - **Tepat Waktu / Sebelum Tempo**: Hari Terlambat = `0`, Denda = `Rp 0`. Status denda: `Tidak Ada`.
   - **Terlambat**: Sistem menghitung selisih hari keterlambatan dan mengalikannya dengan tarif denda harian (misal: Terlambat 3 hari x Rp 500 = `Rp 1.500`).
5. Jika ada denda:
   - Pilih status pembayaran: **"Lunas"** (jika siswa langsung membayar uang kas) atau **"Belum Lunas"** (jika dicatat sebagai tunggakan).
6. Klik **"Simpan Pengembalian"**.
   - Status peminjaman berubah menjadi `dikembalikan`.
   - Stok buku di rak otomatis bertambah kembali (`stok_tersedia + 1`).
   - Cache katalog publik langsung diperbarui.

---

## 7. Perpanjangan Masa Peminjaman

1. Masuk ke menu **Sirkulasi Peminjaman**.
2. Temukan transaksi buku yang ingin diperpanjang.
3. Klik tombol **"Perpanjang"**.
4. **Ketentuan Perpanjangan**:
   - Hanya dapat dilakukan **maksimal 1 kali** untuk setiap peminjaman.
   - Tidak dapat dilakukan jika buku sudah melewati tanggal jatuh tempo (terlambat). Siswa harus mengembalikan buku terlebih dahulu dan melunasi denda jika ada.
5. Klik konfirmasi. Tanggal jatuh tempo akan otomatis bertambah 7 hari kalender.

---

## 8. Laporan & Ekspor Dokumen Resmi

1. Masuk ke menu **Laporan Perpustakaan**.
2. Tentukan filter rentang tanggal (Harian, Mingguan, Bulanan, atau Tahunan).
3. Pilih jenis laporan:
   - **Laporan Sirkulasi Peminjaman & Pengembalian**
   - **Laporan Keterlambatan & Rekapitulasi Denda Kas**
   - **Laporan Inventaris Stok Buku Per Kategori**
4. Klik format ekspor yang diinginkan:
   - **Export to Excel (.xls)**: Menghasilkan spreadsheet rapi dengan formula dan border lengkap untuk arsip pembukuan.
   - **Export to Word (.doc)**: Dokumen siap cetak dengan kop surat resmi sekolah untuk lampiran laporan dinas.
   - **Export to PDF**: Dokumen pratinjau portabel berkualitas tinggi.

---

## 9. Pemeliharaan Sistem & Tindakan Darurat

1. **Pembersihan Cache Manual**:
   Jika data buku di katalog publik tidak sinkron setelah perbaikan data, jalankan di terminal:
   ```bash
   php spark cache:invalidate-katalog
   ```
2. **Backup Basis Data**:
   Untuk mencadangkan seluruh data sebelum tutup buku tahun ajaran:
   ```bash
   php spark db:backup
   ```
   File `.sql` akan tersimpan aman di folder `backups/`.
3. **Pembersihan Log Terjadwal**:
   Untuk merotasi dan mengompres log lama:
   ```bash
   php spark logs:rotate
   ```
