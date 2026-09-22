<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeysToTables extends Migration
{
    public function up()
    {
        $constraints = [
            [
                'name'  => 'fk_buku_kategori',
                'table' => 'buku',
                'sql'   => 'ALTER TABLE buku ADD CONSTRAINT fk_buku_kategori FOREIGN KEY (kategori_id) REFERENCES kategori_buku(id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ],
            [
                'name'  => 'fk_peminjaman_anggota',
                'table' => 'peminjaman',
                'sql'   => 'ALTER TABLE peminjaman ADD CONSTRAINT fk_peminjaman_anggota FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ],
            [
                'name'  => 'fk_peminjaman_buku',
                'table' => 'peminjaman',
                'sql'   => 'ALTER TABLE peminjaman ADD CONSTRAINT fk_peminjaman_buku FOREIGN KEY (buku_id) REFERENCES buku(id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ],
            [
                'name'  => 'fk_peminjaman_admin',
                'table' => 'peminjaman',
                'sql'   => 'ALTER TABLE peminjaman ADD CONSTRAINT fk_peminjaman_admin FOREIGN KEY (admin_id) REFERENCES admin(id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ],
            [
                'name'  => 'fk_pengembalian_peminjaman',
                'table' => 'pengembalian',
                'sql'   => 'ALTER TABLE pengembalian ADD CONSTRAINT fk_pengembalian_peminjaman FOREIGN KEY (peminjaman_id) REFERENCES peminjaman(id) ON UPDATE CASCADE ON DELETE CASCADE',
            ],
            [
                'name'  => 'fk_pengembalian_admin',
                'table' => 'pengembalian',
                'sql'   => 'ALTER TABLE pengembalian ADD CONSTRAINT fk_pengembalian_admin FOREIGN KEY (admin_id) REFERENCES admin(id) ON UPDATE CASCADE ON DELETE RESTRICT',
            ],
        ];

        foreach ($constraints as $c) {
            $check = $this->db->query(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = ?",
                [$c['name']]
            )->getRow();

            if (!$check) {
                $this->db->query($c['sql']);
            }
        }
    }

    public function down()
    {
        $drops = [
            ['table' => 'pengembalian', 'name' => 'fk_pengembalian_admin'],
            ['table' => 'pengembalian', 'name' => 'fk_pengembalian_peminjaman'],
            ['table' => 'peminjaman',   'name' => 'fk_peminjaman_admin'],
            ['table' => 'peminjaman',   'name' => 'fk_peminjaman_buku'],
            ['table' => 'peminjaman',   'name' => 'fk_peminjaman_anggota'],
            ['table' => 'buku',         'name' => 'fk_buku_kategori'],
        ];

        foreach ($drops as $d) {
            $check = $this->db->query(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = ?",
                [$d['name']]
            )->getRow();

            if ($check) {
                $this->db->query("ALTER TABLE {$d['table']} DROP FOREIGN KEY {$d['name']}");
            }
        }
    }
}
