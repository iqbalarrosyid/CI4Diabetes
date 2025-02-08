<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table      = 'pasien';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'alamat', 'tanggal_lahir', 'jenis_kelamin'];
}
