<?php

namespace App\Controllers;

use App\Models\RiwayatModel;
use App\Models\PasienModel;
use CodeIgniter\HTTP\Client;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DateTime;

class RiwayatController extends BaseController
{
    protected $riwayatModel;
    protected $pasienModel;

    public function __construct()
    {
        $this->riwayatModel = new RiwayatModel();
        $this->pasienModel = new PasienModel();
    }

    // Tampilkan Riwayat Pasien
    public function index($pasien_id)
    {
        // Ambil data pasien
        $pasienModel = new PasienModel();
        $pasien = $pasienModel->find($pasien_id);

        // Cek jika data pasien tidak ditemukan
        if (!$pasien) {
            return redirect()->to('/pasien')->with('error', 'Data pasien tidak ditemukan.');
        }

        // Cek parameter sort (default DESC)
        $sortOrder = $this->request->getGet('sort') === 'asc' ? 'ASC' : 'DESC';

        // Ambil data riwayat BESERTA nama petugas sesuai pasien_id & urutan sort
        $data['riwayat'] = $this->riwayatModel->getRiwayatWithPetugas($pasien_id, $sortOrder);
        $data['pasien'] = $pasien;         // Kirim data pasien ke view
        $data['sort'] = $sortOrder;        // Kirim data sort ke view (penting untuk tombol aktif)

        return view('petugas/riwayat/index', $data);
    }

    public function semuaRiwayat()
    {
        // Ambil semua riwayat terbaru dari setiap pasien
        $data['riwayat'] = $this->riwayatModel->getRiwayatTerbaru();

        return view('petugas/riwayat/all', $data);
    }


    // Form Tambah Prediksi
    public function create($id)
    {
        $data['pasien_id'] = $id;
        return view('petugas/riwayat/create', $data);
    }

