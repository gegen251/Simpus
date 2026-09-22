<?php

namespace App\Controllers;

use App\Models\PengaturanModel;
use App\Models\AuditLogModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $data = [
            'title'       => 'Pengaturan Sistem & Perpustakaan',
            'active_menu' => 'pengaturan',
            'pengaturan'  => $this->pengaturanModel->getSemuaMap(),
        ];

        return view('pengaturan/index', $data);
    }

    public function update()
    {
        $rules = [
            'nama_perpustakaan'     => 'required|min_length[3]|max_length[255]',
            'alamat_perpustakaan'   => 'required|max_length[500]',
            'durasi_pinjam_default' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[60]',
            'tarif_denda_per_hari'  => 'required|numeric|greater_than_equal_to[0]',
            'max_pinjam_buku'       => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[20]',
        ];

        if (!$this->validate($rules)) {
            $err = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('error', 'Validasi pengaturan gagal: ' . implode(', ', $err));
        }

        $inputs = [
            'nama_perpustakaan',
            'alamat_perpustakaan',
            'kepala_perpustakaan',
            'nip_kepala',
            'durasi_pinjam_default',
            'tarif_denda_per_hari',
            'max_pinjam_buku',
            'kiosk_allowed_ips',
            'kiosk_secret_token',
        ];

        $updatedKeys = [];
        foreach ($inputs as $key) {
            $val = $this->request->getPost($key);
            if ($val !== null) {
                $this->pengaturanModel->updateKunci($key, trim($val));
                $updatedKeys[] = $key;
            }
        }

        // Audit Log
        AuditLogModel::record(
            'UBAH_PENGATURAN',
            'Memperbarui konfigurasi parameter perpustakaan: ' . implode(', ', $updatedKeys) . '.'
        );

        return redirect()->to(site_url('/pengaturan'))->with('success', 'Pengaturan sistem perpustakaan berhasil disimpan.');
    }
}
