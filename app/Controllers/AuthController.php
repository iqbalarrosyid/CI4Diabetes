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
        $request = service('request');
        $username = $request->getPost('username');
        $password = $request->getPost('password');

        // 1. Cek jika ada form yang tidak diisi
        if (empty($username) || empty($password)) {
            $errorMessage = '';
            if (empty($username) && empty($password)) {
                $errorMessage = 'Harap isi Username dan Password';
            } elseif (empty($username)) {
                $errorMessage = 'Harap isi Username';
            } else { // empty($password)
                $errorMessage = 'Harap isi Password';
            }
            $session->setFlashdata('error', $errorMessage);
            return redirect()->to('/');
        }

        // 2. Cek di tabel admin
        $adminModel = new \App\Models\AdminModel();
        $admin = $adminModel->where('username', $username)->first();

        if ($admin) {
            // Username admin ditemukan
            if (password_verify($password, $admin['password'])) {
                // Password benar
                $session->set([
                    'id' => $admin['id'],
                    'admin_id' => $admin['id'],
                    'username' => $admin['username'],
                    'nama' => $admin['nama'],
                    'role' => 'admin',
                    'logged_in' => true
                ]);
                $session->setFlashdata('successLogin', 'Login berhasil, Selamat datang ' . $admin['nama']);
                return redirect()->to('/admin');
            } else {
                // Password salah untuk admin
                $session->setFlashdata('error', 'Password yang anda masukkan salah');
                return redirect()->to('/');
            }
        }

        // 3. Jika bukan admin (username admin tidak ditemukan), cek di tabel petugas
        $petugasModel = new \App\Models\PetugasModel();
        $petugas = $petugasModel->where('username', $username)->first();

        if ($petugas) {
            // Username petugas ditemukan
            if (password_verify($password, $petugas['password'])) {
                // Password benar
                $session->set([
                    'id' => $petugas['id'],
                    'petugas_id' => $petugas['id'],
                    'username' => $petugas['username'],
                    'nama' => $petugas['nama'],
                    'role' => 'petugas',
                    'logged_in' => true
                ]);
                $session->setFlashdata('successLogin', 'Login berhasil, Selamat datang ' . $petugas['nama']);
                return redirect()->to('/petugas');
            } else {
                // Password salah untuk petugas
                $session->setFlashdata('error', 'Password yang anda masukkan salah');
                return redirect()->to('/');
            }
        }

        // 4. Jika username tidak ditemukan di kedua tabel (admin dan petugas)
        // Kondisi ini tercapai jika $admin NULL dan $petugas NULL
        $session->setFlashdata('error', 'Username tidak ditemukan');
        return redirect()->to('/');
    }


    public function register()
    {
        return view('auth/register');
    }

    public function registerProcess()
    {
        $role = $this->request->getPost('role');
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

    public function checkRole($role)
    {
        if (!session()->get('logged_in') || session()->get('role') !== $role) {
            session()->setFlashdata('error', 'Akses ditolak!');
            return redirect()->to('/');
        }
    }
    public function editProfile()
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

    public function updateProfile()
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
