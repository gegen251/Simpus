<?php

namespace App\Controllers;

use App\Models\PeminjamanModel;
use App\Models\BukuModel;
use App\Models\AnggotaModel;
use App\Models\PengaturanModel;

class Peminjaman extends BaseController
{
    protected $peminjamanModel;
    protected $bukuModel;
    protected $anggotaModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->peminjamanModel = new PeminjamanModel();
        $this->bukuModel       = new BukuModel();
        $this->anggotaModel    = new AnggotaModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        // Update any overdue status first
        $this->peminjamanModel->updateStatusTerlambat();

        $status    = $this->request->getGet('status') ?: 'aktif';
        $keyword   = $this->request->getGet('q');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $peminjaman = $this->peminjamanModel->getPeminjamanDetail(null, $status, $keyword, $startDate, $endDate);

        $activeFilterCount = 0;
        if (!empty($keyword)) $activeFilterCount++;
        if ($status !== 'aktif') $activeFilterCount++;
        if (!empty($startDate)) $activeFilterCount++;
        if (!empty($endDate)) $activeFilterCount++;

        // Data for new loan modal
        $durasiDefault = (int)($this->pengaturanModel->getKunci('durasi_pinjam_default', 7));
        $maxPinjam     = (int)($this->pengaturanModel->getKunci('max_pinjam_buku', 3));
        
        $availableBooks = $this->bukuModel->where('status', 'aktif')
                                          ->where('stok_tersedia >', 0)
                                          ->orderBy('judul', 'ASC')
                                          ->findAll();

        $activeMembers = $this->anggotaModel->where('status', 'aktif')
                                            ->orderBy('nama', 'ASC')
                                            ->findAll();

        $data = [
            'title'             => 'Transaksi Peminjaman Buku',
            'active_menu'       => 'peminjaman',
            'peminjaman'        => $peminjaman,
            'selectedStat'      => $status,
            'keyword'           => $keyword,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'activeFilterCount' => $activeFilterCount,
            'availableBooks'    => $availableBooks,
            'activeMembers'     => $activeMembers,
            'newKode'           => $this->peminjamanModel->generateKodeTransaksi(),
            'defaultTglPinjam'  => date('Y-m-d'),
            'defaultTglTempo'   => date('Y-m-d', strtotime("+$durasiDefault days")),
            'maxPinjam'         => $maxPinjam,
        ];

