<?php

namespace App\Models;

use CodeIgniter\Model;

class BukuModel extends Model
{
    protected $table            = 'buku';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_buku', 'judul', 'penulis', 'penerbit', 'isbn',
        'kategori_id', 'tahun_terbit', 'jumlah_eksemplar',
        'stok_tersedia', 'lokasi_rak', 'status', 'deskripsi', 'cover',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getBukuWithKategori(
        $id = null,
        $keyword = null,
        $kategoriId = null,
        $status = null,
        $lokasiRak = null,
        $penulis = null,
        $penerbit = null,
        $ketersediaan = null,
        $order = 'ASC'
    ) {
        $builder = $this->select('buku.*, kategori_buku.nama_kategori')
                        ->join('kategori_buku', 'kategori_buku.id = buku.kategori_id', 'left');

        if ($id !== null) {
            return $builder->where('buku.id', $id)->first();
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('buku.judul', $keyword)
                    ->orLike('buku.penulis', $keyword)
                    ->orLike('buku.penerbit', $keyword)
                    ->orLike('buku.isbn', $keyword)
                    ->orLike('buku.kode_buku', $keyword)
                    ->orLike('buku.lokasi_rak', $keyword)
                    ->groupEnd();
        }

        if (!empty($kategoriId)) {
            $builder->where('buku.kategori_id', $kategoriId);
        }

        if (!empty($status)) {
            $builder->where('buku.status', $status);
        }

        if (!empty($lokasiRak)) {
            $builder->like('buku.lokasi_rak', $lokasiRak);
        }

        if (!empty($penulis)) {
            $builder->like('buku.penulis', $penulis);
        }

        if (!empty($penerbit)) {
            $builder->like('buku.penerbit', $penerbit);
        }

        if (!empty($ketersediaan)) {
            if ($ketersediaan === 'tersedia') {
                $builder->where('buku.stok_tersedia >', 0);
            } elseif ($ketersediaan === 'habis') {
                $builder->where('buku.stok_tersedia <=', 0);
            }
        }

        $dir = (strtoupper($order) === 'DESC') ? 'DESC' : 'ASC';
        return $builder->orderBy('buku.kode_buku', $dir)->findAll();
    }

    public function generateKodeBuku()
    {
        $last = $this->select('kode_buku')->orderBy('id', 'DESC')->first();
        if (!$last || !preg_match('/BK-(\d+)/', $last['kode_buku'], $m)) {
            return 'BK-0001';
        }
        $num = intval($m[1]) + 1;
        return 'BK-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
