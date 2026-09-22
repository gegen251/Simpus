<?php

namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\PeminjamanModel;
use App\Models\PengaturanModel;

class Buku extends BaseController
{
    protected $bukuModel;
    protected $kategoriModel;
    protected $peminjamanModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->bukuModel       = new BukuModel();
        $this->kategoriModel   = new KategoriModel();
        $this->peminjamanModel = new PeminjamanModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $keyword      = $this->request->getGet('q');
        $kategoriId   = $this->request->getGet('kategori');
        $status       = $this->request->getGet('status');
        $lokasiRak    = $this->request->getGet('lokasi_rak');
        $penulis      = $this->request->getGet('penulis');
        $penerbit     = $this->request->getGet('penerbit');
        $ketersediaan = $this->request->getGet('ketersediaan');
        $order        = $this->request->getGet('order') ?: 'ASC';

        $bukuList = $this->bukuModel->getBukuWithKategori(
            null, $keyword, $kategoriId, $status, $lokasiRak, $penulis, $penerbit, $ketersediaan, $order
        );
        $kategori = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

        // Hitung jumlah filter aktif untuk indikator badge
        $activeFiltersCount = 0;
        if (!empty($keyword)) $activeFiltersCount++;
        if (!empty($kategoriId)) $activeFiltersCount++;
        if (!empty($status)) $activeFiltersCount++;
        if (!empty($lokasiRak)) $activeFiltersCount++;
        if (!empty($penulis)) $activeFiltersCount++;
        if (!empty($penerbit)) $activeFiltersCount++;
        if (!empty($ketersediaan)) $activeFiltersCount++;

        // Daftar lokasi rak yang ada untuk mempermudah pemilihan
        $rakList = $this->bukuModel->select('lokasi_rak')->distinct()->where('lokasi_rak IS NOT NULL')->where("lokasi_rak != ''")->orderBy('lokasi_rak', 'ASC')->findAll();

        $data = [
            'title'              => 'Koleksi Buku SD',
            'active_menu'        => 'buku',
            'buku'               => $bukuList,
            'kategori'           => $kategori,
            'rakList'            => $rakList,
            'selectedKat'        => $kategoriId,
            'selectedStat'       => $status,
            'selectedRak'        => $lokasiRak,
            'penulis'            => $penulis,
            'penerbit'           => $penerbit,
            'ketersediaan'       => $ketersediaan,
            'order'              => $order,
            'keyword'            => $keyword,
            'activeFiltersCount' => $activeFiltersCount,
            'newKode'            => $this->bukuModel->generateKodeBuku(),
        ];

