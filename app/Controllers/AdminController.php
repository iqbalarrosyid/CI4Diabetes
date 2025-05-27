<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\RiwayatModel;
use App\Models\PetugasModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminController extends BaseController
{
    protected $pasienModel;
    protected $petugasModel;
    protected $riwayatModel;

    public function __construct()
    {
        $this->pasienModel = new PasienModel();
        $this->petugasModel = new PetugasModel();
        $this->riwayatModel = new RiwayatModel();
    }

    public function dashboard()
    {
        $totalPasien = $this->pasienModel->countAllResults();
        $totalPetugas = $this->petugasModel->countAllResults();

        // Data untuk Daftar Aktivitas Terbaru
        // Diasumsikan tabel petugas dan pasien memiliki kolom 'created_at'
        $petugasBaru = $this->petugasModel->orderBy('created_at', 'DESC')->findAll(5);
        $pasienBaru = $this->pasienModel->orderBy('created_at', 'DESC')->findAll(5);

        $data = [
            'title'         => 'Dashboard Admin',
            'totalPasien'   => $totalPasien,
            'totalPetugas'  => $totalPetugas,
            'petugasBaru'   => $petugasBaru,
            'pasienBaru'    => $pasienBaru,
        ];

        return view('admin/dashboard', $data); // Path ke view dashboard admin
    }

    // ========== CRUD PASIEN ==========
    public function index()
    {
        $data = [
            'title' => 'Daftar Pasien',
            'pasien' => $this->pasienModel->findAll()
        ];
        return view('admin/pasien/index', $data);
    }

    public function create()
    {
        return view('admin/pasien/create', ['title' => 'Tambah Pasien']);
    }

    public function store()
    {
        $this->pasienModel->save([
            'nama' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Data pasien berhasil ditambahkan.');
        return redirect()->to('/admin/pasien');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Pasien',
            'pasien' => $this->pasienModel->find($id)
        ];
        return view('admin/pasien/edit', $data);
    }

    public function update($id)
    {
        $this->pasienModel->update($id, [
            'nama' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Data pasien berhasil diperbarui.');
        return redirect()->to("/admin/pasien/edit/$id");
    }

    public function delete($id)
    {
        $this->pasienModel->delete($id);
        session()->setFlashdata('success', 'Data pasien berhasil dihapus.');
        return redirect()->to('/admin/pasien');
    }

    // ========== CRUD PETUGAS ==========
    public function indexPetugas()
    {
        $data = [
            'title' => 'Daftar Petugas',
            'petugas' => $this->petugasModel->findAll()
        ];
        return view('admin/petugas/index', $data);
    }

    public function createPetugas()
    {
        return view('admin/petugas/create', ['title' => 'Tambah Petugas']);
    }

    public function storePetugas()
    {
        $this->petugasModel->save([
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'petugas',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Petugas berhasil ditambahkan.');
        return redirect()->to('/admin/petugas');
    }

    public function editPetugas($id)
    {
        $data = [
            'title' => 'Edit Petugas',
            'petugas' => $this->petugasModel->find($id)
        ];
        return view('admin/petugas/edit', $data);
    }

    public function updatePetugas($id)
    {
        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Jika password diisi, update dengan hash baru
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->petugasModel->update($id, $data);

        session()->setFlashdata('success', 'Data petugas berhasil diperbarui.');
        return redirect()->to("/admin/petugas/edit/$id");
    }

    public function deletePetugas($id)
    {
        $this->petugasModel->delete($id);
        session()->setFlashdata('success', 'Data petugas berhasil dihapus.');
        return redirect()->to('/admin/petugas');
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
                [$nama, $tanggal_lahir, $alamat, $jenis_kelamin, $tekanan_darah, $berat, $tinggi, $gdp, $hasil, $tanggal, $nama_petugas] = $data[$i];

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
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
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
                    'nama_petugas' => $nama_petugas,
                ]);
            }

            return redirect()->to('/admin/pasien')->with('success', 'Data berhasil diimport!');
        }

        return redirect()->back()->with('error', 'Gagal mengupload file.');
    }
}
