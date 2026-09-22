<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\BukuModel;
use App\Models\PeminjamanModel;
use App\Models\PengembalianModel;
use App\Models\PengaturanModel;
use App\Models\AdminModel;

class Kiosk extends BaseController
{
    protected $anggotaModel;
    protected $bukuModel;
    protected $peminjamanModel;
    protected $pengembalianModel;
    protected $pengaturanModel;
    protected $adminModel;

    public function __construct()
    {
        $this->anggotaModel      = new AnggotaModel();
        $this->bukuModel         = new BukuModel();
        $this->peminjamanModel   = new PeminjamanModel();
        $this->pengembalianModel = new PengembalianModel();
        $this->pengaturanModel   = new PengaturanModel();
        $this->adminModel        = new AdminModel();
    }

    public function index()
    {
        $config = $this->pengaturanModel->getSemuaMap();
        $data = [
            'title'  => 'Layanan Mandiri Siswa - ' . ($config['nama_perpustakaan'] ?? 'SIMPUS'),
            'config' => $config,
        ];
        return view('kiosk/index', $data);
    }

    // API Cek Anggota via Barcode Kartu atau NISN
    public function apiCekAnggota()
    {
        $identifier = trim($this->request->getVar('identifier') ?? '');
        if (empty($identifier)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan scan atau masukkan nomor kartu anggota / NISN.']);
        }

        // 1. Cari berdasarkan nomor_anggota atau no_identitas (tepat atau NISN)
        $cleanDigits = preg_replace('/[^0-9]/', '', $identifier);
        $anggota = $this->anggotaModel
            ->groupStart()
                ->where('nomor_anggota', $identifier)
                ->orWhere('no_identitas', $identifier)
                ->orWhere('no_identitas', 'NISN-' . $identifier)
            ->groupEnd()
            ->first();

        // 2. Fallback pencarian NISN parsial jika memasukkan digit angka saja
        if (!$anggota && strlen($cleanDigits) >= 6) {
            $anggota = $this->anggotaModel
                ->like('no_identitas', $cleanDigits)
                ->first();
        }

        if (!$anggota) {
            // Cek apakah pengguna tidak sengaja men-scan Barcode BUKU di langkah Kartu Anggota
            $cleanIsbn = str_replace(['-', ' '], '', $identifier);
            $buku = $this->bukuModel
                ->groupStart()
                    ->where('kode_buku', $identifier)
                    ->orWhere('isbn', $identifier)
                    ->orWhere("REPLACE(REPLACE(isbn, '-', ''), ' ', '')", $cleanIsbn)
                ->groupEnd()
                ->first();

            if ($buku) {
                return $this->response->setJSON([
                    'success' => false,
                    'is_buku' => true,
                    'message' => "Kode '{$identifier}' adalah Barcode Buku ('{$buku['judul']}'). Pada langkah 1 ini, silakan scan KARTU ANGGOTA terlebih dahulu. Jika ingin mengembalikan buku, silakan klik tab 'Pengembalian Mandiri'."
                ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => "Kartu Anggota / NISN '{$identifier}' tidak terdaftar di sistem perpustakaan."]);
        }

        if ($anggota['status'] !== 'aktif') {
            return $this->response->setJSON(['success' => false, 'message' => 'Status keanggotaan Anda saat ini nonaktif. Silakan hubungi petugas perpustakaan.']);
        }

        // Cek tanggungan denda belum lunas
        $unpaidFines = $this->pengembalianModel
            ->join('peminjaman', 'peminjaman.id = pengembalian.peminjaman_id')
            ->where('peminjaman.anggota_id', $anggota['id'])
            ->where('pengembalian.status_denda', 'belum_lunas')
            ->countAllResults();

        if ($unpaidFines > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda memiliki tanggungan denda keterlambatan yang belum lunas. Harap selesaikan administrasi denda di meja pustakawan sebelum dapat meminjam buku kembali.'
            ]);
        }

        // Ambil peminjaman aktif
        $pinjamanAktif = $this->peminjamanModel
            ->select('peminjaman.*, buku.judul, buku.kode_buku, buku.cover')
            ->join('buku', 'buku.id = peminjaman.buku_id')
            ->where('anggota_id', $anggota['id'])
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
            ->findAll();

