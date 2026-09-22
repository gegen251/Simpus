<?php

namespace App\Controllers;

use App\Models\PengembalianModel;
use App\Models\PeminjamanModel;
use App\Models\BukuModel;
use App\Models\PengaturanModel;

class Pengembalian extends BaseController
{
    protected $pengembalianModel;
    protected $peminjamanModel;
    protected $bukuModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengembalianModel = new PengembalianModel();
        $this->peminjamanModel   = new PeminjamanModel();
        $this->bukuModel         = new BukuModel();
        $this->pengaturanModel   = new PengaturanModel();
    }

    public function index()
    {
        // Auto-update late status
        $this->peminjamanModel->updateStatusTerlambat();

        $keyword = $this->request->getGet('q');
        $status  = $this->request->getGet('status') ?: 'aktif';

        // Only active loans (dipinjam or terlambat) can be returned
        $pinjamanAktif = $this->peminjamanModel->getPeminjamanDetail(null, $status, $keyword);
        $tarifDenda    = (float)($this->pengaturanModel->getKunci('tarif_denda_per_hari', 1000));

        $activeFilterCount = 0;
        if (!empty($keyword)) $activeFilterCount++;
        if ($status !== 'aktif') $activeFilterCount++;

        $data = [
            'title'             => 'Proses Pengembalian Buku',
            'active_menu'       => 'pengembalian',
            'sub_menu'          => 'proses',
            'pinjamanAktif'     => $pinjamanAktif,
            'keyword'           => $keyword,
            'selectedStat'      => $status,
            'activeFilterCount' => $activeFilterCount,
            'tarifDenda'        => $tarifDenda,
            'today'             => date('Y-m-d'),
        ];

        return view('pengembalian/index', $data);
    }

    public function save()
    {
        $rules = [
            'peminjaman_id'  => 'required|numeric',
            'tanggal_kembali' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Data pengembalian tidak lengkap.');
        }

        $peminjamanId   = (int)$this->request->getPost('peminjaman_id');
        $tanggalKembali = $this->request->getPost('tanggal_kembali');
        $catatan        = $this->request->getPost('catatan');

        $peminjaman = $this->peminjamanModel->find($peminjamanId);
        if (!$peminjaman || $peminjaman['status'] === 'dikembalikan') {
            return redirect()->to(site_url('/pengembalian'))->with('error', 'Transaksi peminjaman tidak valid atau sudah dikembalikan sebelumnya.');
        }

        // Cegah tanggal kembali sebelum tanggal pinjam (backdated manipulation)
        if ($tanggalKembali < $peminjaman['tanggal_pinjam']) {
            return redirect()->back()->with('error', "Tanggal pengembalian ({$tanggalKembali}) tidak boleh lebih awal dari tanggal pinjam ({$peminjaman['tanggal_pinjam']}).");
        }

        // FR-20: Kalkulasi Keterlambatan & Denda Otomatis
        $tglTempo = strtotime($peminjaman['tanggal_jatuh_tempo']);
        $tglKembali = strtotime($tanggalKembali);
        $diffSeconds = $tglKembali - $tglTempo;

        $hariTerlambat = 0;
        $denda = 0.00;
        $statusDenda = 'tidak_ada';

        if ($diffSeconds > 0) {
            $hariTerlambat = (int)ceil($diffSeconds / 86400);
            $tarifDenda = (float)($this->pengaturanModel->getKunci('tarif_denda_per_hari', 1000));
            $denda = $hariTerlambat * $tarifDenda;
            $inputStatusDenda = $this->request->getPost('status_denda');
            $statusDenda = in_array($inputStatusDenda, ['lunas', 'belum_lunas']) ? $inputStatusDenda : 'lunas';
        }

        $adminId = session()->get('admin_id') ?: 1;

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Simpan tabel pengembalian
        $this->pengembalianModel->insert([
            'peminjaman_id'         => $peminjamanId,
            'tanggal_kembali'       => $tanggalKembali,
            'jumlah_hari_terlambat' => $hariTerlambat,
            'denda'                 => $denda,
            'status_denda'          => $statusDenda,
            'admin_id'              => $adminId,
            'catatan'               => $catatan,
        ]);

        // 2. Update status peminjaman jadi 'dikembalikan'
        $this->peminjamanModel->update($peminjamanId, [
            'status' => 'dikembalikan',
        ]);

        // 3. FR-19: Kembalikan stok buku (+1) secara atomik tanpa melebihi jumlah_eksemplar
        $db->table('buku')
           ->where('id', $peminjaman['buku_id'])
           ->where('stok_tersedia < jumlah_eksemplar', null, false)
           ->set('stok_tersedia', 'stok_tersedia + 1', false)
           ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(site_url('/pengembalian'))->with('error', 'Gagal memproses pengembalian buku di database.');
        }

        \App\Models\AuditLogModel::record(
            'KEMBALI_BUKU',
            "Pengembalian buku transaksi '{$peminjaman['kode_transaksi']}' berhasil diproses." . ($denda > 0 ? " Denda keterlambatan: Rp " . number_format($denda, 0, ',', '.') . " [Status: " . ucfirst($statusDenda) . "]." : ""),
            $adminId
        );

        $msg = "Pengembalian transaksi '{$peminjaman['kode_transaksi']}' berhasil diproses.";
        if ($denda > 0) {
            $msg .= " Dikenakan denda keterlambatan Rp " . number_format($denda, 0, ',', '.') . " ($hariTerlambat hari) [Status: " . ucfirst($statusDenda) . "].";
        }

        return redirect()->to(site_url('/pengembalian/riwayat'))->with('success', $msg);
    }

    public function lunasiDenda($id)
    {
        $pengembalian = $this->pengembalianModel->find($id);
        if (!$pengembalian) {
            return redirect()->to(site_url('/pengembalian/riwayat'))->with('error', 'Data pengembalian tidak ditemukan.');
        }

        if ($pengembalian['status_denda'] !== 'belum_lunas') {
            return redirect()->to(site_url('/pengembalian/riwayat'))->with('info', 'Status denda transaksi ini sudah lunas atau tidak ada denda.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $catatan = trim(($pengembalian['catatan'] ?? '') . ' | Denda dilunasi ke pustakawan pada ' . date('d/m/Y H:i'));
        $this->pengembalianModel->update($id, [
            'status_denda' => 'lunas',
            'catatan'      => $catatan,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(site_url('/pengembalian/riwayat'))->with('error', 'Gagal memperbarui status pelunasan denda di database.');
        }

        $adminId = session()->get('admin_id') ?: 1;
        \App\Models\AuditLogModel::record(
            'LUNASI_DENDA',
            "Pelunasan denda pengembalian ID {$id} sebesar Rp " . number_format($pengembalian['denda'], 0, ',', '.') . " berhasil dikonfirmasi.",
            $adminId
        );

        return redirect()->to(site_url('/pengembalian/riwayat'))->with('success', 'Pembayaran denda sebesar Rp ' . number_format($pengembalian['denda'], 0, ',', '.') . ' berhasil dikonfirmasi lunas.');
    }

    public function riwayat()
    {
        $keyword   = $this->request->getGet('q');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $riwayat = $this->pengembalianModel->getPengembalianDetail(null, $keyword, $startDate, $endDate);

        $activeFilterCount = 0;
        if (!empty($keyword)) $activeFilterCount++;
        if (!empty($startDate)) $activeFilterCount++;
        if (!empty($endDate)) $activeFilterCount++;

        $data = [
            'title'             => 'Riwayat Pengembalian & Denda',
            'active_menu'       => 'pengembalian',
            'sub_menu'          => 'riwayat',
            'riwayat'           => $riwayat,
            'keyword'           => $keyword,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'activeFilterCount' => $activeFilterCount,
        ];

        return view('pengembalian/riwayat', $data);
    }
}
