<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCoverToBuku extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('cover', 'buku')) {
            $this->forge->addColumn('buku', [
                'cover' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'deskripsi',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('cover', 'buku')) {
            $this->forge->dropColumn('buku', 'cover');
        }
    }
}
