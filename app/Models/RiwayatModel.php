<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatModel extends Model
{
    protected $table = 'riwayat';
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

    /**
     * Mengambil semua riwayat untuk pasien tertentu, beserta nama petugas.
     * Diurutkan berdasarkan waktu pembuatan.
     */
    public function getRiwayatForPasien($pasien_id, $sortOrder = 'DESC')
    {
        return $this->select('riwayat.*, riwayat.nama_petugas') // nama_petugas sudah ada di tabel riwayat
            ->where('riwayat.pasien_id', $pasien_id)
            ->orderBy('riwayat.created_at', $sortOrder)
            ->findAll();
    }

    /**
     * Mengambil riwayat terbaru dari setiap pasien, beserta detail pasien dan nama petugas.
     * Diurutkan berdasarkan waktu pembuatan riwayat terbaru.
     */
    public function getLatestRiwayatPerPasienWithDetails($sortOrder = 'DESC')
    {
        // Subquery untuk mendapatkan ID riwayat terbaru per pasien_id
        $subQuery = $this->db->table('riwayat')
            ->select('MAX(id) as max_id')
            ->groupBy('pasien_id')
            ->getCompiledSelect();

        return $this->select('riwayat.*, pasien.nama AS nama_pasien, pasien.tanggal_lahir, pasien.alamat, riwayat.nama_petugas')
            ->join('pasien', 'pasien.id = riwayat.pasien_id')
            ->where("riwayat.id IN ({$subQuery})", null, false) // false untuk mencegah escape query
            ->orderBy('riwayat.created_at', $sortOrder)
            ->findAll();
    }

    /**
     * Mengambil SEMUA riwayat dari SEMUA pasien, beserta detail pasien dan nama petugas.
     * Diurutkan berdasarkan waktu pembuatan.
     */
    public function getAllRiwayatAllPasienWithDetails($sortOrder = 'DESC')
    {
        return $this->select('riwayat.*, pasien.nama AS nama_pasien, pasien.tanggal_lahir, pasien.alamat, riwayat.nama_petugas')
            ->join('pasien', 'pasien.id = riwayat.pasien_id')
            ->orderBy('riwayat.created_at', $sortOrder)
            ->findAll();
    }


    // Fungsi simpan otomatis nama petugas dari petugas_id
    // Catatan: Pastikan $data['petugas_id'] tersedia saat memanggil fungsi ini.
    // Jika Anda sudah menyimpan nama_petugas langsung dari session di controller, fungsi ini mungkin tidak
    // selalu diperlukan untuk 'save', tapi bisa berguna untuk 'update' jika petugas_id diubah.
    public function saveWithPetugasName(array $data)
    {
        if (isset($data['petugas_id']) && !isset($data['nama_petugas'])) {
            $petugasModel = new \App\Models\PetugasModel(); // Pastikan PetugasModel ada
            $petugas = $petugasModel->find($data['petugas_id']);
            $data['nama_petugas'] = $petugas['nama'] ?? 'Petugas Tidak Dikenal';
        } elseif (!isset($data['nama_petugas'])) {
            // Fallback jika petugas_id tidak ada dan nama_petugas juga tidak ada
            $data['nama_petugas'] = 'Sistem';
        }
        return $this->save($data);
    }
}
