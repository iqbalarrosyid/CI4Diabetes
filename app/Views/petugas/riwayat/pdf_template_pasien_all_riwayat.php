<!DOCTYPE html>
<html lang="id">
<?php
// Path ke file gambar di folder public
$path = FCPATH . 'logo-bantul.png';

// Cek apakah file ada
if (file_exists($path)) {
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
} else {
    $base64 = ''; // Jika file tidak ditemukan
}

// Array bulan Indonesia
$bulanIndo = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember',
];

// Ambil tanggal sekarang dalam format Indonesia
$tgl = date('d');
$bln = $bulanIndo[date('m')];
$thn = date('Y');
$jam = date('H:i:s');

// Fungsi untuk konversi tanggal ke format "10 Februari 2025"
function formatTanggalIndo($tanggal, $bulanIndo)
{
    $dateObj = date_create($tanggal);
    $tgl = date_format($dateObj, 'd');
    $bln = $bulanIndo[date_format($dateObj, 'm')];
    $thn = date_format($dateObj, 'Y');
    return "$tgl $bln $thn";
}
?>

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pasien <?= esc($pasien['nama']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 80px;
            height: auto;
        }

        .kop-text {
            text-align: center;
            flex: 1;
        }

        .kop-text h2,
        .kop-text h3,
        .kop-text p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .justify-text {
            text-align: justify;
        }

        p {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <!-- KOP SURAT -->
    <table width="100%" style="border-collapse: collapse;">
        <tr>
            <td width="10%" align="center" style="border: none;">
                <img src="<?= $base64 ?>" alt="Logo Bantul" width="100">
            </td>
            <td width="90%" align="center" style="border: none; padding: none;">
                <h3 style="margin: 0; text-align: center;">PEMERINTAH KABUPATEN BANTUL</h3>
                <h3 style="margin: 0; text-align: center;">DINAS KESEHATAN</h3>
                <h2 style="margin: 0; text-align: center;">UPTD PUSKESMAS IMOGIRI I</h2>
                <p style="margin: 0; font-size: 12px; text-align: center;">
                    Alamat: Ngancar Karangtalun, Imogiri, Bantul, DIY, Kode Pos 55782 <br>
                    Telp (0274) 6460694 | Website: <a href="http://pusk-imogiri1.bantulkab.go.id">pusk-imogiri1.bantulkab.go.id</a> <br>
                    Email: pusk.imogiri1@bantulkab.go.id
                </p>
            </td>
        </tr>
    </table>
    <hr>
    <?php
    // Hitung umur secara otomatis
    $tanggalLahir = new DateTime($pasien['tanggal_lahir']);
    $sekarang = new DateTime();
    $umur = $sekarang->diff($tanggalLahir);
    ?>
    <p><b>Riwayat Pasien: <?= esc($pasien['nama']) ?></b></p>
    <p>Tanggal Lahir : <?= formatTanggalIndo($pasien['tanggal_lahir'], $bulanIndo) ?> / <?= $umur->y; ?> tahun</p>
    <p>Jenis Kelamin : <?= esc($pasien['jenis_kelamin']) ?></p>
    <p>Alamat : <?= esc($pasien['alamat']) ?></p>

    <table style="font-size: 13px;">
        <thead>
            <tr>
                <th>No</th>
                <th>GDP (mg/dL)</th>
                <th>Tekanan Darah (mmHg)</th>
                <th>Berat (kg)</th>
                <th>Tinggi (cm)</th>
                <th>IMT (kg/cm2)</th>
                <th>Hasil*</th>
                <th>Waktu</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($riwayat as $data): ?>
                <?php
                $tanggalFormat = date_create($data['created_at']);
                $tanggalCetak = date_format($tanggalFormat, 'd');
                $bulanCetak = $bulanIndo[date_format($tanggalFormat, 'm')];
                $tahunCetak = date_format($tanggalFormat, 'Y');
                $jamCetak = date_format($tanggalFormat, 'H:i:s');
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($data['gdp']) ?></td>
                    <td><?= esc($data['tekanan_darah']) ?></td>
                    <td><?= esc($data['berat']) ?></td>
                    <td><?= esc($data['tinggi']) ?></td>
                    <td><?= number_format($data['imt'], 2) ?></td>
                    <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                    <td><?= "$tanggalCetak $bulanCetak $tahunCetak $jamCetak" ?></td>
                    <td><?= esc($data['nama_petugas'] ?? 'Tidak Diketahui') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="text-muted justify-text"><i>(*) Jika sebelumnya hasil Klasifikasi menunjukan pasien Diabetes, kemudian Klasifikasi berubah menjadi Tidak Diabetes, ini menunjukkan bahwa pasien dapat mengendalikan kadar gula darahnya. Bukan berarti sembuh dari diabetes.</i></p>

    <p>Dicetak pada <?= "$tgl $bln $thn Pukul $jam" ?></p>
</body>

</html>