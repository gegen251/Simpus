<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);

$html = <<<'HTML'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Spesifikasi Skema Database & ERD - SIMPUS</title>
    <style>
        @page {
            margin: 35px 40px 45px 40px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 10.5pt;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .header-container {
            border-bottom: 2.5px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .org-badge {
            display: inline-block;
            background-color: #e0e7ff;
            color: #3730a3;
            font-size: 8pt;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        h1 {
            font-size: 18pt;
            color: #0f172a;
            margin: 4px 0 2px 0;
            font-weight: bold;
        }
        .subtitle {
            font-size: 10pt;
            color: #475569;
            margin: 0 0 8px 0;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 14px;
            margin-top: 10px;
            font-size: 8.5pt;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 6px;
        }
        .meta-label {
            font-weight: bold;
            color: #334155;
            width: 18%;
        }
        .meta-val {
            color: #0f172a;
        }

        h2 {
            font-size: 13pt;
            color: #1e3a8a;
            border-left: 4px solid #2563eb;
            padding-left: 8px;
            margin: 22px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        h3 {
            font-size: 11pt;
            color: #0f172a;
            margin: 14px 0 6px 0;
        }
        p {
            margin: 0 0 8px 0;
            color: #334155;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 8.5pt;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #1e293b;
            font-size: 8.5pt;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            font-family: monospace;
        }
        .badge-pk { background-color: #fef08a; color: #854d0e; border: 1px solid #facc15; }
        .badge-fk { background-color: #bfdbfe; color: #1e40af; border: 1px solid #93c5fd; }
        .badge-uq { background-color: #fed7aa; color: #9a3412; border: 1px solid #fdba74; }
        .badge-req { background-color: #fee2e2; color: #991b1b; }
        .badge-opt { background-color: #f1f5f9; color: #64748b; }

        .diagram-box {
            background-color: #0f172a;
            color: #38bdf8;
            padding: 12px;
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 7.5pt;
            line-height: 1.25;
            white-space: pre;
            margin-bottom: 14px;
            border: 1px solid #334155;
        }

        .relation-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        .relation-table th {
            background-color: #0284c7;
            color: white;
            padding: 6px 8px;
            border: 1px solid #0284c7;
            text-align: left;
        }
        .relation-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
        }
        .relation-table tr:nth-child(even) {
            background-color: #f0f9ff;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            display: flex;
            justify-content: space-between;
        }
        .callout {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 8px 12px;
            font-size: 8.5pt;
            margin: 10px 0;
            border-radius: 0 4px 4px 0;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-container">
        <span class="org-badge">SD Negeri 01 Harapan Bangsa &bull; Perpustakaan Pelita Ilmu</span>
        <h1>SPESIFIKASI SKEMA DATABASE & ERD</h1>
        <div class="subtitle">Sistem Informasi Manajemen Perpustakaan Terpadu (SIMPUS) - CodeIgniter 4</div>
        
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td class="meta-label">Nama Database:</td>
                    <td class="meta-val"><code>perpustakaan</code></td>
                    <td class="meta-label">Storage Engine:</td>
                    <td class="meta-val">InnoDB (ACID Compliant)</td>
                </tr>
                <tr>
                    <td class="meta-label">RDBMS / Port:</td>
                    <td class="meta-val">MariaDB 10.4 / MySQL 8.x (Port 3307)</td>
                    <td class="meta-label">Karakter / Collation:</td>
                    <td class="meta-val">utf8mb4 / utf8mb4_general_ci</td>
                </tr>
                <tr>
                    <td class="meta-label">Framework / CLI:</td>
                    <td class="meta-val">CodeIgniter 4.7.4 (Spark Migrations)</td>
                    <td class="meta-label">Status Integritas:</td>
                    <td class="meta-val"><strong style="color: #16a34a;">100% Enforced Foreign Key Constraints</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Section 1: Entity Relationship Diagram -->
    <h2>1. DIAGRAM RELASI ENTITAS (ENTITY RELATIONSHIP DIAGRAM / ERD)</h2>
    <p>Database SIMPUS tersusun atas 7 tabel relasional yang saling terhubung dengan referential integrity ketat menggunakan mekanisme InnoDB Foreign Key:</p>

    <div class="diagram-box">
+--------------------+               +--------------------+               +--------------------+
|   kategori_buku    |               |        buku        |               |      anggota       |
+--------------------+               +--------------------+               +--------------------+
| PK id              |&lt;---+          | PK id              |&lt;---+          | PK id              |&lt;---+
|    nama_kategori   |    |          | UQ kode_buku       |    |          | UQ nomor_anggota   |    |
|    keterangan      |    |          |    judul           |    |          |    nama            |    |
+--------------------+    |          |    penulis         |    |          | UQ no_identitas    |    |
                          +----------| FK kategori_id     |    |          |    tipe_anggota    |    |
                             (1 : N) |    stok_tersedia   |    |          |    kelas           |    |
                                     +--------------------+    |          +--------------------+    |
                                                               |                                    |
                                                               | (1 : N)                            | (1 : N)
+--------------------+                                         |                                    |
|       admin        |                               +--------------------+                         |
+--------------------+                               |     peminjaman     |                         |
| PK id              |&lt;------------------------------| PK id              |-------------------------+
| UQ username        |        (1 : N)                | UQ kode_transaksi  |
| UQ email           |                               | FK anggota_id      |
|    role [admin|staf|&lt;--------+                     | FK buku_id         |
+--------------------+         |                     |    status          |
                               | (1 : N)             |    tgl_jatuh_tempo |
                               |                     +--------------------+
                               |                               |
                               |                               | (1 : 1)
                     +--------------------+                    |
                     |    pengembalian    |                    |
                     +--------------------+                    |
                     | PK id              |                    |
                     | FK peminjaman_id   |&lt;-------------------+
                     | FK admin_id        |
                     |    denda           |
                     |    status_denda    |
                     +--------------------+
    </div>

    <!-- Tabel Matriks Relasi -->
    <h3>Matriks Relasi Foreign Key (Constraint Matrix)</h3>
    <table class="relation-table">
        <thead>
            <tr>
                <th width="20%">Tabel Asal (Child)</th>
                <th width="18%">Kolom FK</th>
                <th width="20%">Tabel Rujukan (Parent)</th>
                <th width="15%">Kolom Target</th>
                <th width="12%">Kardinalitas</th>
                <th width="15%">Aturan Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>buku</strong></td>
                <td><code>kategori_id</code></td>
                <td><strong>kategori_buku</strong></td>
                <td><code>id</code></td>
                <td>N : 1 (Many-to-One)</td>
                <td>ON UPDATE CASCADE<br>ON DELETE RESTRICT</td>
            </tr>
            <tr>
                <td><strong>peminjaman</strong></td>
                <td><code>anggota_id</code></td>
                <td><strong>anggota</strong></td>
                <td><code>id</code></td>
                <td>N : 1 (Many-to-One)</td>
                <td>ON UPDATE CASCADE<br>ON DELETE RESTRICT</td>
            </tr>
            <tr>
                <td><strong>peminjaman</strong></td>
                <td><code>buku_id</code></td>
                <td><strong>buku</strong></td>
                <td><code>id</code></td>
                <td>N : 1 (Many-to-One)</td>
                <td>ON UPDATE CASCADE<br>ON DELETE RESTRICT</td>
            </tr>
            <tr>
                <td><strong>peminjaman</strong></td>
                <td><code>admin_id</code></td>
                <td><strong>admin</strong></td>
                <td><code>id</code></td>
                <td>N : 1 (Many-to-One)</td>
                <td>ON UPDATE CASCADE<br>ON DELETE RESTRICT</td>
            </tr>
            <tr>
                <td><strong>pengembalian</strong></td>
                <td><code>peminjaman_id</code></td>
                <td><strong>peminjaman</strong></td>
                <td><code>id</code></td>
                <td>1 : 1 (Unique Tx)</td>
                <td>ON UPDATE CASCADE<br>ON DELETE CASCADE</td>
            </tr>
            <tr>
                <td><strong>pengembalian</strong></td>
                <td><code>admin_id</code></td>
                <td><strong>admin</strong></td>
                <td><code>id</code></td>
                <td>N : 1 (Many-to-One)</td>
                <td>ON UPDATE CASCADE<br>ON DELETE RESTRICT</td>
            </tr>
        </tbody>
    </table>

    <div class="callout">
        <strong>Prinsip Integritas Data:</strong> Aturan <code>ON DELETE RESTRICT</code> mencegah penghapusan data master (misal: Buku atau Anggota) yang masih tercatat memiliki riwayat peminjaman aktif/historis demi keabsahan audit perpustakaan.
    </div>

    <div class="page-break"></div>

    <!-- Section 2: Data Dictionary -->
    <h2>2. KAMUS DATA & SPESIFIKASI TABEL (DATA DICTIONARY)</h2>

    <!-- Tabel 1: admin -->
    <h3>2.1. Tabel <code>admin</code> (Data Petugas & Autentikasi)</h3>
    <p>Menyimpan kredensial otorisasi sistem untuk Kepala Perpustakaan dan Staf Pustakawan.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="18%">Kolom</th>
                <th width="16%">Tipe Data</th>
                <th width="16%">Atribut / Index</th>
                <th width="12%">Default</th>
                <th width="38%">Deskripsi Bisnis</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-pk">PRIMARY KEY</span></td>
                <td>AUTO_INC</td>
                <td>Identifier unik data petugas perpustakaan.</td>
            </tr>
            <tr>
                <td><strong>nama</strong></td>
                <td>VARCHAR(100)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Nama lengkap petugas/pustakawan bertugas.</td>
            </tr>
            <tr>
                <td><strong>username</strong></td>
                <td>VARCHAR(50)</td>
                <td><span class="badge badge-uq">UNIQUE</span></td>
                <td>-</td>
                <td>ID unik login petugas ke panel administrasi.</td>
            </tr>
            <tr>
                <td><strong>email</strong></td>
                <td>VARCHAR(100)</td>
                <td><span class="badge badge-uq">UNIQUE</span></td>
                <td>-</td>
                <td>Alamat email resmi petugas untuk korespondensi.</td>
            </tr>
            <tr>
                <td><strong>password</strong></td>
                <td>VARCHAR(255)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Hash password terenkripsi kuat via BCRYPT algorithm.</td>
            </tr>
            <tr>
                <td><strong>role</strong></td>
                <td>ENUM('admin','staf')</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>'staf'</td>
                <td>Tingkatan hak akses: <em>admin</em> (penuh) atau <em>staf</em> (operasional).</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>DATETIME</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Waktu registrasi akun petugas.</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>DATETIME</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Waktu pembaruan profil atau password terakhir.</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 2: kategori_buku -->
    <h3>2.2. Tabel <code>kategori_buku</code> (Klasifikasi Koleksi)</h3>
    <p>Menyimpan data klasifikasi subjek buku sesuai kurikulum sekolah dan DDC (Dewey Decimal Classification).</p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="18%">Kolom</th>
                <th width="16%">Tipe Data</th>
                <th width="16%">Atribut / Index</th>
                <th width="12%">Default</th>
                <th width="38%">Deskripsi Bisnis</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-pk">PRIMARY KEY</span></td>
                <td>AUTO_INC</td>
                <td>Identifier kategori buku.</td>
            </tr>
            <tr>
                <td><strong>nama_kategori</strong></td>
                <td>VARCHAR(100)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Nama klasifikasi (contoh: Buku Tematik SD, Ensiklopedia, Fabel).</td>
            </tr>
            <tr>
                <td><strong>keterangan</strong></td>
                <td>TEXT</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Uraian lingkup subjek dan cakupan kelompok buku.</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>DATETIME</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Waktu pencatatan kategori.</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>DATETIME</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Waktu perubahan terakhir.</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 3: buku -->
    <h3>2.3. Tabel <code>buku</code> (Master Koleksi Buku & Inventaris)</h3>
    <p>Menyimpan metadata lengkap setiap judul pustaka beserta kontrol stok eksemplar fisik.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="18%">Kolom</th>
                <th width="16%">Tipe Data</th>
                <th width="16%">Atribut / Index</th>
                <th width="12%">Default</th>
                <th width="38%">Deskripsi Bisnis</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-pk">PRIMARY KEY</span></td>
                <td>AUTO_INC</td>
                <td>ID record master buku.</td>
            </tr>
            <tr>
                <td><strong>kode_buku</strong></td>
                <td>VARCHAR(30)</td>
                <td><span class="badge badge-uq">UNIQUE</span></td>
                <td>-</td>
                <td>Kode barcode / inventaris buku (misal: BK-0001).</td>
            </tr>
            <tr>
                <td><strong>judul</strong></td>
                <td>VARCHAR(255)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Judul utama buku pustaka.</td>
            </tr>
            <tr>
                <td><strong>penulis</strong></td>
                <td>VARCHAR(150)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Nama pengarang / penyusun buku.</td>
            </tr>
            <tr>
                <td><strong>penerbit</strong></td>
                <td>VARCHAR(150)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Badan penerbit / percetakan.</td>
            </tr>
            <tr>
                <td><strong>isbn</strong></td>
                <td>VARCHAR(50)</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Nomor ISBN standar internasional.</td>
            </tr>
            <tr>
                <td><strong>kategori_id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-fk">FK -> kategori_buku</span></td>
                <td>-</td>
                <td>Relasi klasifikasi kategori buku.</td>
            </tr>
            <tr>
                <td><strong>tahun_terbit</strong></td>
                <td>YEAR(4)</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Tahun publikasi buku.</td>
            </tr>
            <tr>
                <td><strong>jumlah_eksemplar</strong></td>
                <td>INT(11)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>1</td>
                <td>Total fisik buku yang dimiliki perpustakaan.</td>
            </tr>
            <tr>
                <td><strong>stok_tersedia</strong></td>
                <td>INT(11)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>1</td>
                <td>Sisa stok fisik di rak yang siap dipinjamkan.</td>
            </tr>
            <tr>
                <td><strong>lokasi_rak</strong></td>
                <td>VARCHAR(50)</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Kode denah / rak penyimpanan fisik buku.</td>
            </tr>
            <tr>
                <td><strong>status</strong></td>
                <td>ENUM('aktif','nonaktif')</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>'aktif'</td>
                <td>Status ketersediaan buku di katalog OPAC.</td>
            </tr>
            <tr>
                <td><strong>cover</strong></td>
                <td>VARCHAR(255)</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Path file thumbnail gambar sampul buku.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Tabel 4: anggota -->
    <h3>2.4. Tabel <code>anggota</code> (Data Keanggotaan Siswa & Guru)</h3>
    <p>Menyimpan identitas resmi pemustaka yang berhak meminjam koleksi perpustakaan.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="18%">Kolom</th>
                <th width="16%">Tipe Data</th>
                <th width="16%">Atribut / Index</th>
                <th width="12%">Default</th>
                <th width="38%">Deskripsi Bisnis</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-pk">PRIMARY KEY</span></td>
                <td>AUTO_INC</td>
                <td>Identifier keanggotaan.</td>
            </tr>
            <tr>
                <td><strong>nomor_anggota</strong></td>
                <td>VARCHAR(30)</td>
                <td><span class="badge badge-uq">UNIQUE</span></td>
                <td>-</td>
                <td>Nomor unik barcode kartu anggota (misal: AGT-2026-0001).</td>
            </tr>
            <tr>
                <td><strong>nama</strong></td>
                <td>VARCHAR(150)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Nama lengkap siswa, guru, atau staf.</td>
            </tr>
            <tr>
                <td><strong>no_identitas</strong></td>
                <td>VARCHAR(50)</td>
                <td><span class="badge badge-uq">UNIQUE</span></td>
                <td>-</td>
                <td>NISN (untuk siswa), NIP (guru), atau NIK resmi.</td>
            </tr>
            <tr>
                <td><strong>tipe_anggota</strong></td>
                <td>ENUM('siswa','guru','staf','umum')</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>'siswa'</td>
                <td>Kategori pemustaka untuk validasi aturan peminjaman.</td>
            </tr>
            <tr>
                <td><strong>kelas</strong></td>
                <td>VARCHAR(50)</td>
                <td><span class="badge badge-opt">NULL</span></td>
                <td>NULL</td>
                <td>Rombongan belajar (misal: Kelas 1A, 5B, atau Ruang Guru).</td>
            </tr>
            <tr>
                <td><strong>jenis_kelamin</strong></td>
                <td>ENUM('L','P')</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>'L'</td>
                <td>Gender anggota (Laki-laki / Perempuan).</td>
            </tr>
            <tr>
                <td><strong>kontak</strong></td>
                <td>VARCHAR(30)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Nomor WhatsApp orang tua/siswa untuk notifikasi.</td>
            </tr>
            <tr>
                <td><strong>status</strong></td>
                <td>ENUM('aktif','nonaktif')</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>'aktif'</td>
                <td>Status hak peminjaman anggota di perpustakaan.</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 5: peminjaman -->
    <h3>2.5. Tabel <code>peminjaman</code> (Transaksi Sirkulasi Peminjaman)</h3>
    <p>Mencatat setiap peristiwa peminjaman buku, durasi tenggat waktu, dan kuota perpanjangan.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="20%">Kolom</th>
                <th width="16%">Tipe Data</th>
                <th width="16%">Atribut / Index</th>
                <th width="12%">Default</th>
                <th width="36%">Deskripsi Bisnis</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-pk">PRIMARY KEY</span></td>
                <td>AUTO_INC</td>
                <td>ID transaksi sirkulasi.</td>
            </tr>
            <tr>
                <td><strong>kode_transaksi</strong></td>
                <td>VARCHAR(50)</td>
                <td><span class="badge badge-uq">UNIQUE</span></td>
                <td>-</td>
                <td>Nomor bukti pinjam (misal: TRX-20260915-001).</td>
            </tr>
            <tr>
                <td><strong>anggota_id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-fk">FK -> anggota</span></td>
                <td>-</td>
                <td>Pemustaka yang meminjam buku.</td>
            </tr>
            <tr>
                <td><strong>buku_id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-fk">FK -> buku</span></td>
                <td>-</td>
                <td>Eksemplar judul buku yang dipinjam.</td>
            </tr>
            <tr>
                <td><strong>tanggal_pinjam</strong></td>
                <td>DATE</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Tanggal buku diserahkan ke anggota.</td>
            </tr>
            <tr>
                <td><strong>tanggal_jatuh_tempo</strong></td>
                <td>DATE</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Batas akhir pengembalian sebelum kena denda.</td>
            </tr>
            <tr>
                <td><strong>status</strong></td>
                <td>ENUM('dipinjam','dikembalikan','terlambat')</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>'dipinjam'</td>
                <td>Status live sirkulasi buku.</td>
            </tr>
            <tr>
                <td><strong>jumlah_perpanjangan</strong></td>
                <td>INT(11)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>0</td>
                <td>Frekuensi perpanjangan durasi pinjam (max 1x).</td>
            </tr>
            <tr>
                <td><strong>admin_id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-fk">FK -> admin</span></td>
                <td>-</td>
                <td>Petugas yang memproses transaksi peminjaman.</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 6: pengembalian -->
    <h3>2.6. Tabel <code>pengembalian</code> (Transaksi Pengembalian & Perhitungan Denda)</h3>
    <p>Mencatat pengembalian fisik buku ke rak serta perhitungan denda keterlambatan secara otomatis.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="20%">Kolom</th>
                <th width="16%">Tipe Data</th>
                <th width="16%">Atribut / Index</th>
                <th width="12%">Default</th>
                <th width="36%">Deskripsi Bisnis</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-pk">PRIMARY KEY</span></td>
                <td>AUTO_INC</td>
                <td>ID pengembalian.</td>
            </tr>
            <tr>
                <td><strong>peminjaman_id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-fk">FK -> peminjaman</span></td>
                <td>-</td>
                <td>Relasi referensi transaksi peminjaman asal.</td>
            </tr>
            <tr>
                <td><strong>tanggal_kembali</strong></td>
                <td>DATE</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>-</td>
                <td>Tanggal aktual buku diterima kembali di konter.</td>
            </tr>
            <tr>
                <td><strong>jumlah_hari_terlambat</strong></td>
                <td>INT(11)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>0</td>
                <td>Selisih hari keterlambatan terhadap jatuh tempo.</td>
            </tr>
            <tr>
                <td><strong>denda</strong></td>
                <td>DECIMAL(12,2)</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>0.00</td>
                <td>Total nominal denda (Hari Terlambat &times; Tarif Denda).</td>
            </tr>
            <tr>
                <td><strong>status_denda</strong></td>
                <td>ENUM('lunas','belum_lunas','tidak_ada')</td>
                <td><span class="badge badge-req">NOT NULL</span></td>
                <td>'tidak_ada'</td>
                <td>Status rekonsiliasi kas pembayaran denda.</td>
            </tr>
            <tr>
                <td><strong>admin_id</strong></td>
                <td>INT(11) UNSIGNED</td>
                <td><span class="badge badge-fk">FK -> admin</span></td>
                <td>-</td>
                <td>Petugas yang menerima pengembalian buku.</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 7: pengaturan -->
    <h3>2.7. Tabel <code>pengaturan</code> (Konfigurasi Dinamis Sistem)</h3>
    <p>Menyimpan parameter operasional sekolah dasar yang dapat diatur sewaktu-waktu oleh administrator.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="22%">Kolom (Kunci Parameter)</th>
                <th width="18%">Tipe Data</th>
                <th width="15%">Nilai Standar</th>
                <th width="45%">Kegunaan Parameter</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>nama_perpustakaan</strong></td>
                <td>VARCHAR(50) UQ</td>
                <td>Perpustakaan Pelita Ilmu</td>
                <td>Nama kop laporan resmi PDF, Excel, dan Kartu Anggota.</td>
            </tr>
            <tr>
                <td><strong>durasi_pinjam_default</strong></td>
                <td>VARCHAR(50) UQ</td>
                <td>5 (Hari)</td>
                <td>Standar lama pinjaman buku bagi siswa sekolah dasar.</td>
            </tr>
            <tr>
                <td><strong>tarif_denda_per_hari</strong></td>
                <td>VARCHAR(50) UQ</td>
                <td>Rp 500,-</td>
                <td>Besaran tarif sanksi keterlambatan per hari ramah anak.</td>
            </tr>
            <tr>
                <td><strong>max_pinjam_buku</strong></td>
                <td>VARCHAR(50) UQ</td>
                <td>2 (Eksemplar)</td>
                <td>Batas maksimal kuota pinjaman aktif simultan per siswa.</td>
            </tr>
        </tbody>
    </table>

    <div class="callout" style="margin-top: 15px;">
        <strong>Dokumentasi Diterbitkan:</strong> Dokumen ini merupakan spesifikasi teknis resmi arsitektur basis data Sistem Informasi Manajemen Perpustakaan Terpadu (SIMPUS) SD Negeri 01 Harapan Bangsa.
    </div>

</body>
</html>
HTML;

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$output = $dompdf->output();
$outputPath = __DIR__ . '/Database_Schema_Perpustakaan.pdf';
file_put_contents($outputPath, $output);

echo "SUCCESS: PDF Database Schema successfully generated at: " . $outputPath . "\n";
echo "File Size: " . round(filesize($outputPath) / 1024, 2) . " KB\n";
