<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePlainPasswordFromAdmin extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('plain_password', 'admin')) {
            $this->forge->dropColumn('admin', 'plain_password');
        }
    }

    public function down()
    {
        if (!$this->db->fieldExists('plain_password', 'admin')) {
            $this->forge->addColumn('admin', [
                'plain_password' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'role',
                ],
            ]);
        }
    }
}
