<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\PetugasModel;
use CodeIgniter\Controller;

class ProfileController extends Controller
{
    public function edit()
    {
        $session = session();
        $role = $session->get('role');

        if ($role === 'admin') {
            $model = new AdminModel();
            $user = $model->find($session->get('admin_id'));
            return view('admin/profile/edit', ['user' => $user]);
        } elseif ($role === 'petugas') {
            $model = new PetugasModel();
            $user = $model->find($session->get('petugas_id'));
            return view('petugas/profile/edit', ['user' => $user]);
        } else {
            return redirect()->to('/')->with('error', 'Akses tidak valid.');
        }
    }

    public function update()
    {
        $session = session();
        $role = $session->get('role');
        $id = $session->get('admin_id') ?? $session->get('petugas_id');
        $request = service('request');

        $nama = $request->getPost('nama');
        $username = $request->getPost('username');
        $password = $request->getPost('password');

        $updateData = [
            'nama' => $nama,
            'username' => $username
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($role === 'admin') {
            $model = new AdminModel();
            $redirectPath = 'profile/edit';
        } elseif ($role === 'petugas') {
            $model = new PetugasModel();
            $redirectPath = 'profile/edit';
        } else {
            return redirect()->to('/')->with('error', 'Akses tidak valid.');
        }

        $model->update($id, $updateData);

        // Perbarui data sesi
        $session->set([
            'username' => $username,
            'nama' => $nama
        ]);

        session()->setFlashdata('success', 'Profil berhasil diperbarui.');
        return redirect()->back();
    }
}
