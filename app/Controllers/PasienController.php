<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RiwayatModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PasienController extends BaseController
{
    protected $pasienModel;

    public function __construct()
    {
        $this->pasienModel = new PasienModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Pasien',
            'pasien' => $this->pasienModel->findAll()
        ];
        return view('petugas/pasien/index', $data);
    }

    public function create()
    {
        return view('petugas/pasien/create', ['title' => 'Tambah Pasien']);
    }

    public function store()
    {
        $this->pasienModel->save([
            'nama' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
        ]);

        return redirect()->to('/petugas/pasien')->with('success', 'Data pasien berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Pasien',
            'pasien' => $this->pasienModel->find($id)
        ];
        return view('petugas/pasien/edit', $data);
    }

    public function update($id)
    {
        $this->pasienModel->update($id, [
            'nama' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
        ]);

        session()->setFlashdata('success', 'Data pasien berhasil diperbarui.');
        return redirect()->to("/petugas/pasien/edit/$id");
    }

    public function delete($id)
    {
        $this->pasienModel->delete($id);
        session()->setFlashdata('success', 'Data pasien berhasil dihapus.');
        return redirect()->to('/petugas/pasien');
    }

    public function import()
    {
        return view('petugas/pasien/import');
    }

    public function upload()
    {
        $file = $this->request->getFile('excel');
        if ($file->isValid() && !$file->hasMoved()) {
            $spreadsheet = IOFactory::load($file->getPathname());
            $data = $spreadsheet->getActiveSheet()->toArray();

            $pasienModel = new PasienModel();
            $riwayatModel = new RiwayatModel();

            for ($i = 1; $i < count($data); $i++) {
                [$nama, $tanggal_lahir, $alamat, $jenis_kelamin, $tekanan_darah, $berat, $tinggi, $gdp, $hasil, $tanggal] = $data[$i];

                // Validasi tanggal
                if (!strtotime($tanggal_lahir) || !strtotime($tanggal)) {
                    return redirect()->back()->with('error', "Format tanggal salah di baris ke-" . ($i + 1));
                }

                // Cek apakah pasien sudah ada
                $pasien = $pasienModel->where([
                    'nama' => $nama,
                    'tanggal_lahir' => $tanggal_lahir
                ])->first();

                if (!$pasien) {
                    $pasienId = $pasienModel->insert([
                        'nama' => $nama,
                        'tanggal_lahir' => $tanggal_lahir,
                        'alamat' => $alamat,
                        'jenis_kelamin' => $jenis_kelamin,
                    ], true);
                } else {
                    $pasienId = $pasien['id'];
                }

                // Hitung IMT
                $tinggiMeter = $tinggi / 100;
                $imt = $tinggiMeter > 0 ? $berat / ($tinggiMeter * $tinggiMeter) : 0;

                // Simpan ke riwayat
                $riwayatModel->insert([
                    'pasien_id' => $pasienId,
                    'gdp' => $gdp,
                    'tekanan_darah' => $tekanan_darah,
                    'tinggi' => $tinggi,
                    'berat' => $berat,
                    'imt' => round($imt, 2),
                    'hasil' => strtolower($hasil) === 'diabetes' ? 1 : 0,
                    'created_at' => date('Y-m-d', strtotime($tanggal)),
                    'nama_petugas' => session()->get('nama'),
                ]);
            }

            return redirect()->to('/petugas/pasien')->with('success', 'Data berhasil diimport!');
        }

        return redirect()->back()->with('error', 'Gagal mengupload file.');
    }
}
