<?php

namespace App\Controllers;

use App\Models\PeminjamanModel;
use App\Models\PengembalianModel;
use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\AnggotaModel;
use App\Models\PengaturanModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Laporan extends BaseController
{
    protected $peminjamanModel;
    protected $pengembalianModel;
    protected $bukuModel;
    protected $kategoriModel;
    protected $anggotaModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->peminjamanModel   = new PeminjamanModel();
        $this->pengembalianModel = new PengembalianModel();
        $this->bukuModel         = new BukuModel();
        $this->kategoriModel     = new KategoriModel();
        $this->anggotaModel      = new AnggotaModel();
        $this->pengaturanModel   = new PengaturanModel();
    }

    public function index()
    {
        return redirect()->to(site_url('/laporan/semua'));
    }

    // Rekapitulasi Semua Laporan / Ringkasan Eksekutif Sirkulasi
    public function semua()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');

        $dataLaporan = $this->peminjamanModel->getSemuaLaporanSirkulasi($startDate, $endDate);

        // Hitung statistik komprehensif
        $totalBuku      = $this->bukuModel->countAllResults();
        $sumEksemplar   = $this->bukuModel->selectSum('jumlah_eksemplar')->first()['jumlah_eksemplar'] ?? 0;
        $totalAnggota   = $this->anggotaModel->countAllResults();

        $totalPinjam    = count($dataLaporan);
        $totalAktif     = 0;
        $totalTerlambat = 0;
        $totalKembali   = 0;
        $totalDenda     = 0;

        foreach ($dataLaporan as $row) {
            if ($row['status'] === 'dipinjam') {
                $totalAktif++;
            } elseif ($row['status'] === 'terlambat') {
                $totalTerlambat++;
            } elseif ($row['status'] === 'dikembalikan') {
                $totalKembali++;
            }
            if (!empty($row['denda'])) {
                $totalDenda += (float)$row['denda'];
            }
        }

        $data = [
            'title'          => 'Semua Laporan & Ringkasan Sirkulasi SD',
            'active_menu'    => 'laporan',
            'sub_menu'       => 'laporan_semua',
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'dataLaporan'    => $dataLaporan,
            'stats'          => [
                'totalBuku'      => $totalBuku,
                'totalEksemplar' => (int)$sumEksemplar,
                'totalAnggota'   => $totalAnggota,
                'totalPinjam'    => $totalPinjam,
                'totalAktif'     => $totalAktif,
                'totalTerlambat' => $totalTerlambat,
                'totalKembali'   => $totalKembali,
                'totalDenda'     => $totalDenda,
            ]
        ];

        return view('laporan/semua', $data);
    }

    // FR-22: Laporan Peminjaman
    public function peminjaman()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
        $status    = $this->request->getGet('status');

        $dataLaporan = $this->peminjamanModel->getPeminjamanDetail(null, $status, null, $startDate, $endDate);

        $data = [
            'title'       => 'Laporan Peminjaman Buku',
            'active_menu' => 'laporan',
            'sub_menu'    => 'laporan_peminjaman',
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'status'      => $status,
            'dataLaporan' => $dataLaporan,
        ];

        return view('laporan/peminjaman', $data);
    }

    // FR-23: Laporan Pengembalian
    public function pengembalian()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');

        $dataLaporan = $this->pengembalianModel->getPengembalianDetail(null, null, $startDate, $endDate);

        $totalDenda = 0;
        foreach ($dataLaporan as $row) {
            $totalDenda += $row['denda'];
        }

        $data = [
            'title'       => 'Laporan Pengembalian & Denda',
            'active_menu' => 'laporan',
            'sub_menu'    => 'laporan_pengembalian',
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'dataLaporan' => $dataLaporan,
            'totalDenda'  => $totalDenda,
        ];

        return view('laporan/pengembalian', $data);
    }

    // FR-24: Laporan Data Buku
    public function buku()
    {
        $kategoriId = $this->request->getGet('kategori_id');
        $status     = $this->request->getGet('status');

        $dataLaporan = $this->bukuModel->getBukuWithKategori(null, null, $kategoriId, $status);
        $kategori    = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

        $data = [
            'title'       => 'Laporan Koleksi Data Buku',
            'active_menu' => 'laporan',
            'sub_menu'    => 'laporan_buku',
            'kategori'    => $kategori,
            'selectedKat' => $kategoriId,
            'selectedStat'=> $status,
            'dataLaporan' => $dataLaporan,
        ];

        return view('laporan/buku', $data);
    }

    // Export PDF via Dompdf
    public function exportPdf($jenis)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $config = $this->pengaturanModel->getSemuaMap();
        $today = date('d F Y');
        $adminNama = session()->get('admin_nama') ?: 'Administrator';

        $html = '';
        $filename = 'laporan_' . $jenis . '_' . date('Ymd_His') . '.pdf';

        if ($jenis === 'semua') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $dataLaporan = $this->peminjamanModel->getSemuaLaporanSirkulasi($startDate, $endDate);

            $totalDenda = 0;
            foreach ($dataLaporan as $r) {
                if (!empty($r['denda'])) $totalDenda += (float)$r['denda'];
            }

            $html = view('laporan/pdf_semua', [
                'config'      => $config,
                'startDate'   => $startDate,
                'endDate'     => $endDate,
                'dataLaporan' => $dataLaporan,
                'totalDenda'  => $totalDenda,
                'today'       => $today,
                'adminNama'   => $adminNama,
            ]);
            $dompdf->setPaper('A4', 'landscape');

        } elseif ($jenis === 'peminjaman') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $status    = $this->request->getGet('status');
            $dataLaporan = $this->peminjamanModel->getPeminjamanDetail(null, $status, null, $startDate, $endDate);

            $html = view('laporan/pdf_peminjaman', [
                'config'      => $config,
                'startDate'   => $startDate,
                'endDate'     => $endDate,
                'status'      => $status,
                'dataLaporan' => $dataLaporan,
                'today'       => $today,
                'adminNama'   => $adminNama,
            ]);
            $dompdf->setPaper('A4', 'landscape');

        } elseif ($jenis === 'pengembalian') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $dataLaporan = $this->pengembalianModel->getPengembalianDetail(null, null, $startDate, $endDate);

            $totalDenda = 0;
            foreach ($dataLaporan as $r) {
                $totalDenda += $r['denda'];
            }

            $html = view('laporan/pdf_pengembalian', [
                'config'      => $config,
                'startDate'   => $startDate,
                'endDate'     => $endDate,
                'dataLaporan' => $dataLaporan,
                'totalDenda'  => $totalDenda,
                'today'       => $today,
                'adminNama'   => $adminNama,
            ]);
            $dompdf->setPaper('A4', 'landscape');

        } elseif ($jenis === 'buku') {
            $kategoriId = $this->request->getGet('kategori_id');
            $status     = $this->request->getGet('status');
            $dataLaporan = $this->bukuModel->getBukuWithKategori(null, null, $kategoriId, $status);

            $html = view('laporan/pdf_buku', [
                'config'      => $config,
                'dataLaporan' => $dataLaporan,
                'today'       => $today,
                'adminNama'   => $adminNama,
            ]);
            $dompdf->setPaper('A4', 'portrait');
        } else {
            return redirect()->to(site_url('/laporan/semua'));
        }

        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => false]);
        exit();
    }

    // Export Microsoft Word (.doc)
    public function exportWord($jenis)
    {
        $config = $this->pengaturanModel->getSemuaMap();
        $today = date('d F Y');
        $adminNama = session()->get('admin_nama') ?: 'Administrator';
        $filename = 'laporan_' . $jenis . '_' . date('Ymd_His') . '.doc';

        $data = [
            'config'    => $config,
            'today'     => $today,
            'adminNama' => $adminNama,
        ];

        if ($jenis === 'semua') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $data['startDate']   = $startDate;
            $data['endDate']     = $endDate;
            $data['dataLaporan'] = $this->peminjamanModel->getSemuaLaporanSirkulasi($startDate, $endDate);
            $totalDenda = 0;
            foreach ($data['dataLaporan'] as $r) {
                if (!empty($r['denda'])) $totalDenda += (float)$r['denda'];
            }
            $data['totalDenda']  = $totalDenda;
            $viewName = 'laporan/export_word_semua';

        } elseif ($jenis === 'peminjaman') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $status    = $this->request->getGet('status');
            $data['startDate']   = $startDate;
            $data['endDate']     = $endDate;
            $data['status']      = $status;
            $data['dataLaporan'] = $this->peminjamanModel->getPeminjamanDetail(null, $status, null, $startDate, $endDate);
            $viewName = 'laporan/export_word_peminjaman';

        } elseif ($jenis === 'pengembalian') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $data['startDate']   = $startDate;
            $data['endDate']     = $endDate;
            $data['dataLaporan'] = $this->pengembalianModel->getPengembalianDetail(null, null, $startDate, $endDate);
            $totalDenda = 0;
            foreach ($data['dataLaporan'] as $r) {
                $totalDenda += $r['denda'];
            }
            $data['totalDenda']  = $totalDenda;
            $viewName = 'laporan/export_word_pengembalian';

        } elseif ($jenis === 'buku') {
            $kategoriId = $this->request->getGet('kategori_id');
            $status     = $this->request->getGet('status');
            $data['dataLaporan'] = $this->bukuModel->getBukuWithKategori(null, null, $kategoriId, $status);
            $viewName = 'laporan/export_word_buku';

        } else {
            return redirect()->to(site_url('/laporan/semua'));
        }

        header("Content-Type: application/vnd.ms-word; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Cache-Control: max-age=0");
        header("Pragma: public");

        echo view($viewName, $data);
        exit();
    }

    // Export Microsoft Excel (.xls)
    public function exportExcel($jenis)
    {
        $config = $this->pengaturanModel->getSemuaMap();
        $today = date('d F Y');
        $adminNama = session()->get('admin_nama') ?: 'Administrator';
        $filename = 'laporan_' . $jenis . '_' . date('Ymd_His') . '.xls';

        $data = [
            'config'    => $config,
            'today'     => $today,
            'adminNama' => $adminNama,
        ];

        if ($jenis === 'semua') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $data['startDate']   = $startDate;
            $data['endDate']     = $endDate;
            $data['dataLaporan'] = $this->peminjamanModel->getSemuaLaporanSirkulasi($startDate, $endDate);
            $totalDenda = 0;
            foreach ($data['dataLaporan'] as $r) {
                if (!empty($r['denda'])) $totalDenda += (float)$r['denda'];
            }
            $data['totalDenda']  = $totalDenda;
            $viewName = 'laporan/export_excel_semua';

        } elseif ($jenis === 'peminjaman') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $status    = $this->request->getGet('status');
            $data['startDate']   = $startDate;
            $data['endDate']     = $endDate;
            $data['status']      = $status;
            $data['dataLaporan'] = $this->peminjamanModel->getPeminjamanDetail(null, $status, null, $startDate, $endDate);
            $viewName = 'laporan/export_excel_peminjaman';

        } elseif ($jenis === 'pengembalian') {
            $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
            $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
            $data['startDate']   = $startDate;
            $data['endDate']     = $endDate;
            $data['dataLaporan'] = $this->pengembalianModel->getPengembalianDetail(null, null, $startDate, $endDate);
            $totalDenda = 0;
            foreach ($data['dataLaporan'] as $r) {
                $totalDenda += $r['denda'];
            }
            $data['totalDenda']  = $totalDenda;
            $viewName = 'laporan/export_excel_pengembalian';

        } elseif ($jenis === 'buku') {
            $kategoriId = $this->request->getGet('kategori_id');
            $status     = $this->request->getGet('status');
            $data['dataLaporan'] = $this->bukuModel->getBukuWithKategori(null, null, $kategoriId, $status);
            $viewName = 'laporan/export_excel_buku';

        } else {
            return redirect()->to(site_url('/laporan/semua'));
        }

        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Cache-Control: max-age=0");
        header("Pragma: public");

        echo view($viewName, $data);
        exit();
    }
}
