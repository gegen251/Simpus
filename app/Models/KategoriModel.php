<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori_buku';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kategori', 'keterangan', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getKategoriWithCount($keyword = null, $order = 'ASC')
    {
        $builder = $this->select('kategori_buku.*, COUNT(buku.id) as total_buku')
                        ->join('buku', 'buku.kategori_id = kategori_buku.id', 'left')
                        ->groupBy('kategori_buku.id');

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('kategori_buku.nama_kategori', $keyword)
                    ->orLike('kategori_buku.keterangan', $keyword)
                    ->groupEnd();
        }

        if ($order === 'DESC') {
            $builder->orderBy('kategori_buku.nama_kategori', 'DESC');
        } else {
            $builder->orderBy('kategori_buku.nama_kategori', 'ASC');
        }

        return $builder->findAll();
    }
}
