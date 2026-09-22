<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlainPasswordToAdmin extends Migration
{
    public function up()
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

            // Set default known passwords for existing users
            $this->db->query("UPDATE admin SET plain_password = 'admin123' WHERE username = 'admin'");
            $this->db->query("UPDATE admin SET plain_password = 'staf123' WHERE username = 'budisantoso'");
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('plain_password', 'admin')) {
            $this->forge->dropColumn('admin', 'plain_password');
        }
    }
}
