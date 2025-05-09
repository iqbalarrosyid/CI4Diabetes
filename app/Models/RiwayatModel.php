<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatModel extends Model
{
    protected $table      = 'riwayat';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pasien_id',
        'petugas_id',
        'nama_petugas', // untuk menyimpan nama petugas saat input
        'tinggi',
        'berat',
        'imt',
        'tekanan_darah',
        'gdp',
        'hasil',
        'created_at'
    ];

    // Fungsi untuk join dengan tabel petugas dan filter berdasarkan pasien_id
    public function getRiwayatWithPetugas($pasien_id, $sortOrder = 'DESC')
    {
        return $this->select('riwayat.*, riwayat.nama_petugas')
            ->where('riwayat.pasien_id', $pasien_id)
            ->orderBy('riwayat.created_at', $sortOrder)
            ->findAll();
    }

    public function getRiwayatTerbaru()
    {
        return $this->select('riwayat.*, pasien.nama AS nama_pasien, pasien.tanggal_lahir, riwayat.nama_petugas, pasien.alamat')
            ->join('pasien', 'pasien.id = riwayat.pasien_id')
            ->where('riwayat.id IN (
                SELECT MAX(id) FROM riwayat GROUP BY pasien_id
            )')
            ->orderBy('riwayat.created_at', 'DESC')
            ->findAll();
    }

    // Fungsi simpan otomatis nama petugas dari petugas_id
    public function saveWithPetugasName(array $data)
    {
        $petugasModel = new \App\Models\PetugasModel();
        $petugas = $petugasModel->find($data['petugas_id'] ?? 0);
        $data['nama_petugas'] = $petugas['nama'] ?? 'Petugas Tidak Dikenal';
        return $this->save($data);
    }
}
