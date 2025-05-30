<?php
// File: tests/app/Controllers/PasienControllerTest.php

namespace Tests\App\Controllers;

use App\Controllers\PasienController;
use App\Models\PasienModel;
use App\Models\PetugasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;


class PasienControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $migrate = true;
    protected $refresh = true;
    protected $namespace = 'App';

    private int $pasienId;
    private int $petugasId;

    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        $_POST = [];

        Factories::reset();
        // Inisialisasi model di sini jika diperlukan, atau biarkan autoloader/Factories menanganinya.
        // Jika Anda secara eksplisit membuat instance model dalam tes, pastikan mereka di-reset atau di-mock dengan benar.
        // Untuk kesederhanaan, kita akan mengandalkan autoloader atau model() helper.

        $this->aturDataTes(); // Mengubah nama metode internal

        session()->set([
            'logged_in' => true,
            'role' => 'petugas',
            'petugas_id' => $this->petugasId,
            'nama' => 'Petugas Tester'
        ]);
    }

    private function aturDataTes() // Mengubah nama metode internal
    {
        $petugasModel = model(PetugasModel::class);
        $this->petugasId = $petugasModel->insert([
            'nama' => 'Petugas Tester',
            'username' => 'petugastest',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $pasienModel = model(PasienModel::class);
        $this->pasienId = $pasienModel->insert([
            'nama' => 'Budi Santoso',
            'tanggal_lahir' => '1980-05-15',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Merdeka No. 10',
        ]);
    }

    // --- Tes untuk method index() ---

    public function testHalamanIndeksDapatDiakses() // Mengubah nama fungsi tes
    {
        $result = $this->controller(PasienController::class)->execute('index');

        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Daftar Pasien'));
        $this->assertTrue($result->see('Budi Santoso'));
    }

    // --- Tes untuk method create() ---

    public function testHalamanBuatDapatDiakses() // Mengubah nama fungsi tes
    {
        $result = $this->controller(PasienController::class)->execute('create');

        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Tambah Pasien'));
    }

    // --- Tes untuk method store() ---

    public function testPenyimpananBerhasil() // Mengubah nama fungsi tes
    {
        $_POST = [
            'nama' => 'Siti Aminah',
            'alamat' => 'Jl. Pahlawan No. 25',
            'tanggal_lahir' => '1992-11-20',
            'jenis_kelamin' => 'Perempuan',
        ];

        $result = $this->controller(PasienController::class)->execute('store');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data pasien berhasil ditambahkan.', session()->getFlashdata('success'));
        $this->seeInDatabase('pasien', ['nama' => 'Siti Aminah', 'alamat' => 'Jl. Pahlawan No. 25']);
    }

    // --- Tes untuk method edit() ---

    public function testHalamanEditDapatDiakses() // Mengubah nama fungsi tes
    {
        $result = $this->controller(PasienController::class)->execute('edit', $this->pasienId);

        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Edit Pasien'));
        $this->assertTrue($result->see('Budi Santoso'));
    }

    // --- Tes untuk method update() ---

    public function testPembaruanBerhasil() // Mengubah nama fungsi tes
    {
        $_POST = [
            'nama' => 'Budi Santoso Updated',
            'alamat' => 'Jl. Kemerdekaan No. 11',
            'tanggal_lahir' => '1980-05-16',
            'jenis_kelamin' => 'Laki-laki',
        ];

        $result = $this->controller(PasienController::class)->execute('update', $this->pasienId);

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data pasien berhasil diperbarui.', session()->getFlashdata('success'));
        $this->seeInDatabase('pasien', ['id' => $this->pasienId, 'nama' => 'Budi Santoso Updated']);
    }

    // --- Tes untuk method delete() ---

    public function testPenghapusanBerhasil() // Mengubah nama fungsi tes
    {
        $result = $this->controller(PasienController::class)->execute('delete', $this->pasienId);

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data pasien berhasil dihapus.', session()->getFlashdata('success'));
        $this->dontSeeInDatabase('pasien', ['id' => $this->pasienId]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Services::reset();
        $_FILES = [];
        Factories::reset();
    }
}
