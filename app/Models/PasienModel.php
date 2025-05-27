<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table            = 'pasien';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true; // Biasanya sudah default true

    protected $returnType       = 'array'; // Atau 'object' atau class Anda
    protected $useSoftDeletes   = false; // Set true jika Anda menggunakan kolom deleted_at

    // Field yang diizinkan untuk diisi (mass assignment)
    // Tambahkan 'created_at' dan 'updated_at' jika Anda ingin mengisinya manual,
    // tapi biasanya tidak perlu jika useTimestamps true.
    protected $allowedFields    = [
        'nama',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin'
        // 'created_at', 'updated_at' // Biasanya tidak perlu jika useTimestamps = true
    ];

    // Mengaktifkan penggunaan timestamp otomatis
    protected $useTimestamps      = true;
    protected $createdField       = 'created_at'; // Nama kolom untuk created at
    protected $updatedField       = 'updated_at'; // Nama kolom untuk updated at
    protected $deletedField       = 'deleted_at'; // Nama kolom untuk soft delete (jika useSoftDeletes true)

    // Validasi (opsional, tapi sangat direkomendasikan)
    protected $validationRules    = [
        'nama'          => 'required|min_length[3]|max_length[100]',
        'tanggal_lahir' => 'permit_empty|valid_date',
        'jenis_kelamin' => 'permit_empty|in_list[Laki-laki,Perempuan]',
    ];
    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama pasien wajib diisi.',
            'min_length' => 'Nama pasien minimal 3 karakter.',
            'max_length' => 'Nama pasien maksimal 100 karakter.',
        ],
    ];
    protected $skipValidation     = false; // Jangan lewati validasi
}
