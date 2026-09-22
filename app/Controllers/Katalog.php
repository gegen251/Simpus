<?php

namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\PengaturanModel;

class Katalog extends BaseController
{
    protected $bukuModel;
    protected $kategoriModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->bukuModel       = new BukuModel();
        $this->kategoriModel   = new KategoriModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $keyword      = $this->request->getGet('q');
        $kategoriId   = $this->request->getGet('kategori');
        $ketersediaan = $this->request->getGet('ketersediaan');

        // Fetch books for public catalog (only active books)
        $bukuList = $this->bukuModel->getBukuWithKategori(
            null, $keyword, $kategoriId, 'aktif', null, null, null, $ketersediaan, 'ASC'
        );

        $kategori = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();
        $config   = $this->pengaturanModel->getSemuaMap();

        // Count totals
        $totalBuku      = $this->bukuModel->where('status', 'aktif')->countAllResults();
        $totalTersedia  = $this->bukuModel->where('status', 'aktif')->where('stok_tersedia >', 0)->countAllResults();

        $data = [
            'title'         => 'Katalog Perpustakaan Terbuka (OPAC)',
            'buku'          => $bukuList,
            'kategori'      => $kategori,
            'config'        => $config,
            'keyword'       => $keyword,
            'selectedKat'   => $kategoriId,
            'ketersediaan'  => $ketersediaan,
            'totalBuku'     => $totalBuku,
            'totalTersedia' => $totalTersedia,
        ];

        return view('katalog/index', $data);
    }

    public function detail($id)
    {
        $buku = $this->bukuModel->getBukuWithKategori($id);
        if (!$buku) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku tidak ditemukan'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $buku
        ]);
    }
}
