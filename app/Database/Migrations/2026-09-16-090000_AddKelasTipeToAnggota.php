<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelasTipeToAnggota extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('tipe_anggota', 'anggota')) {
            $fields['tipe_anggota'] = [
                'type'       => 'ENUM',
                'constraint' => ['siswa', 'guru', 'karyawan'],
                'default'    => 'siswa',
                'after'      => 'nama',
            ];
        }

        if (!$this->db->fieldExists('kelas', 'anggota')) {
            $fields['kelas'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'tipe_anggota',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('anggota', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('kelas', 'anggota')) {
            $this->forge->dropColumn('anggota', 'kelas');
        }
        if ($this->db->fieldExists('tipe_anggota', 'anggota')) {
            $this->forge->dropColumn('anggota', 'tipe_anggota');
        }
    }
}
