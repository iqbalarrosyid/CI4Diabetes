<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RiwayatModel;

class DashboardController extends BaseController
{
    protected $pasienModel;
    protected $riwayatModel;

    public function __construct()
    {
        $this->pasienModel = new PasienModel();
        $this->riwayatModel = new RiwayatModel();
    }

    public function index()
    {
        // Data untuk Kartu Statistik Ringkas
        $totalPasien = $this->pasienModel->countAllResults();
        $totalPrediksi = $this->riwayatModel->countAllResults();

        // Data untuk status diabetes (berdasarkan riwayat terbaru per pasien)
        // Ini memerlukan logika/fungsi khusus di model untuk efisiensi
        // Untuk sementara, kita bisa membuat placeholder atau query sederhana
        // $statusCounts = $this->riwayatModel->getPatientStatusCounts(); // Fungsi ideal di RiwayatModel
        // Placeholder sederhana (perlu disempurnakan dengan query yang benar di model):
        $latestRiwayatPerPasien = $this->riwayatModel->getLatestRiwayatPerPasienWithDetails(); // Menggunakan fungsi yang sudah ada

        $pasienDiabetesCount = 0;
        $pasienNonDiabetesCount = 0;
        $uniquePasienWithRiwayat = [];

        foreach ($latestRiwayatPerPasien as $riwayat) {
            if (!in_array($riwayat['pasien_id'], $uniquePasienWithRiwayat)) {
                $uniquePasienWithRiwayat[] = $riwayat['pasien_id'];
                if ($riwayat['hasil'] == 1 || strtolower($riwayat['hasil']) === 'diabetes') {
                    $pasienDiabetesCount++;
                } else {
                    $pasienNonDiabetesCount++;
                }
            }
        }
        $pasienBelumPrediksiCount = $totalPasien - count($uniquePasienWithRiwayat);


        // Data untuk Daftar Aktivitas Terbaru
        $pasienBaru = $this->pasienModel->orderBy('created_at', 'DESC')->findAll(8);
        $prediksiTerbaru = $this->riwayatModel
            ->select('riwayat.*, pasien.nama as nama_pasien, riwayat.nama_petugas as nama_petugas_pemeriksa') // Ambil nama pasien dan nama petugas dari riwayat
            ->join('pasien', 'pasien.id = riwayat.pasien_id', 'left')
            ->orderBy('riwayat.created_at', 'DESC')
            ->findAll(5);

        $data = [
            'title'                 => 'Dashboard Petugas',
            'totalPasien'           => $totalPasien,
            'totalPrediksi'         => $totalPrediksi,
            'pasienDiabetes'        => $pasienDiabetesCount,
            'pasienNonDiabetes'     => $pasienNonDiabetesCount,
            'pasienBaru'            => $pasienBaru,
            'prediksiTerbaru'       => $prediksiTerbaru,
        ];

        return view('petugas/dashboard', $data); // Sesuaikan path jika nama file view berbeda
    }
}
