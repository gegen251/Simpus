<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\PeminjamanModel;
use App\Models\PengaturanModel;

class Anggota extends BaseController
{
    protected $anggotaModel;
    protected $peminjamanModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->anggotaModel    = new AnggotaModel();
        $this->peminjamanModel = new PeminjamanModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $keyword      = $this->request->getGet('q');
        $status       = $this->request->getGet('status');
        $tipeAnggota  = $this->request->getGet('tipe');
        $kelas        = $this->request->getGet('kelas');
        $jenisKelamin = $this->request->getGet('jenis_kelamin');
        $order        = $this->request->getGet('order') ?? 'DESC';

        $builder = $this->anggotaModel->select('anggota.*, 
            (SELECT COUNT(peminjaman.id) FROM peminjaman WHERE peminjaman.anggota_id = anggota.id AND peminjaman.status IN ("dipinjam", "terlambat")) AS pinjaman_aktif');

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('nama', $keyword)
                    ->orLike('nomor_anggota', $keyword)
                    ->orLike('no_identitas', $keyword)
                    ->orLike('kontak', $keyword)
                    ->orLike('kelas', $keyword)
                    ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        if (!empty($tipeAnggota) && in_array($tipeAnggota, ['siswa', 'guru', 'karyawan'])) {
            $builder->where('tipe_anggota', $tipeAnggota);
        }

        if (!empty($kelas)) {
            $builder->where('kelas', $kelas);
        }

        if (!empty($jenisKelamin) && in_array($jenisKelamin, ['L', 'P'])) {
            $builder->where('jenis_kelamin', $jenisKelamin);
        }

        if ($order === 'ASC') {
            $builder->orderBy('nama', 'ASC');
        } elseif ($order === 'nomor') {
            $builder->orderBy('nomor_anggota', 'ASC');
        } else {
            $builder->orderBy('id', 'DESC');
        }

        $anggotaList = $builder->findAll();

        $activeFilterCount = 0;
        if (!empty($keyword)) $activeFilterCount++;
        if (!empty($status)) $activeFilterCount++;
        if (!empty($tipeAnggota)) $activeFilterCount++;
        if (!empty($kelas)) $activeFilterCount++;
        if (!empty($jenisKelamin)) $activeFilterCount++;
        if ($order !== 'DESC') $activeFilterCount++;

        // Daftar kelas yang ada untuk filter
        $kelasList = $this->anggotaModel->select('kelas')
                                        ->distinct()
                                        ->where('kelas IS NOT NULL')
                                        ->where("kelas != ''")
                                        ->orderBy('kelas', 'ASC')
                                        ->findAll();

        $data = [
            'title'             => 'Manajemen Data Anggota',
            'active_menu'       => 'anggota',
            'anggota'           => $anggotaList,
            'kelasList'         => $kelasList,
            'keyword'           => $keyword,
            'selectedStat'      => $status,
            'selectedTipe'      => $tipeAnggota,
            'selectedKelas'     => $kelas,
            'selectedJk'        => $jenisKelamin,
            'order'             => $order,
            'activeFilterCount' => $activeFilterCount,
            'newNomor'          => $this->anggotaModel->generateNomorAnggota(),
        ];

        return view('anggota/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama'         => 'required|min_length[3]|max_length[150]',
            'no_identitas' => 'required|min_length[4]|max_length[50]|is_unique[anggota.no_identitas]',
            'kontak'       => 'required|min_length[6]|max_length[30]',
            'tipe_anggota' => 'permit_empty|in_list[siswa,guru,karyawan]',
            'kelas'        => 'permit_empty|max_length[50]',
            'foto_file'    => 'permit_empty|is_image[foto_file]|mime_in[foto_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto_file,2048]',
        ];

        if (!$this->validate($rules)) {
            $err = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $err));
        }

        $nomorAnggota = $this->request->getPost('nomor_anggota');
        if (empty($nomorAnggota) || $this->anggotaModel->where('nomor_anggota', $nomorAnggota)->first()) {
            $nomorAnggota = $this->anggotaModel->generateNomorAnggota();
        }

        $fotoPath = null;
        $fotoFile = $this->request->getFile('foto_file');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $newName = $fotoFile->getRandomName();
            $fotoFile->move(FCPATH . 'uploads/anggota', $newName);
            $fotoPath = 'uploads/anggota/' . $newName;
        }

        $this->anggotaModel->save([
            'nomor_anggota' => $nomorAnggota,
            'nama'          => $this->request->getPost('nama'),
            'tipe_anggota'  => $this->request->getPost('tipe_anggota') ?: 'siswa',
            'kelas'         => $this->request->getPost('kelas'),
            'foto'          => $fotoPath,
            'no_identitas'  => $this->request->getPost('no_identitas'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin') ?: 'L',
            'kontak'        => $this->request->getPost('kontak'),
            'email'         => $this->request->getPost('email'),
            'alamat'        => $this->request->getPost('alamat'),
            'status'        => $this->request->getPost('status') ?: 'aktif',
        ]);

        return redirect()->to(site_url('/anggota'))->with('success', 'Data anggota baru berhasil ditambahkan.');
    }

    public function update($id)
    {
        $anggota = $this->anggotaModel->find($id);
        if (!$anggota) {
            return redirect()->to(site_url('/anggota'))->with('error', 'Data anggota tidak ditemukan.');
        }

        $noIdentitas = $this->request->getPost('no_identitas');
        $uniqueRule = ($noIdentitas !== $anggota['no_identitas']) ? '|is_unique[anggota.no_identitas]' : '';

        $rules = [
            'nama'         => 'required|min_length[3]|max_length[150]',
            'no_identitas' => 'required|min_length[4]|max_length[50]' . $uniqueRule,
            'kontak'       => 'required|min_length[6]|max_length[30]',
            'tipe_anggota' => 'permit_empty|in_list[siswa,guru,karyawan]',
            'kelas'        => 'permit_empty|max_length[50]',
            'foto_file'    => 'permit_empty|is_image[foto_file]|mime_in[foto_file,image/jpg,image/jpeg,image/png,image/webp]|max_size[foto_file,2048]',
        ];

        if (!$this->validate($rules)) {
            $err = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('error', 'Validasi gagal: ' . implode(', ', $err));
        }

        $fotoPath = $anggota['foto'];
        $fotoFile = $this->request->getFile('foto_file');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            if (!empty($anggota['foto']) && file_exists(FCPATH . $anggota['foto'])) {
                @unlink(FCPATH . $anggota['foto']);
            }
            $newName = $fotoFile->getRandomName();
            $fotoFile->move(FCPATH . 'uploads/anggota', $newName);
            $fotoPath = 'uploads/anggota/' . $newName;
        } elseif ($this->request->getPost('hapus_foto') === '1') {
            if (!empty($anggota['foto']) && file_exists(FCPATH . $anggota['foto'])) {
                @unlink(FCPATH . $anggota['foto']);
            }
            $fotoPath = null;
        }

        $this->anggotaModel->update($id, [
            'nama'          => $this->request->getPost('nama'),
            'tipe_anggota'  => $this->request->getPost('tipe_anggota') ?: 'siswa',
            'kelas'         => $this->request->getPost('kelas'),
            'foto'          => $fotoPath,
            'no_identitas'  => $noIdentitas,
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin') ?: 'L',
            'kontak'        => $this->request->getPost('kontak'),
            'email'         => $this->request->getPost('email'),
            'alamat'        => $this->request->getPost('alamat'),
            'status'        => $this->request->getPost('status') ?: 'aktif',
        ]);

        return redirect()->to(site_url('/anggota'))->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function delete($id)
    {
        $anggota = $this->anggotaModel->find($id);
        if (!$anggota) {
            return redirect()->to(site_url('/anggota'))->with('error', 'Data anggota tidak ditemukan.');
        }

        // FR-12: Anggota dengan pinjaman aktif tidak dapat dihapus
        $activeLoans = $this->peminjamanModel->where('anggota_id', $id)
                                             ->whereIn('status', ['dipinjam', 'terlambat'])
                                             ->countAllResults();

        if ($activeLoans > 0) {
            return redirect()->to(site_url('/anggota'))->with('error', "Anggota '{$anggota['nama']}' tidak dapat dihapus karena masih memiliki $activeLoans buku yang sedang dipinjam.");
        }

        // Cek riwayat historis sirkulasi untuk mencegah Foreign Key Constraint Crash
        $totalHistory = $this->peminjamanModel->where('anggota_id', $id)->countAllResults();
        if ($totalHistory > 0) {
            return redirect()->to(site_url('/anggota'))->with('warning', "Anggota '{$anggota['nama']}' memiliki riwayat arsip sirkulasi ({$totalHistory} transaksi). Demi validitas arsip laporan, ubah status anggota menjadi 'nonaktif' alih-alih menghapusnya.");
        }

        try {
            if (!empty($anggota['foto']) && file_exists(FCPATH . $anggota['foto'])) {
                @unlink(FCPATH . $anggota['foto']);
            }
            $this->anggotaModel->delete($id);

            // Audit Log
            \App\Models\AuditLogModel::record(
                'HAPUS_ANGGOTA',
                "Menghapus data anggota: '{$anggota['nama']}' (No: {$anggota['nomor_anggota']}, Identitas: {$anggota['no_identitas']})."
            );

            return redirect()->to(site_url('/anggota'))->with('success', 'Data anggota berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to(site_url('/anggota'))->with('error', 'Gagal menghapus anggota karena data masih terkait dengan modul sirkulasi lain.');
        }
    }

    // =========================================================================
    // PRIORITAS 4: CETAK KARTU ANGGOTA DENGAN BARCODE
    // =========================================================================
    public function cetakKartu($id = null)
    {
        $config = $this->pengaturanModel->getSemuaMap();

        if ($id !== null) {
            $anggota = $this->anggotaModel->where('id', $id)->findAll();
        } else {
            // Cetak seluruh anggota aktif
            $anggota = $this->anggotaModel->where('status', 'aktif')->orderBy('nama', 'ASC')->findAll();
        }

        if (empty($anggota)) {
            return redirect()->to(site_url('/anggota'))->with('error', 'Tidak ada data anggota untuk dicetak.');
        }

        $data = [
            'config'  => $config,
            'anggota' => $anggota,
            'isSingle'=> ($id !== null),
        ];

        return view('anggota/cetak_kartu', $data);
    }

    // =========================================================================
    // PRIORITAS 5: DOWNLOAD TEMPLATE & IMPORT DATA EXCEL/CSV
    // =========================================================================
    public function downloadTemplate()
    {
        $filename = 'template_import_anggota.csv';
        $header = ['Nomor Anggota (Opsional)', 'Nama Lengkap', 'Tipe (siswa/guru/karyawan)', 'Kelas atau Jabatan', 'Nomor Identitas (NISN/NIP)', 'Jenis Kelamin (L/P)', 'Kontak / WA', 'Email', 'Alamat'];
        $samples = [
            ['', 'Muhammad Fatih', 'siswa', 'Kelas 4A', '0098234121', 'L', '081234567890', 'fatih@example.com', 'Jl. Kenanga No. 12'],
            ['', 'Siti Nurhaliza', 'siswa', 'Kelas 5B', '0098234122', 'P', '081298765432', 'siti@example.com', 'Jl. Melati No. 5'],
            ['', 'Drs. Hendra Gunawan', 'guru', 'Wali Kelas 6A', '198005122005011002', 'L', '081377889900', 'hendra@example.com', 'Perumahan Guru Blok B']
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
            return redirect()->to(site_url('/anggota'))->with('error', 'File unggahan tidak valid atau belum dipilih.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return redirect()->to(site_url('/anggota'))->with('error', 'Hanya file format CSV (.csv) yang didukung.');
        }

        $filepath = $file->getTempName();
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return redirect()->to(site_url('/anggota'))->with('error', 'Gagal membaca file CSV.');
        }

        // Deteksi delimiter (koma atau titik koma)
        $firstLine = fgets($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 1000, $delimiter);
        $importedCount = 0;
        $skippedCount  = 0;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (empty($row) || count($row) < 4) continue;

            $nomorAnggota = trim($row[0] ?? '');
            $nama         = trim($row[1] ?? '');
            $tipe         = strtolower(trim($row[2] ?? 'siswa'));
            $kelas        = trim($row[3] ?? '');
            $noIdentitas  = trim($row[4] ?? '');
            $jk           = strtoupper(trim($row[5] ?? 'L'));
            $kontak       = trim($row[6] ?? '-');
            $email        = trim($row[7] ?? '');
            $alamat       = trim($row[8] ?? '');

            if (empty($nama) || empty($noIdentitas)) {
                $skippedCount++;
                continue;
            }

            // Validasi duplikasi no_identitas
            if ($this->anggotaModel->where('no_identitas', $noIdentitas)->first()) {
                $skippedCount++;
                continue;
            }

            if (!in_array($tipe, ['siswa', 'guru', 'karyawan'])) {
                $tipe = 'siswa';
            }
            if (!in_array($jk, ['L', 'P'])) {
                $jk = 'L';
            }

            if (empty($nomorAnggota) || $this->anggotaModel->where('nomor_anggota', $nomorAnggota)->first()) {
                $nomorAnggota = $this->anggotaModel->generateNomorAnggota();
            }

            $this->anggotaModel->insert([
                'nomor_anggota' => $nomorAnggota,
                'nama'          => $nama,
                'tipe_anggota'  => $tipe,
                'kelas'         => $kelas ?: null,
                'no_identitas'  => $noIdentitas,
                'jenis_kelamin' => $jk,
                'kontak'        => $kontak,
                'email'         => $email ?: null,
                'alamat'        => $alamat ?: null,
                'status'        => 'aktif',
            ]);

            $importedCount++;
        }

        fclose($handle);

        $msg = "Import berhasil: {$importedCount} data anggota baru telah ditambahkan ke sistem.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris dilewati karena duplikasi atau data tidak lengkap).";
        }

        return redirect()->to(site_url('/anggota'))->with('success', $msg);
    }
}
