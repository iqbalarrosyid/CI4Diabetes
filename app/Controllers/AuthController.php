<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\PetugasModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $session = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $adminModel = new AdminModel();
        $petugasModel = new PetugasModel();

        // Cek login sebagai admin
        $admin = $adminModel->where('username', $username)->first();
        if ($admin && password_verify($password, $admin['password'])) {
            $session->set([
                'id' => $admin['id'],
                'username' => $admin['username'],
                'nama' => $admin['nama'],
                'role' => 'admin',
                'logged_in' => true
            ]);
            return redirect()->to('/dashboard');
        }

        // Cek login sebagai petugas
        $petugas = $petugasModel->where('username', $username)->first();
        if ($petugas && password_verify($password, $petugas['password'])) {
            $session->set([
                'id' => $petugas['id'],
                'petugas_id' => $petugas['id'], // Tambahkan ini
                'username' => $petugas['username'],
                'nama' => $petugas['nama'],
                'role' => 'petugas',
                'logged_in' => true
            ]);
            return redirect()->to('/pasien');
        }

        return redirect()->back()->with('error', 'Username atau password salah.');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function registerProcess()
    {
        $role = $this->request->getPost('role'); // admin atau petugas
        $nama = $this->request->getPost('nama');
        $username = $this->request->getPost('username');
        $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        if ($role === 'admin') {
            $model = new AdminModel();
        } else {
            $model = new PetugasModel();
        }

        $model->save([
            'nama' => $nama,
            'username' => $username,
            'password' => $password,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/')->with('success', 'Registrasi berhasil. Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
