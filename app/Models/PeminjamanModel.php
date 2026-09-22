<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table            = 'peminjaman';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_transaksi', 'anggota_id', 'buku_id', 'tanggal_pinjam',
        'tanggal_jatuh_tempo', 'status', 'jumlah_perpanjangan', 'tanggal_perpanjangan_terakhir',
        'admin_id', 'catatan', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateKodeTransaksi()
    {
        $date = date('Ymd');
        $prefix = 'PJ-' . $date . '-';
        $last = $this->like('kode_transaksi', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $parts = explode('-', $last['kode_transaksi']);
        $seq = intval(end($parts)) + 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getPeminjamanDetail($id = null, $status = null, $keyword = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('peminjaman.*, 
                                 anggota.nama as nama_anggota, anggota.nomor_anggota, anggota.kontak as kontak_anggota, anggota.kelas as kelas_anggota, anggota.tipe_anggota,
                                 buku.judul as judul_buku, buku.kode_buku, buku.penulis as penulis_buku, buku.lokasi_rak, buku.cover as cover_buku,
                                 kategori_buku.nama_kategori,
                                 admin.nama as nama_admin')
                        ->join('anggota', 'anggota.id = peminjaman.anggota_id')
                        ->join('buku', 'buku.id = peminjaman.buku_id')
                        ->join('kategori_buku', 'kategori_buku.id = buku.kategori_id', 'left')
                        ->join('admin', 'admin.id = peminjaman.admin_id', 'left');

        if ($id !== null) {
            return $builder->where('peminjaman.id', $id)->first();
        }

        if (!empty($status)) {
            if ($status === 'aktif') {
                $builder->whereIn('peminjaman.status', ['dipinjam', 'terlambat']);
            } else {
                $builder->where('peminjaman.status', $status);
            }
        }

        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('peminjaman.kode_transaksi', $keyword)
                    ->orLike('anggota.nama', $keyword)
                    ->orLike('anggota.nomor_anggota', $keyword)
                    ->orLike('buku.judul', $keyword)
                    ->orLike('buku.kode_buku', $keyword)
                    ->groupEnd();
        }

        if (!empty($startDate)) {
            $builder->where('peminjaman.tanggal_pinjam >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('peminjaman.tanggal_pinjam <=', $endDate);
        }

        return $builder->orderBy('peminjaman.id', 'DESC')->findAll();
    }

    public function updateStatusTerlambat()
    {
        $today = date('Y-m-d');
        // Update any active loan where due date has passed
        return $this->where('status', 'dipinjam')
                    ->where('tanggal_jatuh_tempo <', $today)
                    ->set(['status' => 'terlambat'])
                    ->update();
    }

    public function countPinjamanAktifAnggota($anggotaId)
    {
        return $this->where('anggota_id', $anggotaId)
                    ->whereIn('status', ['dipinjam', 'terlambat'])
                    ->countAllResults();
    }

    public function getSemuaLaporanSirkulasi($startDate = null, $endDate = null)
    {
        $builder = $this->select('peminjaman.*, 
                                 anggota.nama as nama_anggota, anggota.nomor_anggota, anggota.no_identitas as identitas_anggota,
                                 buku.judul as judul_buku, buku.kode_buku, buku.penulis as penulis_buku,
                                 kategori_buku.nama_kategori,
                                 pengembalian.tanggal_kembali, pengembalian.jumlah_hari_terlambat, pengembalian.denda, pengembalian.status_denda,
                                 admin.nama as nama_admin')
                        ->join('anggota', 'anggota.id = peminjaman.anggota_id')
                        ->join('buku', 'buku.id = peminjaman.buku_id')
                        ->join('kategori_buku', 'kategori_buku.id = buku.kategori_id', 'left')
                        ->join('pengembalian', 'pengembalian.peminjaman_id = peminjaman.id', 'left')
                        ->join('admin', 'admin.id = peminjaman.admin_id', 'left');

        if (!empty($startDate)) {
            $builder->where('peminjaman.tanggal_pinjam >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('peminjaman.tanggal_pinjam <=', $endDate);
        }

        return $builder->orderBy('peminjaman.id', 'DESC')->findAll();
    }
}
