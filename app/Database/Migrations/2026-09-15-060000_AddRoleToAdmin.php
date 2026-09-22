<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToAdmin extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('role', 'admin')) {
            $this->forge->addColumn('admin', [
                'role' => [
                    'type'       => 'ENUM',
                    'constraint' => ['admin', 'staf'],
                    'default'    => 'staf',
                    'after'      => 'password',
                ],
            ]);

            // Set existing primary user as admin
            $this->db->query("UPDATE admin SET role = 'admin' WHERE id = 1");
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('role', 'admin')) {
            $this->forge->dropColumn('admin', 'role');
        }
    }
}
