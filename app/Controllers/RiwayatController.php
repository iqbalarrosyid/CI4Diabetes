<?php

namespace App\Controllers;

use App\Models\RiwayatModel;
use App\Models\PasienModel;
use CodeIgniter\HTTP\Client; // Masih dibutuhkan untuk predict & store
use DateTime; // Masih dibutuhkan untuk predict & store

class RiwayatController extends BaseController
{
    protected $riwayatModel;
    protected $pasienModel;

    public function __construct()
    {
        $this->riwayatModel = new RiwayatModel();
        $this->pasienModel = new PasienModel();
    }

    // Tampilkan Riwayat Pasien
    public function index($pasien_id)
    {
        // Ambil data pasien
        $pasien = $this->pasienModel->find($pasien_id);

        // Cek jika data pasien tidak ditemukan
        if (!$pasien) {
            return redirect()->to('/pasien')->with('error', 'Data pasien tidak ditemukan.');
        }

        // Cek parameter sort (default DESC)
        $sortOrder = $this->request->getGet('sort') === 'asc' ? 'ASC' : 'DESC';

        // Ambil data riwayat BESERTA nama petugas sesuai pasien_id & urutan sort
        $data['riwayat'] = $this->riwayatModel->getRiwayatForPasien($pasien_id, $sortOrder);
        $data['pasien'] = $pasien;      // Kirim data pasien ke view
        $data['sort'] = $sortOrder;     // Kirim data sort ke view (penting untuk tombol aktif)

        return view('petugas/riwayat/index', $data);
    }

    public function semuaRiwayat()
    {
        // Ambil semua riwayat terbaru dari setiap pasien
        $data['riwayat'] = $this->riwayatModel->getLatestRiwayatPerPasienWithDetails();

        return view('petugas/riwayat/all', $data);
    }

    // Form Tambah Prediksi
    public function create($id)
    {
        $data['pasien_id'] = $id;
        return view('petugas/riwayat/create', $data);
    }

    public function predict()
    {
        $json = $this->request->getJSON(); // Ambil data JSON mentah

        $tinggi = $json->tinggi;
        $berat = $json->berat;
        $imt = $berat / (($tinggi / 100) ** 2);
        $gdp = $json->gdp;
        $tekanan_darah = $json->tekanan_darah;
        $pasien_id = $json->pasien_id;

        $pasien = $this->pasienModel->find($pasien_id);
        if (!$pasien) {
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Data pasien tidak ditemukan.'
            ], 404);
        }
        $umur = date_diff(date_create($pasien['tanggal_lahir']), date_create('today'))->y;

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->post('https://iqbalarrosyid.pythonanywhere.com/predict', [
                'json' => [
                    'imt' => $imt,
                    'umur' => $umur,
                    'gdp' => $gdp,
                    'tekanan_darah' => $tekanan_darah
                ]
            ]);

            $hasil = json_decode($response->getBody(), true)['outcome'];
            return $this->response->setJSON(['hasil' => $hasil]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Simpan Prediksi
    public function store()
    {
        $pasien_id = $this->request->getPost('pasien_id');
        $tinggi = $this->request->getPost('tinggi');
        $berat = $this->request->getPost('berat');
        $imt = $berat / (($tinggi / 100) ** 2);
        $gdp = $this->request->getPost('gdp');
        $tekanan_darah = $this->request->getPost('tekanan_darah');

        // Ambil Umur dari Tanggal Lahir
        $pasien = $this->pasienModel->find($pasien_id);
        if (!$pasien) {
            return redirect()->back()->with('error', 'Pasien tidak ditemukan.');
        }

        $umur = date_diff(date_create($pasien['tanggal_lahir']), date_create('today'))->y;

        // Kirim Data ke Flask untuk Prediksi
        $client = \Config\Services::curlrequest();
        $response = $client->post('https://iqbalarrosyid.pythonanywhere.com/predict', [
            'json' => [
                'imt' => $imt,
                'umur' => $umur,
                'gdp' => $gdp,
                'tekanan_darah' => $tekanan_darah
            ]
        ]);

        $hasil = json_decode($response->getBody(), true)['outcome'] ?? 'Tidak diketahui';

        // Simpan ke Database
        $this->riwayatModel->save([
            'pasien_id'     => $pasien_id,
            'petugas_id'    => session()->get('petugas_id'),
            'nama_petugas'  => session()->get('nama'),
            'tinggi'        => $tinggi,
            'berat'         => $berat,
            'imt'           => $imt,
            'tekanan_darah' => $tekanan_darah,
            'gdp'           => $gdp,
            'hasil'         => $hasil,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/petugas/riwayat/' . $pasien_id)->with('success', 'Data prediksi berhasil disimpan.');
    }
}
