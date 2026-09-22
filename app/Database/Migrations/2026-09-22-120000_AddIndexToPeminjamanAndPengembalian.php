<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToPeminjamanAndPengembalian extends Migration
{
    public function up()
    {
        // 1. Tambah index pada kolom peminjaman.status untuk akselerasi query filter transaksi aktif & sirkulasi
        $this->db->query("CREATE INDEX idx_peminjaman_status ON peminjaman (status)");

        // 2. Tambah index pada kolom pengembalian.status_denda untuk akselerasi query cek tanggungan denda
        $this->db->query("CREATE INDEX idx_pengembalian_status_denda ON pengembalian (status_denda)");
    }

    public function down()
    {
        $this->db->query("DROP INDEX idx_peminjaman_status ON peminjaman");
        $this->db->query("DROP INDEX idx_pengembalian_status_denda ON pengembalian");
    }
}