        return view('peminjaman/index', $data);
    }

    public function store()
    {
        $rules = [
            'anggota_id'          => 'required|numeric',
            'buku_id'             => 'required|numeric',
            'tanggal_pinjam'      => 'required|valid_date',
            'tanggal_jatuh_tempo' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Harap lengkapi seluruh formulir peminjaman.');
        }

        $anggotaId = (int)$this->request->getPost('anggota_id');
        $bukuId    = (int)$this->request->getPost('buku_id');
        $tglPinjam = $this->request->getPost('tanggal_pinjam');
        $tglTempo  = $this->request->getPost('tanggal_jatuh_tempo');

        // 1. Validasi Anggota
        $anggota = $this->anggotaModel->find($anggotaId);
        if (!$anggota || $anggota['status'] !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Anggota tidak ditemukan atau sedang dinonaktifkan.');
        }

        // FR-16: Validasi Batas Pinjam Aktif
        $maxPinjam = (int)($this->pengaturanModel->getKunci('max_pinjam_buku', 3));
        $activeLoans = $this->peminjamanModel->countPinjamanAktifAnggota($anggotaId);
        if ($activeLoans >= $maxPinjam) {
            return redirect()->back()->withInput()->with('error', "Peminjaman ditolak: Anggota '{$anggota['nama']}' telah mencapai batas maksimal ($maxPinjam buku) peminjaman aktif.");
        }

        // Validasi Tanggungan Denda Belum Lunas
        $pengembalianModel = new \App\Models\PengembalianModel();
        $unpaidFines = $pengembalianModel
            ->join('peminjaman', 'peminjaman.id = pengembalian.peminjaman_id')
            ->where('peminjaman.anggota_id', $anggotaId)
            ->where('pengembalian.status_denda', 'belum_lunas')
            ->countAllResults();

        if ($unpaidFines > 0) {
            return redirect()->back()->withInput()->with('error', "Peminjaman ditolak: Anggota '{$anggota['nama']}' masih memiliki tanggungan denda keterlambatan yang belum lunas. Harap selesaikan denda terlebih dahulu.");
        }

        // 2. FR-15: Validasi Stok Buku
        $buku = $this->bukuModel->find($bukuId);
        if (!$buku || $buku['status'] !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Buku tidak ditemukan atau berstatus nonaktif.');
        }

        if ($buku['stok_tersedia'] <= 0) {
            return redirect()->back()->withInput()->with('error', "Peminjaman ditolak: Stok buku '{$buku['judul']}' sedang habis (0 tersedia).");
        }

        // 3. Simpan Transaksi Peminjaman secara Atomik
        $kodeTransaksi = $this->peminjamanModel->generateKodeTransaksi();
        $adminId = session()->get('admin_id') ?: 1;

        $db = \Config\Database::connect();
        $db->transStart();

        // FR-14: Kurangi stok buku secara atomik hanya jika stok_tersedia > 0
        $db->table('buku')
           ->where('id', $bukuId)
           ->where('stok_tersedia >', 0)
           ->set('stok_tersedia', 'stok_tersedia - 1', false)
           ->update();

        // Validasi race condition: Jika 0 baris terupdate, berarti stok baru saja habis
        if ($db->affectedRows() === 0) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', "Peminjaman ditolak: Stok buku '{$buku['judul']}' baru saja habis terpinjam.");
        }

        $this->peminjamanModel->insert([
            'kode_transaksi'      => $kodeTransaksi,
            'anggota_id'          => $anggotaId,
            'buku_id'             => $bukuId,
            'tanggal_pinjam'      => $tglPinjam,
            'tanggal_jatuh_tempo' => $tglTempo,
            'status'              => 'dipinjam',
            'admin_id'            => $adminId,
            'catatan'             => $this->request->getPost('catatan'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan transaksi peminjaman.');
        }

        // Catat ke audit log keamanan & data
        \App\Models\AuditLogModel::record(
            'PINJAM_BUKU',
            "Peminjaman buku '{$buku['judul']}' ({$kodeTransaksi}) oleh anggota '{$anggota['nama']}' berhasil dicatat.",
            $adminId
        );

        return redirect()->to(site_url('/peminjaman'))->with('success', "Peminjaman buku '{$buku['judul']}' untuk '{$anggota['nama']}' berhasil dicatat.");
    }

    public function detail($id)
    {
        $detail = $this->peminjamanModel->getPeminjamanDetail($id);
        if (!$detail) {
            return redirect()->to(site_url('/peminjaman'))->with('error', 'Data transaksi tidak ditemukan.');
        }

        $data = [
            'title'       => 'Detail Peminjaman ' . $detail['kode_transaksi'],
            'active_menu' => 'peminjaman',
            'detail'      => $detail,
        ];

        return view('peminjaman/detail', $data);
    }

    public function perpanjang($id)
    {
        $peminjaman = $this->peminjamanModel->find($id);
        if (!$peminjaman) {
            return redirect()->to(site_url('/peminjaman'))->with('error', 'Data transaksi peminjaman tidak ditemukan.');
        }

        if ($peminjaman['status'] === 'dikembalikan') {
            return redirect()->to(site_url('/peminjaman'))->with('error', 'Transaksi ini sudah selesai (buku telah dikembalikan).');
        }

        $today = date('Y-m-d');
        if ($peminjaman['status'] === 'terlambat' || $peminjaman['tanggal_jatuh_tempo'] < $today) {
            return redirect()->to(site_url('/peminjaman'))->with('error', 'Peminjaman yang sudah melewati tanggal jatuh tempo tidak dapat diperpanjang. Harap lakukan pengembalian dan selesaikan denda terlebih dahulu.');
        }

        // Validasi Aturan Bisnis: Tidak bisa perpanjang jika anggota memiliki tanggungan denda belum lunas
        $pengembalianModel = new \App\Models\PengembalianModel();
        $unpaidFines = $pengembalianModel
            ->join('peminjaman', 'peminjaman.id = pengembalian.peminjaman_id')
            ->where('peminjaman.anggota_id', $peminjaman['anggota_id'])
            ->where('pengembalian.status_denda', 'belum_lunas')
            ->countAllResults();

        if ($unpaidFines > 0) {
            return redirect()->to(site_url('/peminjaman'))->with('error', 'Perpanjangan ditolak: Anggota masih memiliki tanggungan denda keterlambatan yang belum lunas. Harap selesaikan denda terlebih dahulu.');
        }

        // Validasi Aturan Bisnis: Batas maksimal perpanjangan
        $maxPerpanjang = (int)($this->pengaturanModel->getKunci('max_perpanjangan_buku', 2));
        $currentPerpanjang = (int)($peminjaman['jumlah_perpanjangan'] ?? 0);
        if ($currentPerpanjang >= $maxPerpanjang) {
            return redirect()->to(site_url('/peminjaman'))->with('error', "Peminjaman telah mencapai batas maksimal perpanjangan ({$maxPerpanjang} kali). Buku harus dikembalikan.");
        }

        $durasiDefault = (int)($this->pengaturanModel->getKunci('durasi_pinjam_default', 5));
        $oldTempo = $peminjaman['tanggal_jatuh_tempo'];
        $newTempo = date('Y-m-d', strtotime($oldTempo . " +{$durasiDefault} days"));

        $catatanTambahan = "Diperpanjang ke-" . ($currentPerpanjang + 1) . " s/d " . date('d/m/Y', strtotime($newTempo));
        $catatanBaru = !empty($peminjaman['catatan']) ? $peminjaman['catatan'] . " | " . $catatanTambahan : $catatanTambahan;

        // Pola Transaksi Konsisten
        $db = \Config\Database::connect();
        $db->transStart();

        $this->peminjamanModel->update($id, [
            'tanggal_jatuh_tempo'           => $newTempo,
            'jumlah_perpanjangan'          => $currentPerpanjang + 1,
            'tanggal_perpanjangan_terakhir' => date('Y-m-d H:i:s'),
            'catatan'                      => $catatanBaru
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(site_url('/peminjaman'))->with('error', 'Gagal memperpanjang transaksi peminjaman di database.');
        }

        $adminId = session()->get('admin_id') ?: 1;
        \App\Models\AuditLogModel::record(
            'PERPANJANG_PINJAM',
            "Perpanjangan peminjaman transaksi '{$peminjaman['kode_transaksi']}' (ke-" . ($currentPerpanjang + 1) . ") berhasil dicatat sampai tanggal " . date('d/m/Y', strtotime($newTempo)) . ".",
            $adminId
        );

        return redirect()->to(site_url('/peminjaman'))->with('success', "Peminjaman transaksi '{$peminjaman['kode_transaksi']}' berhasil diperpanjang (+{$durasiDefault} hari) sampai tanggal " . date('d F Y', strtotime($newTempo)) . ".");
    }
}
