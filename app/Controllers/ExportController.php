<?php

namespace App\Controllers;

use App\Models\RiwayatModel;
use App\Models\PasienModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DateTime;

class ExportController extends BaseController
{
    protected $riwayatModel;
    protected $pasienModel;

    public function __construct()
    {
        $this->riwayatModel = new RiwayatModel();
        $this->pasienModel = new PasienModel();
    }

    /**
     * Export SEMUA riwayat untuk SATU pasien ke PDF.
     */
    public function exportPasienRiwayatToPdf($pasien_id)
    {
        $pasien = $this->pasienModel->find($pasien_id);
        if (!$pasien) {
            return redirect()->back()->with('error', 'Data pasien tidak ditemukan.');
        }

        // Menggunakan metode yang telah diganti namanya dan fungsinya sesuai
        $riwayat = $this->riwayatModel->getRiwayatForPasien($pasien_id, 'ASC');

        if (empty($riwayat)) {
            return redirect()->back()->with('warning', 'Tidak ada data riwayat untuk pasien ini.');
        }

        $html = view('petugas/riwayat/pdf_template_pasien_all_riwayat', compact('pasien', 'riwayat')); // View spesifik jika perlu

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "Riwayat_Lengkap_Pasien_" . preg_replace('/[^A-Za-z0-9\-]/', '_', $pasien['nama']) . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
        exit();
    }

