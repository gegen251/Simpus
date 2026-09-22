<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['kunci', 'nilai', 'keterangan', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getKunci($kunci, $default = null)
    {
        $row = $this->where('kunci', $kunci)->first();
        return $row ? $row['nilai'] : $default;
    }

    public function getSemuaMap()
    {
        $rows = $this->findAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['kunci']] = $r['nilai'];
        }
        return $map;
    }

    public function updateKunci($kunci, $nilai)
    {
        $row = $this->where('kunci', $kunci)->first();
        if ($row) {
            return $this->update($row['id'], ['nilai' => $nilai]);
        }
        return $this->insert(['kunci' => $kunci, 'nilai' => $nilai]);
    }
}
