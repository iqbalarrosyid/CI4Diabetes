<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Cek apakah user sudah login
        if (!$session->get('logged_in')) {
            session()->setFlashdata('error', 'Silakan login terlebih dahulu');
            return redirect()->to('/');
        }

        // Cek apakah ada role yang harus dicek
        if (!empty($arguments) && !in_array($session->get('role'), $arguments)) {
            session()->setFlashdata('error', 'Akses ditolak! Silahkan login dengan role yang sesuai');
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu aksi setelah request
    }
}
