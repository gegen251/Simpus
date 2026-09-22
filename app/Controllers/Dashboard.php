<?php

namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\AnggotaModel;
use App\Models\PeminjamanModel;
use App\Models\PengembalianModel;
use App\Models\KategoriModel;

class Dashboard extends BaseController
{
    protected $bukuModel;
    protected $anggotaModel;
    protected $peminjamanModel;
    protected $pengembalianModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->bukuModel         = new BukuModel();
        $this->anggotaModel      = new AnggotaModel();
        $this->peminjamanModel   = new PeminjamanModel();
        $this->pengembalianModel = new PengembalianModel();
        $this->kategoriModel     = new KategoriModel();
    }

    public function index()
    {
        // Auto update status pinjaman terlambat
        $this->peminjamanModel->updateStatusTerlambat();

        $db = \Config\Database::connect();

        // 1. Ringkasan Statistik
        $totalBuku      = $this->bukuModel->countAllResults();
        $totalEksemplar = $this->bukuModel->selectSum('jumlah_eksemplar')->first()['jumlah_eksemplar'] ?? 0;
        $totalStokTersedia = $this->bukuModel->selectSum('stok_tersedia')->first()['stok_tersedia'] ?? 0;
        $totalAnggota   = $this->anggotaModel->where('status', 'aktif')->countAllResults();
        $totalPinjamAktif = $this->peminjamanModel->whereIn('status', ['dipinjam', 'terlambat'])->countAllResults();
        $totalTerlambat = $this->peminjamanModel->where('status', 'terlambat')->countAllResults();
        $totalDenda     = $this->pengembalianModel->selectSum('denda')->first()['denda'] ?? 0;

        // 2. Data Grafik: Tren Peminjaman 6 Bulan Terakhir
        $months = [];
        $loanCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd   = date('Y-m-t', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime("-$i months"));

            $count = $this->peminjamanModel->where('tanggal_pinjam >=', $monthStart)
                                           ->where('tanggal_pinjam <=', $monthEnd)
                                           ->countAllResults();
            $months[]     = $monthLabel;
            $loanCounts[] = $count;
        }

        // 3. Buku Terpopuler (Paling Sering Dipinjam)
        $popularBooks = $db->table('peminjaman')
                           ->select('buku.judul, COUNT(peminjaman.id) as total_pinjam')
                           ->join('buku', 'buku.id = peminjaman.buku_id')
                           ->groupBy('peminjaman.buku_id')
                           ->orderBy('total_pinjam', 'DESC')
                           ->limit(5)
                           ->get()
                           ->getResultArray();

        $popularLabels = [];
        $popularData   = [];
        foreach ($popularBooks as $pb) {
            $popularLabels[] = strlen($pb['judul']) > 22 ? substr($pb['judul'], 0, 22) . '...' : $pb['judul'];
            $popularData[]   = (int)$pb['total_pinjam'];
        }

        // 4. Pinjaman Mendesak / Terlambat (Top 5)
        $overdueLoans = $this->peminjamanModel->getPeminjamanDetail(null, 'terlambat');
        if (empty($overdueLoans)) {
            // If no overdue, show closest due
            $overdueLoans = $this->peminjamanModel->getPeminjamanDetail(null, 'aktif');
        }
        $overdueLoans = array_slice($overdueLoans, 0, 5);

        // 5. Transaksi Terbaru (5 peminjaman terakhir)
        $recentTransactions = $this->peminjamanModel->getPeminjamanDetail();
        $recentTransactions = array_slice($recentTransactions, 0, 5);

        $data = [
            'title'              => 'Dashboard Perpustakaan',
            'active_menu'        => 'dashboard',
            'totalBuku'          => $totalBuku,
            'totalEksemplar'     => $totalEksemplar,
            'totalStokTersedia'  => $totalStokTersedia,
            'totalAnggota'       => $totalAnggota,
            'totalPinjamAktif'   => $totalPinjamAktif,
            'totalTerlambat'     => $totalTerlambat,
            'totalDenda'         => $totalDenda,
            'chartMonths'        => json_encode($months),
            'chartLoanCounts'    => json_encode($loanCounts),
            'popularLabels'      => json_encode($popularLabels),
            'popularData'        => json_encode($popularData),
            'overdueLoans'       => $overdueLoans,
            'recentTransactions' => $recentTransactions,
        ];

        return view('dashboard/index', $data);
    }
}
