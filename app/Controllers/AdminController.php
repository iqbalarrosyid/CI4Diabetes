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
            'role' => 'petugas'
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
}
