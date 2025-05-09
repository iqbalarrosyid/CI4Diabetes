<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRiwayatTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pasien_id'       => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'petugas_id'      => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'nama_petugas'    => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tinggi'          => [
                'type'       => 'FLOAT',
            ],
            'berat'           => [
                'type'       => 'FLOAT',
            ],
            'imt'             => [
                'type'       => 'FLOAT',
            ],
            'tekanan_darah'   => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'gdp'             => [
                'type'       => 'FLOAT',
            ],
            'hasil'           => [
                'type'       => 'TEXT',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pasien_id', 'pasien', 'id', 'CASCADE', 'CASCADE');
        // petugas_id TIDAK menggunakan foreign key agar tidak hapus otomatis
        $this->forge->createTable('riwayat');
    }

    public function down()
    {
        $this->forge->dropTable('riwayat');
    }
}
