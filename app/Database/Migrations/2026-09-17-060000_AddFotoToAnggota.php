<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToAnggota extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('foto', 'anggota')) {
            $this->forge->addColumn('anggota', [
                'foto' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'kelas',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('foto', 'anggota')) {
            $this->forge->dropColumn('anggota', 'foto');
        }
    }
}
