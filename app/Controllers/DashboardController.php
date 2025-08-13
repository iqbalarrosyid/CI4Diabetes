<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RiwayatModel;
// Pastikan use DateTime, DateInterval, DatePeriod jika masih ada fungsi helper yang memakainya
// Untuk dashboard yang disederhanakan ini, sepertinya tidak lagi dibutuhkan.

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
        // $totalKlasifikasi = $this->riwayatModel->countAllResults(); // Ini tidak akan ditampilkan lagi di kartu

        $latestRiwayatPerPasien = $this->riwayatModel->getLatestRiwayatPerPasienWithDetails();

        $pasienDiabetesCount = 0;
        $pasienNonDiabetesCount = 0;
        $uniquePasienWithRiwayat = []; // Array untuk menyimpan ID pasien yang sudah punya riwayat

        foreach ($latestRiwayatPerPasien as $riwayat) {
            // Memastikan setiap pasien hanya dihitung sekali untuk status diabetes/non-diabetes
            if (!in_array($riwayat['pasien_id'], $uniquePasienWithRiwayat)) {
                $uniquePasienWithRiwayat[] = $riwayat['pasien_id'];
                if ($riwayat['hasil'] == 1 || strtolower($riwayat['hasil']) === 'diabetes') {
                    $pasienDiabetesCount++;
                } else if (($riwayat['hasil'] == 0 || strtolower($riwayat['hasil']) === 'tidak diabetes') && $riwayat['hasil'] !== null && $riwayat['hasil'] !== '') {
                    // Hanya hitung sebagai non-diabetes jika hasilnya eksplisit 0 atau 'tidak diabetes'
                    $pasienNonDiabetesCount++;
                }
            }
        }

        // Menghitung pasien yang belum memiliki riwayat sama sekali
        $pasienBelumAdaRiwayatCount = $totalPasien - count($uniquePasienWithRiwayat);


        // Data untuk Daftar Aktivitas Terbaru
        $pasienBaru = $this->pasienModel->orderBy('created_at', 'DESC')->findAll(8);
        $KlasifikasiTerbaru = $this->riwayatModel
            ->select('riwayat.*, pasien.nama as nama_pasien, riwayat.nama_petugas as nama_petugas_pemeriksa')
            ->join('pasien', 'pasien.id = riwayat.pasien_id', 'left')
            ->orderBy('riwayat.created_at', 'DESC')
            ->findAll(5);

        $data = [
            'title'                   => 'Dashboard Petugas',
            'totalPasien'             => $totalPasien,
            // 'totalKlasifikasi'           => $totalKlasifikasi, // Dihapus dari data yang dikirim ke view kartu ini
            'pasienDiabetes'          => $pasienDiabetesCount,
            'pasienNonDiabetes'       => $pasienNonDiabetesCount,
            'pasienBelumAdaRiwayat'   => $pasienBelumAdaRiwayatCount, // Data baru untuk kartu
            'pasienBaru'              => $pasienBaru,
            'KlasifikasiTerbaru'         => $KlasifikasiTerbaru,
        ];

        return view('petugas/dashboard', $data);
    }
}
