<?php

namespace App\Controllers;

use App\Models\PeminjamanModel;
use App\Models\BukuModel;
use App\Models\AnggotaModel;
use App\Models\PengaturanModel;

class Notifikasi extends BaseController
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
        $config = $this->pengaturanModel->getSemuaMap();
        $tarifDenda = (float)($config['tarif_denda_per_hari'] ?? 500);
        $namaPerpus = $config['nama_perpustakaan'] ?? 'Perpustakaan Pelita Ilmu';

        $filter = $this->request->getGet('filter') ?: 'semua'; // 'semua', 'terlambat', 'segera'

        // Ambil peminjaman aktif
        $rawLoans = $this->peminjamanModel
            ->select('peminjaman.*, buku.judul, buku.kode_buku, anggota.nama as nama_anggota, anggota.nomor_anggota, anggota.kontak, anggota.kelas, anggota.tipe_anggota')
            ->join('buku', 'buku.id = peminjaman.buku_id')
            ->join('anggota', 'anggota.id = peminjaman.anggota_id')
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
            ->orderBy('peminjaman.tanggal_jatuh_tempo', 'ASC')
            ->findAll();

        $today = date('Y-m-d');
        $loans = [];

        $countTerlambat = 0;
        $countHariIni = 0;
        $countH1 = 0;
        $countH2H3 = 0;

        foreach ($rawLoans as $loan) {
            $jatuhTempo = $loan['tanggal_jatuh_tempo'];
            $diffDays = (int)round((strtotime($jatuhTempo) - strtotime($today)) / 86400);

            $loan['diff_days'] = $diffDays;

            // Format nomor kontak ke WhatsApp internasional (628...)
            $kontakClean = preg_replace('/[^0-9]/', '', $loan['kontak'] ?? '');
            $isWaValid = (strlen($kontakClean) >= 9);
            $waPhone = '';
            if ($isWaValid) {
                if (str_starts_with($kontakClean, '08')) {
                    $waPhone = '628' . substr($kontakClean, 2);
                } elseif (str_starts_with($kontakClean, '62')) {
                    $waPhone = $kontakClean;
                } else {
                    $waPhone = '62' . $kontakClean;
                }
            }
            $loan['wa_phone'] = $waPhone ?: '-';
            $loan['is_wa_valid'] = $isWaValid;

            // Logika Kategori Status & Pesan
            if ($diffDays < 0) {
                // Terlambat
                $countTerlambat++;
                $hariTelat = abs($diffDays);
                $dendaEst = $hariTelat * $tarifDenda;
                $loan['kategori_status'] = 'terlambat';
                $loan['status_badge'] = "Terlambat {$hariTelat} Hari";
                $loan['denda_est'] = $dendaEst;
                $loan['badge_class'] = 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';

                $pesan = "Halo Kak/Bapak/Ibu *{$loan['nama_anggota']}*,\n\nKami dari *{$namaPerpus}* menginfokan bahwa peminjaman buku:\n📚 *{$loan['judul']}* (Kode: {$loan['kode_buku']})\n\nTelah *terlambat {$hariTelat} hari* (jatuh tempo: " . date('d M Y', strtotime($jatuhTempo)) . ").\nDenda berjalan saat ini: *Rp " . number_format($dendaEst, 0, ',', '.') . "*.\n\nMohon bantuannya untuk segera mengembalikan buku ke perpustakaan sekolah. Terima kasih banyak atas kerjasamanya! 🙏✨";
            } elseif ($diffDays == 0) {
                // Hari Ini
                $countHariIni++;
                $loan['kategori_status'] = 'segera';
                $loan['status_badge'] = "Jatuh Tempo Hari Ini!";
                $loan['denda_est'] = 0;
                $loan['badge_class'] = 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800';

                $pesan = "Halo Kak/Bapak/Ibu *{$loan['nama_anggota']}*,\n\nPengingat dari *{$namaPerpus}*:\nBuku *{$loan['judul']}* (Kode: {$loan['kode_buku']}) jatuh tempo *HARI INI* (" . date('d M Y', strtotime($jatuhTempo)) . ").\n\nSilakan kembalikan ke perpustakaan sekolah sebelum konter tutup ya agar terhindar dari denda. Terima kasih! 📚😊";
            } elseif ($diffDays == 1) {
                // H-1 Besok
                $countH1++;
                $loan['kategori_status'] = 'segera';
                $loan['status_badge'] = "Besok Jatuh Tempo (H-1)";
                $loan['denda_est'] = 0;
                $loan['badge_class'] = 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800';

                $pesan = "Halo Kak/Bapak/Ibu *{$loan['nama_anggota']}*,\n\nPengingat ramah dari *{$namaPerpus}*:\nBuku *{$loan['judul']}* (Kode: {$loan['kode_buku']}) akan jatuh tempo *BESOK* (" . date('d M Y', strtotime($jatuhTempo)) . ").\n\nJangan lupa dibawa ke sekolah untuk dikembalikan atau diperpanjang jika belum selesai membaca. Terima kasih! 📖🙏";
            } else {
                // H-2 s/d H-3
                $countH2H3++;
                $loan['kategori_status'] = 'normal';
                $loan['status_badge'] = "H-{$diffDays} (" . date('d M', strtotime($jatuhTempo)) . ")";
                $loan['denda_est'] = 0;
                $loan['badge_class'] = 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800';

                $pesan = "Halo Kak/Bapak/Ibu *{$loan['nama_anggota']}*,\n\nInfo dari *{$namaPerpus}*:\nBuku yang sedang dipinjam: *{$loan['judul']}*.\nBatas akhir pengembalian: *" . date('d M Y', strtotime($jatuhTempo)) . "* ({$diffDays} hari lagi).\n\nSelamat membaca dan jaga kebersihan buku ya! 📚✨";
            }

            $loan['wa_message'] = $pesan;
            $loan['wa_link'] = $isWaValid ? "https://api.whatsapp.com/send?phone={$waPhone}&text=" . urlencode($pesan) : null;

            // Terapkan Filter
            if ($filter === 'terlambat' && $diffDays >= 0) {
                continue;
            } elseif ($filter === 'segera' && ($diffDays < 0 || $diffDays > 2)) {
                continue;
            }

            $loans[] = $loan;
        }

        $data = [
            'title'          => 'Pusat Notifikasi WhatsApp & Pengingat Sirkulasi',
            'active_menu'    => 'notifikasi',
            'config'         => $config,
            'loans'          => $loans,
            'filter'         => $filter,
            'countTerlambat' => $countTerlambat,
            'countHariIni'   => $countHariIni,
            'countH1'        => $countH1,
            'countH2H3'      => $countH2H3,
            'totalAktif'     => count($rawLoans),
        ];

        return view('notifikasi/index', $data);
    }
}
