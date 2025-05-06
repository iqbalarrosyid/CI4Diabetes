<?php

namespace App\Controllers;

use App\Models\PasienModel;

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

        return redirect()->to('/petugas/pasien')->with('success', 'Data berhasil ditambahkan.');
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

        session()->setFlashdata('success', 'Profil berhasil diperbarui.');
        return redirect()->back();
    }

    public function delete($id)
    {
        $this->pasienModel->delete($id);
        return redirect()->to('/petugas/pasien')->with('success', 'Data berhasil dihapus.');
    }
}
