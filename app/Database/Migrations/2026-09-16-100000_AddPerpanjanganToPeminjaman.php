<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerpanjanganToPeminjaman extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('jumlah_perpanjangan', 'peminjaman')) {
            $fields['jumlah_perpanjangan'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'status',
            ];
        }

        if (!$this->db->fieldExists('tanggal_perpanjangan_terakhir', 'peminjaman')) {
            $fields['tanggal_perpanjangan_terakhir'] = [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'jumlah_perpanjangan',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('peminjaman', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tanggal_perpanjangan_terakhir', 'peminjaman')) {
            $this->forge->dropColumn('peminjaman', 'tanggal_perpanjangan_terakhir');
        }
        if ($this->db->fieldExists('jumlah_perpanjangan', 'peminjaman')) {
            $this->forge->dropColumn('peminjaman', 'jumlah_perpanjangan');
        }
    }
}
