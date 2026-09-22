<?php

namespace App\Models;

use CodeIgniter\Model;

class PengembalianModel extends Model
{
    protected $table            = 'pengembalian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'peminjaman_id', 'tanggal_kembali', 'jumlah_hari_terlambat',
        'denda', 'status_denda', 'admin_id', 'catatan',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPengembalianDetail($id = null, $keyword = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('pengembalian.*,
                                 peminjaman.kode_transaksi, peminjaman.tanggal_pinjam, peminjaman.tanggal_jatuh_tempo,
                                 anggota.nama as nama_anggota, anggota.nomor_anggota,
                                 buku.judul as judul_buku, buku.kode_buku,
                                 admin.nama as nama_admin')
                        ->join('peminjaman', 'peminjaman.id = pengembalian.peminjaman_id')
                        ->join('anggota', 'anggota.id = peminjaman.anggota_id')
                        ->join('buku', 'buku.id = peminjaman.buku_id')
                        ->join('admin', 'admin.id = pengembalian.admin_id', 'left');

        if ($id !== null) {
            return $builder->where('pengembalian.id', $id)->first();
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('peminjaman.kode_transaksi', $keyword)
                    ->orLike('anggota.nama', $keyword)
                    ->orLike('anggota.nomor_anggota', $keyword)
                    ->orLike('buku.judul', $keyword)
                    ->groupEnd();
        }

        if (!empty($startDate)) {
            $builder->where('pengembalian.tanggal_kembali >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('pengembalian.tanggal_kembali <=', $endDate);
        }

        return $builder->orderBy('pengembalian.id', 'DESC')->findAll();
    }
}
