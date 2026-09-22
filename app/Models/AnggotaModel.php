<?php

namespace App\Models;

use CodeIgniter\Model;

class AnggotaModel extends Model
{
    protected $table            = 'anggota';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_anggota', 'nama', 'tipe_anggota', 'kelas', 'foto', 'no_identitas', 'jenis_kelamin',
        'kontak', 'email', 'alamat', 'status', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateNomorAnggota()
    {
        $last = $this->select('nomor_anggota')->orderBy('id', 'DESC')->first();
        if (!$last || !preg_match('/AG-(\d+)/', $last['nomor_anggota'], $m)) {
            return 'AG-0001';
        }
        $num = intval($m[1]) + 1;
        return 'AG-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getAnggotaWithPinjamanCount()
    {
        return $this->select('anggota.*, 
            (SELECT COUNT(peminjaman.id) FROM peminjaman WHERE peminjaman.anggota_id = anggota.id AND peminjaman.status IN ("dipinjam", "terlambat")) AS pinjaman_aktif')
            ->orderBy('anggota.id', 'DESC')
            ->findAll();
    }
}