        return view('buku/index', $data);
    }

    public function store()
    {
        $rules = [
            'judul'            => 'required|min_length[3]|max_length[255]',
            'penulis'          => 'required|min_length[2]|max_length[150]',
            'penerbit'         => 'required|max_length[150]',
            'kategori_id'      => 'required|numeric',
            'tahun_terbit'     => 'permit_empty|exact_length[4]|numeric',
            'jumlah_eksemplar' => 'required|numeric|greater_than[0]',
            'cover_file'       => 'permit_empty|is_image[cover_file]|mime_in[cover_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[cover_file,2048]',
        ];

        if (!$this->validate($rules)) {
            $err = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $err));
        }

        $kodeBuku = $this->request->getPost('kode_buku');
        if (empty($kodeBuku)) {
            $kodeBuku = $this->bukuModel->generateKodeBuku();
        }

        // Check unique code
        if ($this->bukuModel->where('kode_buku', $kodeBuku)->first()) {
            $kodeBuku = $this->bukuModel->generateKodeBuku();
        }

        $jumlah = (int)$this->request->getPost('jumlah_eksemplar');

        $coverPath = null;
        $coverFile = $this->request->getFile('cover_file');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $newName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/covers', $newName);
            $coverPath = 'uploads/covers/' . $newName;
        }

        $this->bukuModel->save([
            'kode_buku'        => $kodeBuku,
            'judul'            => $this->request->getPost('judul'),
            'penulis'          => $this->request->getPost('penulis'),
            'penerbit'         => $this->request->getPost('penerbit'),
            'isbn'             => $this->request->getPost('isbn'),
            'kategori_id'      => $this->request->getPost('kategori_id'),
            'tahun_terbit'     => $this->request->getPost('tahun_terbit') ?: null,
            'jumlah_eksemplar' => $jumlah,
            'stok_tersedia'    => $jumlah,
            'lokasi_rak'       => $this->request->getPost('lokasi_rak'),
            'status'           => $this->request->getPost('status') ?: 'aktif',
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'cover'            => $coverPath,
        ]);

        return redirect()->to(site_url('/buku'))->with('success', 'Data buku berhasil ditambahkan.');
    }

    public function update($id)
    {
        $buku = $this->bukuModel->find($id);
        if (!$buku) {
            return redirect()->to(site_url('/buku'))->with('error', 'Buku tidak ditemukan.');
        }

        $rules = [
            'judul'            => 'required|min_length[3]|max_length[255]',
            'penulis'          => 'required|min_length[2]|max_length[150]',
            'penerbit'         => 'required|max_length[150]',
            'kategori_id'      => 'required|numeric',
            'tahun_terbit'     => 'permit_empty|exact_length[4]|numeric',
            'jumlah_eksemplar' => 'required|numeric|greater_than[0]',
            'cover_file'       => 'permit_empty|is_image[cover_file]|mime_in[cover_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[cover_file,2048]',
        ];

        if (!$this->validate($rules)) {
            $err = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $err));
        }

        $newJumlah = (int)$this->request->getPost('jumlah_eksemplar');
        $currentlyBorrowed = $this->peminjamanModel->where('buku_id', $id)
                                                   ->whereIn('status', ['dipinjam', 'terlambat'])
                                                   ->countAllResults();

        if ($newJumlah < $currentlyBorrowed) {
            return redirect()->back()->withInput()->with('error', "Jumlah eksemplar tidak boleh kurang dari $currentlyBorrowed (jumlah buku yang saat ini sedang dipinjam).");
        }

        $newStok = $newJumlah - $currentlyBorrowed;

        $coverPath = $buku['cover'];
        $coverFile = $this->request->getFile('cover_file');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $newName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/covers', $newName);
            $coverPath = 'uploads/covers/' . $newName;
        }

        $this->bukuModel->update($id, [
            'judul'            => $this->request->getPost('judul'),
            'penulis'          => $this->request->getPost('penulis'),
            'penerbit'         => $this->request->getPost('penerbit'),
            'isbn'             => $this->request->getPost('isbn'),
            'kategori_id'      => $this->request->getPost('kategori_id'),
            'tahun_terbit'     => $this->request->getPost('tahun_terbit') ?: null,
            'jumlah_eksemplar' => $newJumlah,
            'stok_tersedia'    => $newStok,
            'lokasi_rak'       => $this->request->getPost('lokasi_rak'),
            'status'           => $this->request->getPost('status') ?: 'aktif',
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'cover'            => $coverPath,
        ]);

        return redirect()->to(site_url('/buku'))->with('success', 'Data buku berhasil diperbarui.');
    }

    public function delete($id)
    {
        $buku = $this->bukuModel->find($id);
        if (!$buku) {
            return redirect()->to(site_url('/buku'))->with('error', 'Buku tidak ditemukan.');
        }

        // FR-07 & Business Rule: "Buku yang sedang dipinjam tidak dapat dihapus"
        $activeLoans = $this->peminjamanModel->where('buku_id', $id)
                                             ->whereIn('status', ['dipinjam', 'terlambat'])
                                             ->countAllResults();

        if ($activeLoans > 0) {
            return redirect()->to(site_url('/buku'))->with('error', "Buku '{$buku['judul']}' tidak dapat dihapus karena masih ada $activeLoans eksemplar yang sedang dipinjam.");
        }

        // Cek riwayat historis sirkulasi untuk mencegah Foreign Key Constraint Crash
        $totalHistory = $this->peminjamanModel->where('buku_id', $id)->countAllResults();
        if ($totalHistory > 0) {
            return redirect()->to(site_url('/buku'))->with('warning', "Buku '{$buku['judul']}' memiliki riwayat transaksi peminjaman ({$totalHistory} transaksi). Untuk menjaga integritas arsip laporan, ubah status buku menjadi 'nonaktif' alih-alih menghapusnya.");
        }

        try {
            $this->bukuModel->delete($id);

            // Audit Log
            \App\Models\AuditLogModel::record(
                'HAPUS_BUKU',
                "Menghapus data buku: '{$buku['judul']}' (Kode: {$buku['kode_buku']})."
            );

            return redirect()->to(site_url('/buku'))->with('success', 'Data buku berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to(site_url('/buku'))->with('error', 'Gagal menghapus buku karena data masih terkait dengan modul sirkulasi lain.');
        }
    }

    // =========================================================================
    // PRIORITAS 4: CETAK LABEL PUNGGUNG & BARCODE BUKU
    // =========================================================================
    public function cetakLabel($id = null)
    {
        $config = $this->pengaturanModel->getSemuaMap();

        // Parameter Opsi Cetak
        $qtyMode   = $this->request->getVar('qty_mode') ?: 'single';   // 'single' atau 'eksemplar'
        $labelType = $this->request->getVar('label_type') ?: 'all';    // 'all', 'spine', atau 'barcode'
        $katId     = $this->request->getVar('kategori_id');

        // Pilihan ID Buku
        $selectedIds = $this->request->getVar('buku_ids');
        if (empty($selectedIds) && $this->request->getGet('ids')) {
            $selectedIds = explode(',', $this->request->getGet('ids'));
        }

        if ($id !== null) {
            $singleBuku = $this->bukuModel->getBukuWithKategori($id);
            $rawBuku = $singleBuku ? [$singleBuku] : [];
        } elseif (!empty($selectedIds) && is_array($selectedIds)) {
            // Cetak hanya buku yang dipilih
            $rawBuku = [];
            foreach ($selectedIds as $bid) {
                $b = $this->bukuModel->getBukuWithKategori((int)$bid);
                if ($b) {
                    $rawBuku[] = $b;
                }
            }
        } elseif (!empty($katId)) {
            // Cetak berdasarkan kategori
            $rawBuku = $this->bukuModel->getBukuWithKategori(null, null, (int)$katId, 'aktif');
        } else {
            // Cetak seluruh buku aktif
            $rawBuku = $this->bukuModel->getBukuWithKategori(null, null, null, 'aktif');
        }

        if (empty($rawBuku)) {
            return redirect()->to(site_url('/buku'))->with('error', 'Tidak ada buku yang dipilih atau ditemukan untuk dicetak label.');
        }

        // Expand jika opsi cetak berdasarkan jumlah eksemplar fisik
        $bukuList = [];
        foreach ($rawBuku as $item) {
            $totalCopy = ($qtyMode === 'eksemplar' && !empty($item['jumlah_eksemplar'])) ? (int)$item['jumlah_eksemplar'] : 1;
            for ($c = 1; $c <= $totalCopy; $c++) {
                $copyItem = $item;
                $copyItem['salinan_ke'] = $c;
                $copyItem['total_salinan'] = $totalCopy;
                $bukuList[] = $copyItem;
            }
        }

        $data = [
            'config'      => $config,
            'bukuList'    => $bukuList,
            'isSingle'    => ($id !== null),
            'qtyMode'     => $qtyMode,
            'labelType'   => $labelType,
            'totalJudul'  => count($rawBuku),
            'totalLabels' => count($bukuList),
        ];

        return view('buku/cetak_label', $data);
    }

    // =========================================================================
    // PRIORITAS 5: DOWNLOAD TEMPLATE & IMPORT DATA EXCEL/CSV BUKU
    // =========================================================================
    public function downloadTemplate()
    {
        $filename = 'template_import_buku.csv';
        $header = ['Judul Buku', 'Penulis', 'Penerbit', 'Tahun Terbit', 'ISBN', 'Nama Kategori', 'Jumlah Eksemplar', 'Lokasi Rak', 'Deskripsi Ringkas'];
        $samples = [
            ['Si Kancil yang Cerdik', 'Kak Dicky', 'Erlangga for Kids', '2023', '978-602-241-100-1', 'Cerita Anak & Dongeng Nusantara', '5', 'Rak A1 - Fabel', 'Kumpulan fabel mendidik tentang kecerdikan kancil'],
            ['Ensiklopedia Tubuh Manusia Cilik', 'Dr. Sarah Indah', 'Gramedia Pustaka', '2022', '978-602-03-8822-0', 'Ensiklopedia Cilik & Sains Anak', '3', 'Rak B2 - Sains', 'Mengenal panca indera dan organ tubuh untuk siswa SD']
        ];

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // Write BOM for UTF-8 Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, $header);
        foreach ($samples as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    }

    public function importCsv()
    {
        $file = $this->request->getFile('file_csv');
        if (!$file || !$file->isValid()) {
            return redirect()->to(site_url('/buku'))->with('error', 'File unggahan tidak valid atau belum dipilih.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return redirect()->to(site_url('/buku'))->with('error', 'Hanya file format CSV (.csv) yang didukung.');
        }

        $filepath = $file->getTempName();
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return redirect()->to(site_url('/buku'))->with('error', 'Gagal membaca file CSV.');
        }

        // Deteksi delimiter (koma atau titik koma)
        $firstLine = fgets($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 1000, $delimiter);
        $importedCount = 0;
        $skippedCount  = 0;

        // Cache kategori untuk mempercepat lookup
        $allKategori = $this->kategoriModel->findAll();
        $katMap = [];
        foreach ($allKategori as $k) {
            $katMap[strtolower(trim($k['nama_kategori']))] = $k['id'];
        }

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (empty($row) || count($row) < 3) continue;

            $judul       = trim($row[0] ?? '');
            $penulis     = trim($row[1] ?? '');
            $penerbit    = trim($row[2] ?? '');
            $tahunTerbit = trim($row[3] ?? '');
            $isbn        = trim($row[4] ?? '');
            $namaKat     = trim($row[5] ?? 'Umum');
            $jumlahEksem = (int)($row[6] ?? 1);
            $lokasiRak   = trim($row[7] ?? '');
            $deskripsi   = trim($row[8] ?? '');

            if (empty($judul) || empty($penulis) || empty($penerbit)) {
                $skippedCount++;
                continue;
            }

            if ($jumlahEksem <= 0) $jumlahEksem = 1;

            // Cari atau buat kategori otomatis
            $katKey = strtolower($namaKat);
            if (isset($katMap[$katKey])) {
                $kategoriId = $katMap[$katKey];
            } else {
                $this->kategoriModel->insert([
                    'nama_kategori' => $namaKat,
                    'keterangan'    => 'Dibuat otomatis saat import CSV'
                ]);
                $kategoriId = $this->kategoriModel->getInsertID();
                $katMap[$katKey] = $kategoriId;
            }

            $kodeBuku = $this->bukuModel->generateKodeBuku();

            $this->bukuModel->insert([
                'kode_buku'        => $kodeBuku,
                'judul'            => $judul,
                'penulis'          => $penulis,
                'penerbit'         => $penerbit,
                'tahun_terbit'     => !empty($tahunTerbit) ? $tahunTerbit : null,
                'isbn'             => !empty($isbn) ? $isbn : null,
                'kategori_id'      => $kategoriId,
                'jumlah_eksemplar' => $jumlahEksem,
                'stok_tersedia'    => $jumlahEksem,
                'lokasi_rak'       => $lokasiRak ?: null,
                'status'           => 'aktif',
                'deskripsi'        => $deskripsi ?: null,
            ]);

            $importedCount++;
        }

        fclose($handle);

        $msg = "Import berhasil: {$importedCount} data buku baru telah ditambahkan ke koleksi perpustakaan.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris dilewati karena data tidak lengkap).";
        }

        return redirect()->to(site_url('/buku'))->with('success', $msg);
    }
}