    public function predict()
    {
        $json = $this->request->getJSON(); // Ambil data JSON mentah

        $tinggi = $json->tinggi;
        $berat = $json->berat;
        $imt = $berat / (($tinggi / 100) ** 2);
        $gdp = $json->gdp;
        $tekanan_darah = $json->tekanan_darah;
        $pasien_id = $json->pasien_id;

        $pasienModel = new PasienModel();
        $pasien = $pasienModel->find($pasien_id);
        $umur = date_diff(date_create($pasien['tanggal_lahir']), date_create('today'))->y;

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->post('http://localhost:5000/predict', [
                'json' => [
                    'imt' => $imt,
                    'umur' => $umur,
                    'gdp' => $gdp,
                    'tekanan_darah' => $tekanan_darah
                ]
            ]);

            $hasil = json_decode($response->getBody(), true)['outcome'];
            return $this->response->setJSON(['hasil' => $hasil]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }


    // Simpan Prediksi
    public function store()
    {
        $pasien_id = $this->request->getPost('pasien_id');
        $tinggi = $this->request->getPost('tinggi');
        $berat = $this->request->getPost('berat');
        $imt = $berat / (($tinggi / 100) ** 2);
        $gdp = $this->request->getPost('gdp');
        $tekanan_darah = $this->request->getPost('tekanan_darah');

        // Ambil Umur dari Tanggal Lahir
        $pasien = $this->pasienModel->find($pasien_id);
        if (!$pasien) {
            return redirect()->back()->with('error', 'Pasien tidak ditemukan.');
        }

        $umur = date_diff(date_create($pasien['tanggal_lahir']), date_create('today'))->y;

        // Kirim Data ke Flask untuk Prediksi
        $client = \Config\Services::curlrequest();
        $response = $client->post('http://localhost:5000/predict', [
            'json' => [
                'imt' => $imt,
                'umur' => $umur,
                'gdp' => $gdp,
                'tekanan_darah' => $tekanan_darah
            ]
        ]);

        $hasil = json_decode($response->getBody(), true)['outcome'] ?? 'Tidak diketahui';

        // Simpan ke Database
        $this->riwayatModel->save([
            'pasien_id'     => $pasien_id,
            'petugas_id'    => session()->get('petugas_id'),
            'nama_petugas'  => session()->get('nama'),
            'tinggi'        => $tinggi,
            'berat'         => $berat,
            'imt'           => $imt,
            'tekanan_darah' => $tekanan_darah,
            'gdp'           => $gdp,
            'hasil'         => $hasil,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/petugas/riwayat/' . $pasien_id)->with('success', 'Data prediksi berhasil disimpan.');
    }

    public function exportPdf($pasien_id)
    {
        // Ambil data pasien
        $pasien = $this->pasienModel->find($pasien_id);
        if (!$pasien) {
            return redirect()->to('/petugas/pasien')->with('error', 'Data pasien tidak ditemukan.');
        }

        // Ambil riwayat pasien
        $riwayat = $this->riwayatModel->getRiwayatWithPetugas($pasien_id, 'DESC');

        // Load view ke string
        $html = view('petugas/riwayat/pdf_template', compact('pasien', 'riwayat'));

        // Konfigurasi Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output ke browser
        $dompdf->stream("Riwayat_Pasien_" . $pasien['nama'] . ".pdf", ["Attachment" => false]);
    }

    public function exportExcel($pasien_id)
    {
        // Ambil data pasien
        $pasien = $this->pasienModel->find($pasien_id);
        if (!$pasien) {
            return redirect()->to('/petugas/pasien')->with('error', 'Data pasien tidak ditemukan.');
        }

        // Ambil riwayat pasien
        $riwayat = $this->riwayatModel->getRiwayatWithPetugas($pasien_id, 'DESC');

        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'GDP')
            ->setCellValue('C1', 'Tekanan Darah')
            ->setCellValue('D1', 'Berat')
            ->setCellValue('E1', 'Tinggi')
            ->setCellValue('F1', 'IMT')
            ->setCellValue('G1', 'Hasil')
            ->setCellValue('H1', 'Waktu')
            ->setCellValue('I1', 'Petugas');

        // Isi data
        $row = 2;
        $no = 1;
        foreach ($riwayat as $data) {
            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $data['gdp'])
                ->setCellValue('C' . $row, $data['tekanan_darah'])
                ->setCellValue('D' . $row, $data['berat'])
                ->setCellValue('E' . $row, $data['tinggi'])
                ->setCellValue('F' . $row, number_format($data['imt'], 2))
                ->setCellValue('G' . $row, $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes')
                ->setCellValue('H' . $row, date('d-m-Y H:i:s', strtotime($data['created_at'])))
                ->setCellValue('I' . $row, $data['nama_petugas'] ?? 'Tidak Diketahui');
            $row++;
        }

        // Simpan ke file
        $filename = "Riwayat_Pasien_" . $pasien['nama'] . ".xlsx";
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function exportAllPdf()
    {
        $data['riwayat'] = $this->riwayatModel->getRiwayatTerbaru();

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $html = view('/petugas/riwayat/pdf_template_all', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->render();
        $dompdf->stream("Riwayat_Pasien.pdf", ["Attachment" => 0]);
    }

    public function exportAllExcel()
    {
        // Ambil riwayat terbaru
        $riwayat = $this->riwayatModel->getRiwayatTerbaru();

        if (empty($riwayat)) {
            return redirect()->to('/petugas/riwayat')->with('error', 'Data riwayat tidak ditemukan.');
        }

        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama Pasien')
            ->setCellValue('C1', 'Umur')
            ->setCellValue('D1', 'Alamat')
            ->setCellValue('E1', 'GDP')
            ->setCellValue('F1', 'Tekanan Darah')
            ->setCellValue('G1', 'Berat')
            ->setCellValue('H1', 'Tinggi')
            ->setCellValue('I1', 'IMT')
            ->setCellValue('J1', 'Hasil')
            ->setCellValue('K1', 'Waktu')
            ->setCellValue('L1', 'Petugas');

        // Isi data
        $row = 2;
        $no = 1;
        foreach ($riwayat as $data) {
            // Hitung umur berdasarkan tanggal lahir
            $umur = '-';
            if (!empty($data['tanggal_lahir'])) {
                $birthDate = new DateTime($data['tanggal_lahir']);
                $today = new DateTime();
                $umur = $today->diff($birthDate)->y . ' Tahun';
            }

            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $data['nama_pasien'])
                ->setCellValue('C' . $row, $umur)
                ->setCellValue('D' . $row, $data['alamat'] ?? 'Tidak Diketahui')
                ->setCellValue('E' . $row, $data['gdp'])
                ->setCellValue('F' . $row, $data['tekanan_darah'])
                ->setCellValue('G' . $row, $data['berat'])
                ->setCellValue('H' . $row, $data['tinggi'])
                ->setCellValue('I' . $row, number_format($data['imt'], 2))
                ->setCellValue('J' . $row, $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes')
                ->setCellValue('K' . $row, date('d-m-Y H:i:s', strtotime($data['created_at'])))
                ->setCellValue('L' . $row, $data['nama_petugas'] ?? 'Tidak Diketahui');
            $row++;
        }

        // Nama file
        $filename = "Riwayat_Pasien_" . date('d-m-Y') . ".xlsx";

        // Atur header agar file bisa diunduh
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