        $config = $this->pengaturanModel->getSemuaMap();
        $maxPinjam = (int)($config['max_pinjam_buku'] ?? 2);
        $activeCount = count($pinjamanAktif);
        $sisaKuota = max(0, $maxPinjam - $activeCount);

        // Mask PII: jangan ekspos kontak/email/alamat di layar umum Kiosk
        $safeAnggota = [
            'id'            => $anggota['id'],
            'nomor_anggota' => $anggota['nomor_anggota'],
            'nama'          => $anggota['nama'],
            'tipe_anggota'  => $anggota['tipe_anggota'],
            'kelas'         => $anggota['kelas'],
            'foto'          => $anggota['foto'] ?? null,
            'status'        => $anggota['status'],
        ];

        return $this->response->setJSON([
            'success'        => true,
            'anggota'        => $safeAnggota,
            'pinjaman_aktif' => $pinjamanAktif,
            'active_count'   => $activeCount,
            'sisa_kuota'     => $sisaKuota,
            'max_pinjam'     => $maxPinjam,
        ]);
    }

    // API Cek Buku via Barcode (Kode Buku Internal atau ISBN Penerbit)
    public function apiCekBuku()
    {
        $kodeBuku = trim($this->request->getVar('kode_buku') ?? '');
        if (empty($kodeBuku)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan scan atau masukkan kode barcode buku.']);
        }

        // Dukung pencarian kode_buku (BK-XXXX), ISBN resmi, atau angka ISBN tanpa strip
        $cleanIsbn = str_replace(['-', ' '], '', $kodeBuku);

        $buku = $this->bukuModel
            ->select('buku.*, kategori_buku.nama_kategori')
            ->join('kategori_buku', 'kategori_buku.id = buku.kategori_id', 'left')
            ->groupStart()
                ->where('kode_buku', $kodeBuku)
                ->orWhere('buku.isbn', $kodeBuku)
                ->orWhere("REPLACE(REPLACE(buku.isbn, '-', ''), ' ', '')", $cleanIsbn)
            ->groupEnd()
            ->first();

        if (!$buku) {
            // Cek apakah pengguna tidak sengaja men-scan Kartu Anggota di langkah Buku
            $anggota = $this->anggotaModel
                ->where('nomor_anggota', $kodeBuku)
                ->orWhere('no_identitas', $kodeBuku)
                ->first();

            if ($anggota) {
                return $this->response->setJSON([
                    'success'    => false,
                    'is_anggota' => true,
                    'message'    => "Kode '{$kodeBuku}' adalah Barcode Kartu Anggota ('{$anggota['nama']}'). Di langkah ini, silakan scan BARCODE BUKU yang ingin dipinjam."
                ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => "Buku dengan kode/barcode '{$kodeBuku}' tidak ditemukan di katalog perpustakaan."]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'buku'      => $buku,
            'tersedia'  => ((int)$buku['stok_tersedia'] > 0),
        ]);
    }

    // API Eksekusi Peminjaman Mandiri
    public function apiProsesPinjam()
    {
        $anggotaId = (int)$this->request->getVar('anggota_id');
        $bukuId    = (int)$this->request->getVar('buku_id');

        if (empty($anggotaId) || empty($bukuId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data anggota atau buku tidak lengkap.']);
        }

        $anggota = $this->anggotaModel->find($anggotaId);
        if (!$anggota || $anggota['status'] !== 'aktif') {
            return $this->response->setJSON(['success' => false, 'message' => 'Anggota tidak valid atau berstatus nonaktif.']);
        }

        // Cek tanggungan denda belum lunas
        $unpaidFines = $this->pengembalianModel
            ->join('peminjaman', 'peminjaman.id = pengembalian.peminjaman_id')
            ->where('peminjaman.anggota_id', $anggotaId)
            ->where('pengembalian.status_denda', 'belum_lunas')
            ->countAllResults();

        if ($unpaidFines > 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Peminjaman ditolak: Anda masih memiliki tanggungan denda belum lunas di perpustakaan.']);
        }

        $config = $this->pengaturanModel->getSemuaMap();
        $maxPinjam = (int)($config['max_pinjam_buku'] ?? 2);
        $durasi    = (int)($config['durasi_pinjam_default'] ?? 5);

        // Cek jumlah pinjaman aktif
        $activeCount = $this->peminjamanModel
            ->where('anggota_id', $anggotaId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->countAllResults();

        if ($activeCount >= $maxPinjam) {
            return $this->response->setJSON(['success' => false, 'message' => "Batas maksimal peminjaman Anda ($maxPinjam buku) telah tercapai."]);
        }

        // Cek apakah buku yang sama sedang dipinjam
        $alreadyBorrowed = $this->peminjamanModel
            ->where('anggota_id', $anggotaId)
            ->where('buku_id', $bukuId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->first();

        if ($alreadyBorrowed) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda sedang meminjam judul buku ini dan belum mengembalikannya.']);
        }

        // Cek stok buku
        $buku = $this->bukuModel->find($bukuId);
        if (!$buku || (int)$buku['stok_tersedia'] <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stok buku ini sedang kosong di rak perpustakaan.']);
        }

        // Cari admin default / petugas kiosk
        $admin = $this->adminModel->first();
        $adminId = $admin['id'] ?? 1;

        // Generate Kode Transaksi Unik
        $tglHariIni = date('Ymd');
        $lastTrx = $this->peminjamanModel->like('kode_transaksi', "TRX-{$tglHariIni}", 'after')->orderBy('id', 'DESC')->first();
        $urutan = 1;
        if ($lastTrx && preg_match('/TRX-\d{8}-(\d+)/', $lastTrx['kode_transaksi'], $m)) {
            $urutan = (int)$m[1] + 1;
        }
        $kodeTrx = sprintf('TRX-%s-%03d', $tglHariIni, $urutan);

        $tglPinjam = date('Y-m-d');
        $tglJatuhTempo = date('Y-m-d', strtotime("+{$durasi} days"));

        $insertData = [
            'kode_transaksi'      => $kodeTrx,
            'anggota_id'          => $anggotaId,
            'buku_id'             => $bukuId,
            'tanggal_pinjam'      => $tglPinjam,
            'tanggal_jatuh_tempo' => $tglJatuhTempo,
            'status'              => 'dipinjam',
            'jumlah_perpanjangan' => 0,
            'admin_id'            => $adminId,
            'catatan'             => 'Peminjaman Mandiri Siswa',
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        // Kurangi stok buku secara atomik dengan proteksi race condition
        $db->table('buku')
           ->where('id', $bukuId)
           ->where('stok_tersedia >', 0)
           ->set('stok_tersedia', 'stok_tersedia - 1', false)
           ->update();

        if ($db->affectedRows() === 0) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Peminjaman ditolak: Stok buku ini baru saja habis terpinjam.'
            ]);
        }

        $this->peminjamanModel->insert($insertData);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses transaksi peminjaman di database.']);
        }

        \App\Models\AuditLogModel::record(
            'PINJAM_KIOSK',
            "Peminjaman mandiri buku '{$buku['judul']}' ({$kodeTrx}) oleh siswa '{$anggota['nama']}' berhasil dicatat via Kiosk.",
            $adminId
        );

        return $this->response->setJSON([
            'success'             => true,
            'message'             => 'Peminjaman buku berhasil diproses!',
            'receipt'             => [
                'kode_transaksi'      => $kodeTrx,
                'nama_anggota'        => $anggota['nama'],
                'nomor_anggota'       => $anggota['nomor_anggota'],
                'judul_buku'          => $buku['judul'],
                'kode_buku'           => $buku['kode_buku'],
                'tanggal_pinjam'      => date('d M Y', strtotime($tglPinjam)),
                'tanggal_jatuh_tempo' => date('d M Y', strtotime($tglJatuhTempo)),
                'durasi_hari'         => $durasi,
            ]
        ]);
    }

    // API Eksekusi Pengembalian Mandiri
    public function apiProsesKembali()
    {
        $kode = trim($this->request->getVar('kode') ?? '');
        if (empty($kode)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan scan atau masukkan kode buku / nomor transaksi.']);
        }

        // Cari transaksi peminjaman aktif berdasarkan kode_buku, ISBN, atau kode_transaksi
        $cleanIsbn = str_replace(['-', ' '], '', $kode);
        $pinjaman = $this->peminjamanModel
            ->select('peminjaman.*, buku.judul, buku.kode_buku, buku.id as real_buku_id, buku.jumlah_eksemplar, anggota.nama as nama_anggota, anggota.nomor_anggota')
            ->join('buku', 'buku.id = peminjaman.buku_id')
            ->join('anggota', 'anggota.id = peminjaman.anggota_id')
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
            ->groupStart()
                ->where('buku.kode_buku', $kode)
                ->orWhere('buku.isbn', $kode)
                ->orWhere("REPLACE(REPLACE(buku.isbn, '-', ''), ' ', '')", $cleanIsbn)
                ->orWhere('peminjaman.kode_transaksi', $kode)
            ->groupEnd()
            ->orderBy('peminjaman.id', 'DESC')
            ->first();

        if (!$pinjaman) {
            return $this->response->setJSON(['success' => false, 'message' => "Tidak ditemukan riwayat peminjaman aktif untuk kode '$kode'."]);
        }

        $config = $this->pengaturanModel->getSemuaMap();
        $tarifDenda = (float)($config['tarif_denda_per_hari'] ?? 500);

        $today = date('Y-m-d');
        $jatuhTempo = $pinjaman['tanggal_jatuh_tempo'];
        $selisihHari = 0;
        $denda = 0.00;
        $statusDenda = 'tidak_ada';

        if ($today > $jatuhTempo) {
            $selisihHari = (int)((strtotime($today) - strtotime($jatuhTempo)) / 86400);
            $denda = $selisihHari * $tarifDenda;
            $statusDenda = 'belum_lunas';
        }

        $admin = $this->adminModel->first();
        $adminId = $admin['id'] ?? 1;

        $db = \Config\Database::connect();
        $db->transStart();

        // Catat pengembalian
        $this->pengembalianModel->insert([
            'peminjaman_id'         => $pinjaman['id'],
            'tanggal_kembali'       => $today,
            'jumlah_hari_terlambat' => $selisihHari,
            'denda'                 => $denda,
            'status_denda'          => $statusDenda,
            'admin_id'              => $adminId,
            'catatan'               => 'Pengembalian Mandiri Siswa' . ($denda > 0 ? " (Denda Rp " . number_format($denda, 0, ',', '.') . " harap dibayarkan ke pustakawan)" : ""),
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);

        // Update status peminjaman
        $this->peminjamanModel->update($pinjaman['id'], [
            'status'     => 'dikembalikan',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Tambah kembali stok buku secara atomik tanpa melebihi jumlah eksemplar
        $db->table('buku')
           ->where('id', $pinjaman['real_buku_id'])
           ->where('stok_tersedia < jumlah_eksemplar', null, false)
           ->set('stok_tersedia', 'stok_tersedia + 1', false)
           ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses transaksi pengembalian di database.']);
        }

        \App\Models\AuditLogModel::record(
            'KEMBALI_KIOSK',
            "Pengembalian mandiri buku '{$pinjaman['judul']}' ({$pinjaman['kode_transaksi']}) oleh siswa '{$pinjaman['nama_anggota']}' berhasil diproses via Kiosk." . ($denda > 0 ? " Denda: Rp " . number_format($denda, 0, ',', '.') : ""),
            $adminId
        );

        return $this->response->setJSON([
            'success'        => true,
            'message'        => 'Pengembalian buku berhasil diproses!',
            'receipt'        => [
                'kode_transaksi'   => $pinjaman['kode_transaksi'],
                'nama_anggota'     => $pinjaman['nama_anggota'],
                'nomor_anggota'    => $pinjaman['nomor_anggota'],
                'judul_buku'       => $pinjaman['judul'],
                'kode_buku'        => $pinjaman['kode_buku'],
                'tanggal_kembali'  => date('d M Y', strtotime($today)),
                'terlambat_hari'   => $selisihHari,
                'denda'            => $denda,
                'status_denda'     => $statusDenda,
            ]
        ]);
    }
}
