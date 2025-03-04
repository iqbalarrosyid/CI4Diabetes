<!DOCTYPE html>
<html lang="id">
<?php
// Path ke file gambar di folder public
$path = FCPATH . 'logo-bantul.jpg';

// Cek apakah file ada
if (file_exists($path)) {
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
} else {
    $base64 = ''; // Jika file tidak ditemukan
}
?>

<!-- Tambahkan gambar ke PDF -->



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
            margin-top: 10px;
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
                <h4 style="margin: 0; text-align: center;">PEMERINTAH KABUPATEN BANTUL</h4>
                <h4 style="margin: 0; text-align: center;">DINAS KESEHATAN</h4>
                <h3 style="margin: 0; text-align: center;">UPTD PUSKESMAS IMOGIRI I</h3>
                <p style="margin: 0; font-size: 12px; text-align: center;">
                    Alamat: Ngancar Karangtalun, Imogiri, Bantul, DIY, Kode Pos 55782 <br>
                    Telp (0274) 6460694 | Website: <a href="http://pusk-imogiri1.bantulkab.go.id">pusk-imogiri1.bantulkab.go.id</a> <br>
                    Email: pusk.imogiri1@bantulkab.go.id
                </p>
            </td>
        </tr>
    </table>
    <hr>

    <p><b>Riwayat Pasien: <?= esc($pasien['nama']) ?></b></p>
    <p>Tanggal Lahir: <?= date('d-m-Y', strtotime($pasien['tanggal_lahir'])) ?></p>
    <p>Jenis Kelamin: <?= esc($pasien['jenis_kelamin']) ?></p>
    <p>Alamat: <?= esc($pasien['alamat']) ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>GDP</th>
                <th>Tekanan Darah</th>
                <th>Berat</th>
                <th>Tinggi</th>
                <th>IMT</th>
                <th>Hasil*</th>
                <th>Waktu</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($riwayat as $data): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($data['gdp']) ?></td>
                    <td><?= esc($data['tekanan_darah']) ?></td>
                    <td><?= esc($data['berat']) ?> kg</td>
                    <td><?= esc($data['tinggi']) ?> cm</td>
                    <td><?= number_format($data['imt'], 2) ?></td>
                    <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                    <td><?= date('d-m-Y H:i:s', strtotime($data['created_at'])) ?></td>
                    <td><?= esc($data['nama_petugas'] ?? 'Tidak Diketahui') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="text-muted justify-text"><i>(*) Jika sebelumnya hasil prediksi menunjukan pasien Diabetes, kemudian prediksi berubah menjadi Tidak Diabetes, ini menunjukkan bahwa pasien dapat mengendalikan kadar gula darahnya. Bukan berarti sembuh dari diabetes.</i></p>
    <p>Dicetak pada <?= date('d-m-Y H:i:s') ?></p>
</body>

</html>