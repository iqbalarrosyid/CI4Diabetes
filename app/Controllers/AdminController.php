<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\PetugasModel;

class AdminController extends BaseController
{
    protected $pasienModel;
    protected $petugasModel;

    public function __construct()
    {
        $this->pasienModel = new PasienModel();
        $this->petugasModel = new PetugasModel();
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
        ]);

        return redirect()->to('/admin/pasien')->with('success', 'Data berhasil ditambahkan.');
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
        ]);

        session()->setFlashdata('success', 'Profil berhasil diperbarui.');
        return redirect()->back();
    }

    public function delete($id)
    {
        $this->pasienModel->delete($id);
        return redirect()->to('/admin/pasien')->with('success', 'Data berhasil dihapus.');
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
            'role' => 'petugas'
        ]);

        return redirect()->to('/admin/petugas')->with('success', 'Petugas berhasil ditambahkan.');
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
        ];

        // Jika password diisi, update dengan hash baru
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->petugasModel->update($id, $data);

        session()->setFlashdata('success', 'Profil berhasil diperbarui.');
        return redirect()->back();
    }

    public function deletePetugas($id)
    {
        $this->petugasModel->delete($id);
        return redirect()->to('/admin/petugas')->with('success', 'Petugas berhasil dihapus.');
    }
}
