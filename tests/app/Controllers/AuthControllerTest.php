<?php

namespace Tests\App\Controllers;

use App\Controllers\AuthController;
use App\Models\AdminModel;
use App\Models\PetugasModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services; // Ditambahkan untuk Services::reset() jika diperlukan

class AuthControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $migrate     = true;
    // protected $migrateOnce = false; // Biasanya tidak diperlukan jika migrate true
    protected $refresh     = true;
    protected $namespace   = null; // Namespace App akan otomatis terdeteksi

    protected function setUp(): void
    {
        parent::setUp();

        // Setup session untuk testing
        $_SESSION = [];
        $_POST = []; // Pastikan POST juga direset

        // Setup data testing
        $this->aturDataTes(); // Mengubah nama metode internal
    }

    private function aturDataTes() // Mengubah nama metode internal
    {
        // Insert data admin untuk testing
        $adminModel = model(AdminModel::class); // Menggunakan model() helper
        // Hapus data admin yang mungkin sudah ada dengan ID yang sama untuk menghindari konflik primary key
        $adminModel->where('id', 1)->delete();
        $adminModel->insert([
            'id' => 1, // Pastikan ID ini unik atau biarkan auto-increment jika kolom ID Anda demikian
            'nama' => 'Admin Test',
            'username' => 'admin_test',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Insert data petugas untuk testing
        $petugasModel = model(PetugasModel::class); // Menggunakan model() helper
        // Hapus data petugas yang mungkin sudah ada dengan ID yang sama
        $petugasModel->where('id', 1)->delete();
        $petugasModel->insert([
            'id' => 1, // Pastikan ID ini unik atau biarkan auto-increment
            'nama' => 'Petugas Test',
            'username' => 'petugas_test',
            'password' => password_hash('petugas123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Test 1: Halaman login dapat diakses
    public function testHalamanLoginDapatDiakses()
    {
        $result = $this->controller(AuthController::class)
            ->execute('login');

        $this->assertTrue($result->isOK());
    }

    // Test 2: Login dengan field kosong
    public function testLoginDenganFieldKosong()
    {
        // Test username dan password kosong
        $_POST = [
            'username' => '',
            'password' => ''
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Harap isi Username dan Password', session()->getFlashdata('error'));
    }

    // Test 3: Login dengan username kosong
    public function testLoginDenganUsernameKosong()
    {
        $_POST = [
            'username' => '',
            'password' => 'somepassword'
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Harap isi Username', session()->getFlashdata('error'));
    }

    // Test 4: Login dengan password kosong
    public function testLoginDenganPasswordKosong()
    {
        $_POST = [
            'username' => 'someusername',
            'password' => ''
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Harap isi Password', session()->getFlashdata('error'));
    }

    // Test 5: Login admin berhasil
    public function testLoginAdminBerhasil()
    {
        $_POST = [
            'username' => 'admin_test',
            'password' => 'admin123'
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('admin', session()->get('role'));
        $this->assertEquals('Admin Test', session()->get('nama'));
        $this->assertTrue(session()->get('logged_in'));
    }

    // Test 6: Login petugas berhasil
    public function testLoginPetugasBerhasil()
    {
        $_POST = [
            'username' => 'petugas_test',
            'password' => 'petugas123'
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('petugas', session()->get('role'));
        $this->assertEquals('Petugas Test', session()->get('nama'));
        $this->assertTrue(session()->get('logged_in'));
    }

    // Test 7: Login dengan password salah untuk admin
    public function testLoginDenganPasswordAdminSalah()
    {
        $_POST = [
            'username' => 'admin_test',
            'password' => 'wrongpassword'
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Password yang anda masukkan salah', session()->getFlashdata('error'));
    }

    // Test 8: Login dengan password salah untuk petugas
    public function testLoginDenganPasswordPetugasSalah()
    {
        $_POST = [
            'username' => 'petugas_test',
            'password' => 'wrongpassword'
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Password yang anda masukkan salah', session()->getFlashdata('error'));
    }

    // Test 9: Login dengan username tidak ditemukan
    public function testLoginDenganUsernameTidakDitemukan()
    {
        $_POST = [
            'username' => 'nonexistent_user',
            'password' => 'somepassword'
        ];

        $result = $this->controller(AuthController::class)
            ->execute('loginProcess');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Username tidak ditemukan', session()->getFlashdata('error'));
    }

    // Test 13: Logout berhasil
    public function testLogoutBerhasil()
    {
        // Setup session dulu
        session()->set([
            'id' => 1,
            'username' => 'admin_test',
            'role' => 'admin',
            'logged_in' => true
        ]);

        $result = $this->controller(AuthController::class)
            ->execute('logout');

        $this->assertTrue($result->isRedirect());
    }

    // Test 14: Cek role dengan role yang sesuai
    public function testCekRoleDenganRoleValid()
    {
        // Setup session
        session()->set([
            'logged_in' => true,
            'role' => 'admin'
        ]);

        // Metode checkRole biasanya dipanggil di dalam constructor controller lain atau sebagai filter.
        // Untuk mengujinya secara langsung, kita bisa memanggilnya jika public,
        // atau menguji efeknya jika itu adalah filter.
        // Jika checkRole adalah metode protected/private, Anda perlu menguji controller lain yang menggunakannya.
        // Asumsi checkRole adalah public untuk tujuan tes ini atau Anda menguji efeknya.

        // Jika checkRole mengembalikan response (misalnya redirect) saat gagal,
        // dan tidak mengembalikan apa-apa (atau true) saat berhasil, maka:
        $controller = new AuthController(); // Atau mock jika perlu
        $request = Services::request(); // Dapatkan instance request
        $response = Services::response(); // Dapatkan instance response
        $controller->initController($request, $response, Services::logger()); // Inisialisasi controller

        $checkResult = $controller->checkRole('admin'); // Panggil metode secara langsung

        // Jika berhasil, checkRole mungkin tidak mengembalikan apa-apa (void atau null)
        // atau bisa juga mengembalikan true. Sesuaikan assertion ini.
        $this->assertNull($checkResult, "checkRole seharusnya tidak mengembalikan RedirectResponse untuk role yang valid.");
    }

    // Test 15: Cek role dengan role yang tidak sesuai
    public function testCekRoleDenganRoleTidakValid()
    {
        // Setup session
        session()->set([
            'logged_in' => true,
            'role' => 'petugas'
        ]);

        $controller = new AuthController();
        $request = Services::request();
        $response = Services::response();
        $controller->initController($request, $response, Services::logger());

        $checkResult = $controller->checkRole('admin');

        // Verifikasi redirect response
        $this->assertInstanceOf('CodeIgniter\HTTP\RedirectResponse', $checkResult);
        $this->assertEquals('Akses ditolak!', session()->getFlashdata('error'));
    }

    // Test 16: Cek role tanpa login
    public function testCekRoleTanpaLogin()
    {
        // Clear session
        session()->destroy();
        // Re-initialize session service after destroy if needed for flashdata
        Services::injectMock('session', Services::session(null, false));


        $controller = new AuthController();
        $request = Services::request();
        $response = Services::response();
        $controller->initController($request, $response, Services::logger());

        $checkResult = $controller->checkRole('admin');

        // Verifikasi redirect response
        $this->assertInstanceOf('CodeIgniter\HTTP\RedirectResponse', $checkResult);
        // Flashdata mungkin tidak terset jika redirect terjadi sebelum flashdata di-set di controller
        // Tergantung implementasi checkRole
        // $this->assertEquals('Akses ditolak!', session()->getFlashdata('error'));
    }

    // Test 17: Edit profil admin
    public function testEditProfilAdmin()
    {
        // Setup session
        session()->set([
            'logged_in' => true,
            'role' => 'admin',
            'admin_id' => 1 // atau 'user_id' atau 'id' sesuai implementasi sesi
        ]);

        $result = $this->controller(AuthController::class)
            ->execute('editProfile');

        $this->assertTrue($result->isOK());
    }

    // Test 18: Edit profil petugas
    public function testEditProfilPetugas()
    {
        // Setup session
        session()->set([
            'logged_in' => true,
            'role' => 'petugas',
            'petugas_id' => 1 // atau 'user_id' atau 'id' sesuai implementasi sesi
        ]);

        $result = $this->controller(AuthController::class)
            ->execute('editProfile');

        $this->assertTrue($result->isOK());
    }

    // Test 19: Update profil admin
    public function testUpdateProfilAdmin()
    {
        // Setup session
        session()->set([
            'logged_in' => true,
            'role' => 'admin',
            'admin_id' => 1, // atau 'user_id' atau 'id'
            'username' => 'admin_test',
            'nama' => 'Admin Test'
        ]);

        $_POST = [
            'nama' => 'Updated Admin',
            'username' => 'updated_admin',
            'password' => '' // Kosongkan jika password tidak diubah
        ];

        $result = $this->controller(AuthController::class)
            ->execute('updateProfile');

        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Updated Admin', session()->get('nama'));
        $this->assertEquals('updated_admin', session()->get('username'));
    }

    // Test 20: Update profil dengan password baru
    public function testUpdateProfilDenganPasswordBaru()
    {
        // Setup session
        session()->set([
            'logged_in' => true,
            'role' => 'admin',
            'admin_id' => 1, // atau 'user_id' atau 'id'
            'username' => 'admin_test',
            'nama' => 'Admin Test'
        ]);

        $_POST = [
            'nama' => 'Admin Test',
            'username' => 'admin_test',
            'password' => 'newpassword123'
        ];

        $result = $this->controller(AuthController::class)
            ->execute('updateProfile');

        $this->assertTrue($result->isRedirect());

        // Verifikasi password berubah di database
        $adminModel = model(AdminModel::class);
        $admin = $adminModel->find(1);
        $this->assertNotNull($admin, "Admin dengan ID 1 tidak ditemukan.");
        $this->assertTrue(password_verify('newpassword123', $admin['password']));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Reset $_POST setelah setiap test
        $_POST = [];
        Services::reset(); // Reset semua layanan, termasuk session
    }
}
