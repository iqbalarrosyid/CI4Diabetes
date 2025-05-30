<?php
// File: tests/app/Controllers/AdminControllerTest.php

namespace Tests\App\Controllers;

use App\Controllers\AdminController;
use App\Models\PasienModel;
use App\Models\PetugasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;

class AdminControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $migrate = true;
    protected $refresh = true;
    protected $namespace = 'App';

    private int $adminId; // Untuk menyimpan ID admin yang login
    private int $testPasienId;
    private int $testPetugasId;

    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        $_POST = [];

        Factories::reset();
        // Tidak perlu inject mock model secara eksplisit di sini jika menggunakan model() helper
        // dan DB refresh sudah cukup.

        $this->setupTestData();

        // Mengatur sesi login untuk admin
        // Asumsikan admin memiliki 'role' => 'admin' dan 'logged_in' => true
        // Sesuaikan 'admin_id' atau 'user_id' jika nama field di session berbeda
        session()->set([
            'logged_in' => true,
            'role' => 'admin', // Pastikan role ini sesuai dengan yang dicek di controller/filter
            'user_id' => $this->adminId, // atau 'admin_id' atau 'petugas_id' jika admin juga petugas
            'nama' => 'Admin Tester'
        ]);
    }

    private function setupTestData()
    {
        $petugasModel = model(PetugasModel::class);
        // Buat admin user untuk sesi login
        $this->adminId = $petugasModel->insert([
            'nama' => 'Admin Tester',
            'username' => 'admintest',
            'password' => password_hash('adminpass', PASSWORD_DEFAULT),
            'role' => 'admin', // Pastikan admin memiliki role yang sesuai
        ]);

        // Buat data pasien awal untuk tes edit/delete pasien
        $pasienModel = model(PasienModel::class);
        $this->testPasienId = $pasienModel->insert([
            'nama' => 'Pasien Test Awal',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Uji Coba No. 1',
        ]);

        // Buat data petugas awal untuk tes edit/delete petugas
        // Ini bisa jadi petugas yang berbeda dari admin, atau admin itu sendiri jika admin juga petugas
        $this->testPetugasId = $petugasModel->insert([
            'nama' => 'Petugas Test Awal',
            'username' => 'petugastestawal',
            'password' => password_hash('petugaspass', PASSWORD_DEFAULT),
            'role' => 'petugas',
        ]);
    }

    // --- Tes untuk CRUD Pasien oleh Admin ---

    public function testAdminDapatMengaksesIndeksPasien()
    {
        $result = $this->controller(AdminController::class)->execute('index'); // Asumsi 'index' adalah untuk pasien
        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Daftar Pasien'));
        $this->assertTrue($result->see('Pasien Test Awal'));
    }

    public function testAdminDapatMengaksesHalamanBuatPasien()
    {
        $result = $this->controller(AdminController::class)->execute('create'); // Asumsi 'create' adalah untuk pasien
        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Tambah Pasien'));
    }

    public function testAdminDapatMenyimpanPasien()
    {
        $_POST = [
            'nama' => 'Pasien Baru Admin',
            'alamat' => 'Jl. Admin No. 100',
            'tanggal_lahir' => '2000-02-02',
            'jenis_kelamin' => 'Perempuan',
        ];
        $result = $this->controller(AdminController::class)->execute('store'); // Asumsi 'store' adalah untuk pasien
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data pasien berhasil ditambahkan.', session()->getFlashdata('success'));
        $this->seeInDatabase('pasien', ['nama' => 'Pasien Baru Admin']);
    }

    public function testAdminDapatMengaksesHalamanEditPasien()
    {
        $result = $this->controller(AdminController::class)->execute('edit', $this->testPasienId); // Asumsi 'edit' adalah untuk pasien
        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Edit Pasien'));
        $this->assertTrue($result->see('Pasien Test Awal'));
    }

    public function testAdminDapatMemperbaruiPasien()
    {
        $_POST = [
            'nama' => 'Pasien Test Awal Updated',
            'alamat' => 'Jl. Uji Coba No. 1 Revised',
            'tanggal_lahir' => '1990-01-02',
            'jenis_kelamin' => 'Laki-laki',
        ];
        $result = $this->controller(AdminController::class)->execute('update', $this->testPasienId); // Asumsi 'update' adalah untuk pasien
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data pasien berhasil diperbarui.', session()->getFlashdata('success'));
        $this->seeInDatabase('pasien', ['id' => $this->testPasienId, 'nama' => 'Pasien Test Awal Updated']);
    }

    public function testAdminDapatMenghapusPasien()
    {
        $result = $this->controller(AdminController::class)->execute('delete', $this->testPasienId); // Asumsi 'delete' adalah untuk pasien
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data pasien berhasil dihapus.', session()->getFlashdata('success'));
        $this->dontSeeInDatabase('pasien', ['id' => $this->testPasienId]);
    }

    // --- Tes untuk CRUD Petugas oleh Admin ---

    public function testAdminDapatMengaksesIndeksPetugas()
    {
        $result = $this->controller(AdminController::class)->execute('indexPetugas');
        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Daftar Petugas'));
        $this->assertTrue($result->see('Petugas Test Awal'));
    }

    public function testAdminDapatMengaksesHalamanBuatPetugas()
    {
        $result = $this->controller(AdminController::class)->execute('createPetugas');
        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Tambah Petugas'));
    }

    public function testAdminDapatMenyimpanPetugas()
    {
        $_POST = [
            'nama' => 'Petugas Baru Admin',
            'username' => 'petugasbaruadmin',
            'password' => 'password123',
            // 'role' sudah di-set di controller
        ];
        $result = $this->controller(AdminController::class)->execute('storePetugas');
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Petugas berhasil ditambahkan.', session()->getFlashdata('success'));
        $this->seeInDatabase('petugas', ['username' => 'petugasbaruadmin']);
    }

    public function testAdminDapatMengaksesHalamanEditPetugas()
    {
        $result = $this->controller(AdminController::class)->execute('editPetugas', $this->testPetugasId);
        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Edit Petugas'));
        $this->assertTrue($result->see('Petugas Test Awal'));
    }

    public function testAdminDapatMemperbaruiPetugas()
    {
        $_POST = [
            'nama' => 'Petugas Test Awal Updated',
            'username' => 'petugastestawalrevised',
            // Password bisa dikosongkan jika tidak ingin diubah, atau diisi jika ingin diubah
        ];
        $result = $this->controller(AdminController::class)->execute('updatePetugas', $this->testPetugasId);
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data petugas berhasil diperbarui.', session()->getFlashdata('success'));
        $this->seeInDatabase('petugas', ['id' => $this->testPetugasId, 'username' => 'petugastestawalrevised']);
    }

    public function testAdminDapatMemperbaruiPetugasDenganPassword()
    {
        $_POST = [
            'nama' => 'Petugas Test Awal PwdChange',
            'username' => 'petugastestawalpwd',
            'password' => 'newpassword123'
        ];
        $result = $this->controller(AdminController::class)->execute('updatePetugas', $this->testPetugasId);
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data petugas berhasil diperbarui.', session()->getFlashdata('success'));

        $updatedPetugas = model(PetugasModel::class)->find($this->testPetugasId);
        $this->assertTrue(password_verify('newpassword123', $updatedPetugas['password']));
        $this->assertEquals('Petugas Test Awal PwdChange', $updatedPetugas['nama']);
    }

    public function testAdminDapatMenghapusPetugas()
    {
        // Pastikan tidak menghapus admin yang sedang login jika ID-nya sama
        // Untuk amannya, buat petugas lain khusus untuk dihapus jika $this->testPetugasId sama dengan $this->adminId
        $petugasModel = model(PetugasModel::class);
        $petugasToDeleteId = $petugasModel->insert([
            'nama' => 'Petugas Akan Dihapus',
            'username' => 'deletepetugas',
            'password' => password_hash('deleteme', PASSWORD_DEFAULT),
            'role' => 'petugas',
        ]);

        $result = $this->controller(AdminController::class)->execute('deletePetugas', $petugasToDeleteId);
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data petugas berhasil dihapus.', session()->getFlashdata('success'));
        $this->dontSeeInDatabase('petugas', ['id' => $petugasToDeleteId]);
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        Services::reset();
        $_FILES = []; // Meskipun tidak ada upload, ini adalah praktik yang baik
        Factories::reset();
    }
}
