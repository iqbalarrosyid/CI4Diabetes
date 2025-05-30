<?php
// File: tests/app/Controllers/RiwayatControllerTest.php

namespace Tests\App\Controllers;

use App\Controllers\RiwayatController;
use App\Models\PasienModel;
use App\Models\PetugasModel;
use App\Models\RiwayatModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;

class RiwayatControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $migrate    = true;
    protected $refresh    = true;
    protected $namespace  = 'App';

    private int $pasienId;
    private int $petugasId;

    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        $_POST = [];
        $this->aturDataTes(); // Mengubah nama metode internal
    }

    private function aturDataTes() // Mengubah nama metode internal
    {
        $pasienModel = model(PasienModel::class); // Menggunakan model() helper
        $this->pasienId = $pasienModel->insert([
            'nama' => 'Budi Santoso',
            'tanggal_lahir' => '1980-05-15',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Merdeka No. 10',
        ]);

        $petugasModel = model(PetugasModel::class); // Menggunakan model() helper
        $this->petugasId = $petugasModel->insert([
            'nama' => 'Petugas Medis',
            'username' => 'petugas_medis',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);

        $riwayatModel = model(RiwayatModel::class); // Menggunakan model() helper
        $riwayatModel->insert([
            'pasien_id' => $this->pasienId,
            'petugas_id' => $this->petugasId,
            'nama_petugas' => 'Petugas Medis',
            'hasil' => '1', // Asumsi '1' berarti hasil positif atau terdiagnosis
            'created_at' => '2025-01-10 10:00:00',
            // Tambahkan field lain yang relevan jika ada, misal: gdp, tekanan_darah, dll.
            'gdp' => 150, // Contoh data tambahan
            'tekanan_darah' => 140, // Contoh data tambahan
            'tinggi' => 170, // Contoh data tambahan
            'berat' => 80, // Contoh data tambahan
        ]);
    }

    private function mockPermintaanCurl(string $body, int $statusCode = 200) // Mengubah nama metode internal
    {
        $mockResponse = $this->getMockBuilder(\CodeIgniter\HTTP\Response::class)->disableOriginalConstructor()->getMock();
        $mockResponse->method('getBody')->willReturn($body);
        $mockResponse->method('getStatusCode')->willReturn($statusCode);
        $mockClient = $this->getMockBuilder(\CodeIgniter\HTTP\CURLRequest::class)->disableOriginalConstructor()->getMock();
        $mockClient->method('post')->willReturn($mockResponse);
        Services::injectMock('curlrequest', $mockClient);
    }

    // --- Tes untuk method index() ---

    public function testIndeksUntukPasienYangAda() // Mengubah nama fungsi tes
    {
        // SOLUSI: Lengkapi data sesi login
        session()->set(['logged_in' => true, 'role' => 'petugas', 'petugas_id' => $this->petugasId, 'nama' => 'Petugas Medis']);
        $result = $this->controller(RiwayatController::class)->execute('index', $this->pasienId);
        $this->assertTrue($result->isOK());
        $this->assertTrue($result->see('Budi Santoso')); // Memastikan nama pasien tampil di view riwayat
    }

    public function testIndeksUntukPasienYangTidakAda() // Mengubah nama fungsi tes
    {
        // SOLUSI: Lengkapi data sesi login
        session()->set(['logged_in' => true, 'role' => 'petugas', 'petugas_id' => $this->petugasId, 'nama' => 'Petugas Medis']);
        $result = $this->controller(RiwayatController::class)->execute('index', 999); // ID pasien yang tidak ada
        $this->assertTrue($result->isRedirect());
        $this->assertEquals('Data pasien tidak ditemukan.', session()->getFlashdata('error'));
    }

    // --- Tes untuk method predict() ---

    public function testPrediksiAPIBerhasil() // Mengubah nama fungsi tes
    {
        $this->mockPermintaanCurl('{"outcome": 1}', 200); // Sesuaikan dengan respons API Anda
        $requestBody = json_encode([
            'tinggi' => 170,
            'berat' => 80,
            'gdp' => 150,
            'tekanan_darah' => 140,
            'pasien_id' => $this->pasienId
        ]);
        $result = $this->withBody($requestBody)->controller(RiwayatController::class)->execute('predict');
        $this->assertTrue($result->isOK());
        $result->assertJSON('{"hasil":1}'); // Sesuaikan dengan format JSON respons Anda
    }

    public function testPrediksiAPIPasienTidakDitemukan() // Mengubah nama fungsi tes
    {
        // Tidak perlu mockCurlRequest karena seharusnya tidak sampai ke panggilan API eksternal
        $requestBody = json_encode([
            'tinggi' => 170,
            'berat' => 80,
            'gdp' => 150,
            'tekanan_darah' => 140,
            'pasien_id' => 999 // ID pasien yang tidak ada
        ]);
        $result = $this->withBody($requestBody)->controller(RiwayatController::class)->execute('predict');

        // Controller Anda harus mengembalikan respons JSON error jika pasien tidak ditemukan
        // sebelum mencoba memanggil API eksternal.
        // Status code bisa 404 atau 400 tergantung implementasi Anda.
        // $this->assertEquals(404, $result->response()->getStatusCode());
        $result->assertJSONFragment(['error' => true, 'message' => 'Data pasien tidak ditemukan.']);
    }

    // --- Tes untuk method store() ---

    public function testPenyimpananRiwayatBerhasil() // Mengubah nama fungsi tes
    {
        $this->mockPermintaanCurl('{"outcome": 1}', 200); // Asumsi store juga memanggil API prediksi
        session()->set(['petugas_id' => $this->petugasId, 'nama' => 'Petugas Medis', 'logged_in' => true, 'role' => 'petugas']); // Pastikan sesi lengkap
        $_POST = [
            'pasien_id' => $this->pasienId,
            'tinggi' => 175, // Data baru untuk riwayat baru
            'berat' => 85,
            'gdp' => 160,
            'tekanan_darah' => 145,
            // Tambahkan field lain yang diperlukan oleh form Anda
        ];
        $result = $this->controller(RiwayatController::class)->execute('store');
        $this->assertTrue($result->isRedirect()); // Asumsi redirect setelah berhasil
        $this->assertEquals('Data prediksi berhasil disimpan.', session()->getFlashdata('success'));
        $this->seeInDatabase('riwayat', [
            'pasien_id' => $this->pasienId,
            'gdp' => 160, // Verifikasi data yang disimpan
            'hasil' => '1'  // Verifikasi hasil prediksi yang disimpan
        ]);
    }

    public function testPenyimpananRiwayatPasienTidakDitemukan() // Mengubah nama fungsi tes
    {
        // Tidak perlu mockCurlRequest karena seharusnya tidak sampai ke panggilan API
        session()->set(['petugas_id' => $this->petugasId, 'nama' => 'Petugas Medis', 'logged_in' => true, 'role' => 'petugas']);
        $_POST = [
            'pasien_id' => 999, // ID pasien yang tidak ada
            'tinggi' => 170,
            'berat' => 85,
            'gdp' => 160,
            'tekanan_darah' => 145,
        ];
        $result = $this->controller(RiwayatController::class)->execute('store');
        $this->assertTrue($result->isRedirect()); // Asumsi redirect kembali atau ke halaman error
        $this->assertEquals('Pasien tidak ditemukan.', session()->getFlashdata('error'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Services::reset();
    }
}
