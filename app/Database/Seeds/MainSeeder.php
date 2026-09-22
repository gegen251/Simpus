<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        // 1. Admin default (tetap dapat login dengan admin / admin123)
        $db->table('admin')->truncate();
        $db->table('admin')->insert([
            'id'         => 1,
            'nama'       => 'Pustakawan SD',
            'username'   => 'admin',
            'email'      => 'pustaka@sdn12sumbawa.sch.id',
            'password'   => password_hash('admin123', PASSWORD_BCRYPT),
            'role'       => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Pengaturan Default Sekolah Dasar
        $db->table('pengaturan')->truncate();
        $pengaturan = [
            ['kunci' => 'nama_perpustakaan', 'nilai' => 'SD Negeri 12 Sumbawa', 'keterangan' => 'Nama instansi sekolah dasar untuk kop laporan resmi'],
            ['kunci' => 'alamat_perpustakaan', 'nilai' => 'Jl. Sumbawa, Sungai Duri, Kec. Sungai Raya, Kabupaten Bengkayang, Kalimantan Barat 79271', 'keterangan' => 'Alamat lengkap sekolah dasar'],
            ['kunci' => 'kepala_perpustakaan', 'nilai' => 'Dra. Hj. Sri Wahyuni, M.Pd (Kepala Sekolah Contoh)', 'keterangan' => 'Nama Kepala Sekolah / Penanggung Jawab Perpustakaan'],
            ['kunci' => 'nip_kepala', 'nilai' => '19800101 200501 1 001', 'keterangan' => 'NIP Kepala Sekolah (Data Dummy)'],
            ['kunci' => 'durasi_pinjam_default', 'nilai' => '5', 'keterangan' => 'Durasi peminjaman buku siswa SD (hari)'],
            ['kunci' => 'tarif_denda_per_hari', 'nilai' => '500', 'keterangan' => 'Tarif denda keterlambatan per hari (Rupiah ramah anak)'],
            ['kunci' => 'max_pinjam_buku', 'nilai' => '2', 'keterangan' => 'Maksimal buku dipinjam aktif per siswa SD'],
        ];
        foreach ($pengaturan as $p) {
            $p['created_at'] = $now;
            $p['updated_at'] = $now;
            $db->table('pengaturan')->insert($p);
        }

        // 3. Kategori Buku Sekolah Dasar
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->table('pengembalian')->truncate();
        $db->table('peminjaman')->truncate();
        $db->table('buku')->truncate();
        $db->table('kategori_buku')->truncate();
        $db->table('anggota')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        $kategori = [
            ['id' => 1, 'nama_kategori' => 'Buku Tematik & Kurikulum Merdeka', 'keterangan' => 'Buku teks pelajaran resmi siswa SD/MI Kelas 1 sampai Kelas 6'],
            ['id' => 2, 'nama_kategori' => 'Cerita Anak & Dongeng Nusantara', 'keterangan' => 'Fabel binatang, cerita rakyat 34 provinsi, dan dongeng budi pekerti'],
            ['id' => 3, 'nama_kategori' => 'Komik Edukasi & Cerita Bergambar', 'keterangan' => 'Komik sains cilik, petualangan sejarah, dan cerita bergambar interaktif'],
            ['id' => 4, 'nama_kategori' => 'Ensiklopedia Cilik & Sains Anak', 'keterangan' => 'Mengenal satwa, dinosaurus, tata surya, tubuh manusia, dan alam sekitar'],
            ['id' => 5, 'nama_kategori' => 'Pendidikan Karakter & Agama', 'keterangan' => 'Kisah teladan nabi & sahabat, akhlak anak sholeh, dan nilai-nilai Pancasila'],
            ['id' => 6, 'nama_kategori' => 'Kamus Bergambar & Bahasa', 'keterangan' => 'Kamus cilik 3 bahasa (Indonesia - Inggris - Arab) dan latihan membaca'],
            ['id' => 7, 'nama_kategori' => 'Karya Pendidik & Pegangan Guru', 'keterangan' => 'Modul ajar literasi-numerasi dan buku pedoman guru wali kelas SD'],
        ];
        foreach ($kategori as $k) {
            $k['created_at'] = $now;
            $k['updated_at'] = $now;
            $db->table('kategori_buku')->insert($k);
        }

        // 4. Data Koleksi Buku Sekolah Dasar (Tepat 2 Data Dummy Sesuai Permintaan)
        $buku = [
            [
                'id' => 1,
                'kode_buku' => 'BK-0001',
                'judul' => 'Bahasa Indonesia: Lihat Sekitar untuk SD Kelas 4 (Kurikulum Merdeka)',
                'penulis' => 'Eva Y. Nukman & Cicilia Erum',
                'penerbit' => 'Pusat Kurikulum dan Perbukuan Kemendikbudristek',
                'isbn' => '978-602-244-370-4',
                'kategori_id' => 1,
                'tahun_terbit' => 2023,
                'jumlah_eksemplar' => 12,
                'stok_tersedia' => 11, // 1 sedang dipinjam
                'lokasi_rak' => 'Rak Tematik-01',
                'status' => 'aktif',
                'deskripsi' => 'Buku teks utama pembelajaran Bahasa Indonesia kurikulum merdeka untuk siswa SD kelas 4.',
                'cover' => 'images/covers/cover_bk0001.jpg',
            ],
            [
                'id' => 2,
                'kode_buku' => 'BK-0002',
                'judul' => 'Kumpulan Dongeng Nusantara 34 Provinsi Penuh Pesan Moral',
                'penulis' => 'Kak Rian & Tim Sahabat Cilik',
                'penerbit' => 'Bhuana Ilmu Populer (BIP)',
                'isbn' => '978-623-216-455-1',
                'kategori_id' => 2,
                'tahun_terbit' => 2022,
                'jumlah_eksemplar' => 8,
                'stok_tersedia' => 7, // 1 sedang dipinjam (terlambat)
                'lokasi_rak' => 'Rak Cerita-A1',
                'status' => 'aktif',
                'deskripsi' => 'Kisah cerita rakyat nusantara dari Sabang sampai Merauke yang menanamkan kejujuran, tolong-menolong, dan kerja keras.',
                'cover' => 'images/covers/cover_bk0002.jpg',
            ],
        ];
        foreach ($buku as $b) {
            $b['created_at'] = $now;
            $b['updated_at'] = $now;
            $db->table('buku')->insert($b);
        }

        // 5. Data Anggota Siswa & Dewan Guru Sekolah Dasar
        // 5. Data Anggota Siswa & Dewan Guru Sekolah Dasar (100% Mock / Dummy Data)
        $anggota = [
            [
                'id' => 1,
                'nomor_anggota' => 'AG-1001',
                'nama' => 'Ahmad Rayyan Al-Farizi (Kelas 4A)',
                'no_identitas' => 'NISN-0000000001',
                'jenis_kelamin' => 'L',
                'kontak' => '081200000001',
                'email' => 'siswa1.dummy@example.sch.id',
                'alamat' => 'Siswa Kelas 4A - Jl. Melati No. 5, RT 02/RW 04',
                'status' => 'aktif',
            ],
            [
                'id' => 2,
                'nomor_anggota' => 'AG-1002',
                'nama' => 'Nayla Zahra Putri (Kelas 5B)',
                'no_identitas' => 'NISN-0000000002',
                'jenis_kelamin' => 'P',
                'kontak' => '081200000002',
                'email' => 'siswa2.dummy@example.sch.id',
                'alamat' => 'Siswi Kelas 5B - Jl. Kenanga No. 12, RT 01/RW 03',
                'status' => 'aktif',
            ],
            [
                'id' => 3,
                'nomor_anggota' => 'AG-1003',
                'nama' => 'Dimas Arya Pratama (Kelas 3C)',
                'no_identitas' => 'NISN-0000000003',
                'jenis_kelamin' => 'L',
                'kontak' => '081200000003',
                'email' => 'siswa3.dummy@example.sch.id',
                'alamat' => 'Siswa Kelas 3C - Jl. Mawar Indah Blok B-8',
                'status' => 'aktif',
            ],
            [
                'id' => 4,
                'nomor_anggota' => 'AG-1004',
                'nama' => 'Siti Aisyah Azzahra (Kelas 6A)',
                'no_identitas' => 'NISN-0000000004',
                'jenis_kelamin' => 'P',
                'kontak' => '081200000004',
                'email' => 'siswa4.dummy@example.sch.id',
                'alamat' => 'Siswi Kelas 6A - Jl. Dahlia Elok No. 14',
                'status' => 'aktif',
            ],
            [
                'id' => 5,
                'nomor_anggota' => 'AG-2001',
                'nama' => 'Ibu Rina Tri Wulandari, S.Pd (Wali Kelas 4A)',
                'no_identitas' => '19850101 201001 2 001',
                'jenis_kelamin' => 'P',
                'kontak' => '081200000005',
                'email' => 'guru1.dummy@example.sch.id',
                'alamat' => 'Guru Wali Kelas 4A - Ruang Guru SD',
                'status' => 'aktif',
            ],
            [
                'id' => 6,
                'nomor_anggota' => 'AG-2002',
                'nama' => 'Bapak Budi Santoso, S.Pd (Guru Olahraga & Pembina Pramuka)',
                'no_identitas' => '19850202 201001 1 002',
                'jenis_kelamin' => 'L',
                'kontak' => '081200000006',
                'email' => 'guru2.dummy@example.sch.id',
                'alamat' => 'Guru PJOK & Pramuka - Ruang Guru SD',
                'status' => 'aktif',
            ],
        ];
        foreach ($anggota as $a) {
            $a['created_at'] = $now;
            $a['updated_at'] = $now;
            $db->table('anggota')->insert($a);
        }

        // 6. Transaksi Sirkulasi Peminjaman & Pengembalian Ramah Anak
        // Transaksi 1: Aktif dipinjam siswa Kelas 4A
        $pinjam1Date = date('Y-m-d', strtotime('-2 days'));
        $tempo1Date  = date('Y-m-d', strtotime('+3 days'));
        $db->table('peminjaman')->insert([
            'id' => 1,
            'kode_transaksi' => 'TR-' . date('Ym') . '-0001',
            'anggota_id' => 1, // Rayyan
            'buku_id' => 1,    // Buku Tematik B. Indonesia Kelas 4
            'tanggal_pinjam' => $pinjam1Date,
            'tanggal_jatuh_tempo' => $tempo1Date,
            'status' => 'dipinjam',
            'admin_id' => 1,
            'catatan' => 'Peminjaman buku tematik untuk tugas membaca mandiri di kelas',
            'created_at' => $pinjam1Date . ' 08:30:00',
            'updated_at' => $pinjam1Date . ' 08:30:00',
        ]);

        // Transaksi 2: Terlambat siswa Kelas 5B (Dongeng Nusantara)
        $pinjam2Date = date('Y-m-d', strtotime('-9 days'));
        $tempo2Date  = date('Y-m-d', strtotime('-4 days'));
        $db->table('peminjaman')->insert([
            'id' => 2,
            'kode_transaksi' => 'TR-' . date('Ym') . '-0002',
            'anggota_id' => 2, // Nayla
            'buku_id' => 2,    // Kumpulan Dongeng Nusantara
            'tanggal_pinjam' => $pinjam2Date,
            'tanggal_jatuh_tempo' => $tempo2Date,
            'status' => 'terlambat',
            'admin_id' => 1,
            'catatan' => 'Peminjaman bacaan santai akhir pekan',
            'created_at' => $pinjam2Date . ' 09:15:00',
            'updated_at' => $pinjam2Date . ' 09:15:00',
        ]);

        // Transaksi 3: Sudah dikembalikan oleh siswa Kelas 3C dengan denda edukatif Rp 500
        $pinjam3Date  = date('Y-m-d', strtotime('-12 days'));
        $tempo3Date   = date('Y-m-d', strtotime('-7 days'));
        $kembali3Date = date('Y-m-d', strtotime('-6 days'));
        $db->table('peminjaman')->insert([
            'id' => 3,
            'kode_transaksi' => 'TR-' . date('Ym') . '-0003',
            'anggota_id' => 3, // Dimas
            'buku_id' => 1,    // Buku Tematik B. Indonesia Kelas 4
            'tanggal_pinjam' => $pinjam3Date,
            'tanggal_jatuh_tempo' => $tempo3Date,
            'status' => 'dikembalikan',
            'admin_id' => 1,
            'catatan' => 'Buku dikembalikan dalam kondisi bersih dan bersampul rapi',
            'created_at' => $pinjam3Date . ' 10:00:00',
            'updated_at' => $kembali3Date . ' 08:20:00',
        ]);

        $db->table('pengembalian')->insert([
            'id' => 1,
            'peminjaman_id' => 3,
            'tanggal_kembali' => $kembali3Date,
            'jumlah_hari_terlambat' => 1,
            'denda' => 500.00,
            'status_denda' => 'lunas',
            'admin_id' => 1,
            'catatan' => 'Denda keterlambatan 1 hari (Rp 500) lunas dibayarkan ke kas perpustakaan',
            'created_at' => $kembali3Date . ' 08:20:00',
            'updated_at' => $kembali3Date . ' 08:20:00',
        ]);
    }
}
