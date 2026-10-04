<?php

namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\PengaturanModel;
use App\Libraries\CacheInvalidator;

class Katalog extends BaseController
{
    protected $bukuModel;
    protected $kategoriModel;
    protected $pengaturanModel;

    /**
     * Cache TTL for katalog data (seconds).
     * 10 minutes — auto-invalidated when book data changes.
     */
    private const CACHE_TTL = 600;

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

        $cache = \Config\Services::cache();

        // --- Cached: Kategori list (shared across all filter combinations) ---
        $kategoriCacheKey = 'katalog_kategori_list';
        $kategori = $cache->get($kategoriCacheKey);
        if ($kategori === null) {
            $kategori = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();
            $cache->save($kategoriCacheKey, $kategori, self::CACHE_TTL);
            CacheInvalidator::registerKatalogKey($kategoriCacheKey);
        }

        // --- Cached: Pengaturan config (shared) ---
        $configCacheKey = 'katalog_config';
        $config = $cache->get($configCacheKey);
        if ($config === null) {
            $config = $this->pengaturanModel->getSemuaMap();
            $cache->save($configCacheKey, $config, self::CACHE_TTL);
            CacheInvalidator::registerKatalogKey($configCacheKey);
        }

        // --- Cached: Totals (shared when no filter) ---
        $totalBukuKey     = 'katalog_total_buku';
        $totalTersediaKey = 'katalog_total_tersedia';
        $totalBuku      = $cache->get($totalBukuKey);
        $totalTersedia  = $cache->get($totalTersediaKey);
        if ($totalBuku === null) {
            $totalBuku = $this->bukuModel->where('status', 'aktif')->countAllResults();
            $cache->save($totalBukuKey, $totalBuku, self::CACHE_TTL);
            CacheInvalidator::registerKatalogKey($totalBukuKey);
        }
        if ($totalTersedia === null) {
            $totalTersedia = $this->bukuModel->where('status', 'aktif')->where('stok_tersedia >', 0)->countAllResults();
            $cache->save($totalTersediaKey, $totalTersedia, self::CACHE_TTL);
            CacheInvalidator::registerKatalogKey($totalTersediaKey);
        }

        // --- Cached: Book list (per filter combination) ---
        $filterHash = md5(json_encode([
            'q'            => $keyword ?? '',
            'kategori'     => $kategoriId ?? '',
            'ketersediaan' => $ketersediaan ?? '',
        ]));
        $bukuCacheKey = 'katalog_buku_' . $filterHash;
        $bukuList = $cache->get($bukuCacheKey);
        if ($bukuList === null) {
            $bukuList = $this->bukuModel->getBukuWithKategori(
                null, $keyword, $kategoriId, 'aktif', null, null, null, $ketersediaan, 'ASC'
            );
            $cache->save($bukuCacheKey, $bukuList, self::CACHE_TTL);
            CacheInvalidator::registerKatalogKey($bukuCacheKey);
        }

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
        $cache = \Config\Services::cache();
        $detailCacheKey = 'katalog_detail_' . (int)$id;

        $buku = $cache->get($detailCacheKey);
        if ($buku === null) {
            $buku = $this->bukuModel->getBukuWithKategori($id);
            if ($buku) {
                $cache->save($detailCacheKey, $buku, self::CACHE_TTL);
                CacheInvalidator::registerKatalogKey($detailCacheKey);
            }
        }

        if (!$buku) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku tidak ditemukan'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $buku
        ]);
    }
}
