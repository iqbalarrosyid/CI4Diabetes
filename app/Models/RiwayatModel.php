<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatModel extends Model
{
    protected $table      = 'riwayat';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pasien_id', 'petugas_id', 'tinggi', 'berat', 'imt', 'tekanan_darah', 'gdp', 'hasil', 'created_at'];

    // Fungsi untuk join dengan tabel petugas dan filter berdasarkan pasien_id
    public function getRiwayatWithPetugas($pasien_id, $sortOrder = 'DESC')
    {
        return $this->select('riwayat.*, petugas.nama AS nama_petugas')
            ->join('petugas', 'petugas.id = riwayat.petugas_id', 'left')
            ->where('riwayat.pasien_id', $pasien_id) // Filter berdasarkan pasien_id
            ->orderBy('riwayat.created_at', $sortOrder)
            ->findAll();
    }
    public function getRiwayatTerbaru()
    {
        return $this->select('riwayat.*, pasien.nama AS nama_pasien, pasien.tanggal_lahir, petugas.nama AS nama_petugas, pasien.alamat')
            ->join('pasien', 'pasien.id = riwayat.pasien_id')
            ->join('petugas', 'petugas.id = riwayat.petugas_id', 'left')
            ->where('riwayat.id IN (
            SELECT MAX(id) FROM riwayat GROUP BY pasien_id
        )')
            ->orderBy('riwayat.created_at', 'DESC')
            ->findAll();
    }
}
