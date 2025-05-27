<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasienTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'alamat' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true, // Alamat bisa jadi opsional
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true, // Tanggal lahir bisa jadi opsional
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['Laki-laki', 'Perempuan'],
                'default'    => 'Laki-laki', // Atau null jika lebih sesuai
                'null'       => true,
            ],
            'created_at' => [ // Kolom baru
                'type' => 'DATETIME',
                'null' => true, // Atau false jika Anda selalu ingin ada nilainya
            ],
            'updated_at' => [ // Kolom baru
                'type' => 'DATETIME',
                'null' => true, // Atau false
            ],
            'deleted_at' => [ // Opsional, untuk soft delete
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pasien');
    }

    public function down()
    {
        $this->forge->dropTable('pasien');
    }
}