    /**
     * Export SEMUA riwayat untuk SATU pasien ke Excel.
     */
    public function exportPasienRiwayatToExcel($pasien_id)
    {
        $pasien = $this->pasienModel->find($pasien_id);
        if (!$pasien) {
            return redirect()->back()->with('error', 'Data pasien tidak ditemukan.');
        }

        $riwayat = $this->riwayatModel->getRiwayatForPasien($pasien_id, 'ASC');

        if (empty($riwayat)) {
            return redirect()->back()->with('warning', 'Tidak ada data riwayat untuk pasien ini.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Pasien');

        // Tambahkan informasi pasien di atas tabel jika diinginkan
        $sheet->setCellValue('A1', 'Nama Pasien:');
        $sheet->setCellValue('B1', $pasien['nama']);
        $sheet->setCellValue('A2', 'NIK:'); // Ganti dengan field yang sesuai, misal 'nik'
        $sheet->setCellValue('B2', $pasien['nik'] ?? '-'); // Contoh
        $sheet->setCellValue('A3', 'Tanggal Lahir:');
        $sheet->setCellValue('B3', !empty($pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($pasien['tanggal_lahir'])) : '-');


        $headerRow = 5; // Mulai header tabel dari baris ke-5
        $sheet->setCellValue('A' . $headerRow, 'No')
            ->setCellValue('B' . $headerRow, 'GDP')
            ->setCellValue('C' . $headerRow, 'Tekanan Darah')
            ->setCellValue('D' . $headerRow, 'Berat (kg)')
            ->setCellValue('E' . $headerRow, 'Tinggi (cm)')
            ->setCellValue('F' . $headerRow, 'IMT')
            ->setCellValue('G' . $headerRow, 'Hasil')
            ->setCellValue('H' . $headerRow, 'Waktu Pemeriksaan')
            ->setCellValue('I' . $headerRow, 'Petugas');

        $row = $headerRow + 1;
        $no = 1;
        foreach ($riwayat as $data) {
            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $data['gdp'])
                ->setCellValue('C' . $row, $data['tekanan_darah'])
                ->setCellValue('D' . $row, $data['berat'])
                ->setCellValue('E' . $row, $data['tinggi'])
                ->setCellValue('F' . $row, number_format((float)$data['imt'], 2))
                ->setCellValue('G' . $row, ($data['hasil'] == 1 || strtolower($data['hasil']) === 'diabetes') ? 'Diabetes' : 'Tidak Diabetes')
                ->setCellValue('H' . $row, date('d-m-Y H:i:s', strtotime($data['created_at'])))
                ->setCellValue('I' . $row, $data['nama_petugas'] ?? 'Tidak Diketahui');
            $row++;
        }
        // Auto-size columns for better readability
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Riwayat_Lengkap_Pasien_" . preg_replace('/[^A-Za-z0-9\-]/', '_', $pasien['nama']) . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    /**
     * Export riwayat TERBARU dari SETIAP pasien ke PDF.
     */
    public function exportLatestRiwayatAllPasienToPdf()
    {
        $data['riwayat'] = $this->riwayatModel->getLatestRiwayatPerPasienWithDetails('ASC');

        if (empty($data['riwayat'])) {
            return redirect()->back()->with('warning', 'Tidak ada data riwayat terbaru untuk diekspor.');
        }

        // View ini (pdf_template_latest_all_pasien) perlu disesuaikan
        // untuk menampilkan data dari getLatestRiwayatPerPasienWithDetails
        $html = view('petugas/riwayat/pdf_template_latest_all_pasien', $data);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Mungkin landscape lebih cocok untuk banyak kolom
        $dompdf->render();

        $filename = "Riwayat_Terbaru_Semua_Pasien_" . date('d-m-Y') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
        exit();
    }

    /**
     * Export riwayat TERBARU dari SETIAP pasien ke Excel.
     */
    public function exportLatestRiwayatAllPasienToExcel()
    {
        $riwayat = $this->riwayatModel->getLatestRiwayatPerPasienWithDetails('ASC');

        if (empty($riwayat)) {
            return redirect()->back()->with('warning', 'Tidak ada data riwayat terbaru untuk diekspor.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Terbaru Pasien');

        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama Pasien')
            ->setCellValue('C1', 'Umur')
            ->setCellValue('D1', 'Alamat')
            ->setCellValue('E1', 'GDP')
            ->setCellValue('F1', 'Tekanan Darah')
            ->setCellValue('G1', 'Berat (kg)')
            ->setCellValue('H1', 'Tinggi (cm)')
            ->setCellValue('I1', 'IMT')
            ->setCellValue('J1', 'Hasil')
            ->setCellValue('K1', 'Waktu Pemeriksaan Terakhir')
            ->setCellValue('L1', 'Petugas');

        $row = 2;
        $no = 1;
        foreach ($riwayat as $data) {
            $umur = '-';
            if (!empty($data['tanggal_lahir'])) {
                try {
                    $birthDate = new DateTime($data['tanggal_lahir']);
                    $today = new DateTime();
                    $umur = $today->diff($birthDate)->y . ' Tahun';
                } catch (\Exception $e) {
                    $umur = 'Error Tgl Lahir';
                }
            }

            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $data['nama_pasien'])
                ->setCellValue('C' . $row, $umur)
                ->setCellValue('D' . $row, $data['alamat'] ?? 'Tidak Diketahui')
                ->setCellValue('E' . $row, $data['gdp'])
                ->setCellValue('F' . $row, $data['tekanan_darah'])
                ->setCellValue('G' . $row, $data['berat'])
                ->setCellValue('H' . $row, $data['tinggi'])
                ->setCellValue('I' . $row, number_format((float)$data['imt'], 2))
                ->setCellValue('J' . $row, ($data['hasil'] == 1 || strtolower($data['hasil']) === 'diabetes') ? 'Diabetes' : 'Tidak Diabetes')
                ->setCellValue('K' . $row, date('d-m-Y H:i:s', strtotime($data['created_at'])))
                ->setCellValue('L' . $row, $data['nama_petugas'] ?? 'Tidak Diketahui');
            $row++;
        }
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Riwayat_Terbaru_Semua_Pasien_" . date('d-m-Y') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    /**
     * Export SEMUA riwayat dari SEMUA pasien ke PDF.
     */
    public function exportAllHistoricalRiwayatToPdf()
    {
        // Menggunakan metode baru dari model
        $data['riwayat_all'] = $this->riwayatModel->getAllRiwayatAllPasienWithDetails('ASC');

        if (empty($data['riwayat_all'])) {
            return redirect()->back()->with('warning', 'Tidak ada data riwayat sama sekali untuk diekspor.');
        }

        // Anda perlu membuat atau menyesuaikan view ini: 'petugas/riwayat/pdf_template_all_historical'
        // View ini harus bisa mengiterasi $data['riwayat_all'] yang berisi semua record
        $html = view('petugas/riwayat/pdf_template_all_historical', $data);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Landscape mungkin lebih baik
        $dompdf->render();

        $filename = "Semua_Riwayat_Historis_Pasien_" . date('d-m-Y') . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
        exit();
    }

    /**
     * Export SEMUA riwayat dari SEMUA pasien ke Excel.
     */
    public function exportAllHistoricalRiwayatToExcel()
    {
        $all_riwayat = $this->riwayatModel->getAllRiwayatAllPasienWithDetails('ASC');

        if (empty($all_riwayat)) {
            return redirect()->back()->with('warning', 'Tidak ada data riwayat sama sekali untuk diekspor.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Semua Riwayat Historis');

        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Nama Pasien')
            ->setCellValue('C1', 'Umur Saat Periksa')
            ->setCellValue('D1', 'Alamat Pasien')
            ->setCellValue('E1', 'GDP')
            ->setCellValue('F1', 'Tekanan Darah')
            ->setCellValue('G1', 'Berat (kg)')
            ->setCellValue('H1', 'Tinggi (cm)')
            ->setCellValue('I1', 'IMT')
            ->setCellValue('J1', 'Hasil Klasifikasi')
            ->setCellValue('K1', 'Waktu Pemeriksaan')
            ->setCellValue('L1', 'Petugas Pemeriksa');

        $row = 2;
        $no = 1;
        foreach ($all_riwayat as $data) {
            $umur_saat_periksa = '-';
            if (!empty($data['tanggal_lahir']) && !empty($data['created_at'])) {
                try {
                    $birthDate = new DateTime($data['tanggal_lahir']);
                    $checkDate = new DateTime($data['created_at']);
                    if ($checkDate >= $birthDate) {
                        $umur_saat_periksa = $checkDate->diff($birthDate)->y . ' Tahun';
                    } else {
                        $umur_saat_periksa = 'Tgl Lahir > Tgl Periksa';
                    }
                } catch (\Exception $e) {
                    $umur_saat_periksa = 'Error Kalkulasi Umur';
                }
            }

            $sheet->setCellValue('A' . $row, $no++)
                ->setCellValue('B' . $row, $data['nama_pasien'])
                ->setCellValue('C' . $row, $umur_saat_periksa)
                ->setCellValue('D' . $row, $data['alamat'] ?? 'Tidak Diketahui')
                ->setCellValue('E' . $row, $data['gdp'])
                ->setCellValue('F' . $row, $data['tekanan_darah'])
                ->setCellValue('G' . $row, $data['berat'])
                ->setCellValue('H' . $row, $data['tinggi'])
                ->setCellValue('I' . $row, number_format((float)$data['imt'], 2))
                ->setCellValue('J' . $row, ($data['hasil'] == 1 || strtolower($data['hasil']) === 'diabetes') ? 'Diabetes' : 'Tidak Diabetes')
                ->setCellValue('K' . $row, date('d-m-Y H:i:s', strtotime($data['created_at'])))
                ->setCellValue('L' . $row, $data['nama_petugas'] ?? 'Tidak Diketahui');
            $row++;
        }
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Semua_Riwayat_Historis_Pasien_" . date('d-m-Y') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
