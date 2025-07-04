<!DOCTYPE html>
<html>
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
?>

<head>
    <title>Riwayat Pasien</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            /* Ubah rata kiri */
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
            /* Ubah rata kiri */
        }

        p {
            font-size: 12px;
        }
    </style>
</head>

<body>
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
    $bulan = [
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

    $tgl = date('d');
    $bln = $bulan[date('m')];
    $thn = date('Y');

    echo "<h4>Seluruh Data Riwayat Pemeriksaan Diabetes Pasien Tanggal {$tgl} {$bln} {$thn}</h4>";
    ?>

<?php
    // Array bulan Indonesia
    $bulanIndo = [
        '01' => 'Jan',
        '02' => 'Feb',
        '03' => 'Mar',
        '04' => 'Apr',
        '05' => 'Mei',
        '06' => 'Jun',
        '07' => 'Jul',
        '08' => 'Ags',
        '09' => 'Sep',
        '10' => 'Okt',
        '11' => 'Nov',
        '12' => 'Des',
    ];

    // Fungsi format tanggal
    function formatTanggalIndoLengkap($timestamp, $bulanIndo)
    {
        $tanggal = date('d', strtotime($timestamp));
        $bulan = $bulanIndo[date('m', strtotime($timestamp))];
        $tahun = date('Y', strtotime($timestamp));
        $jam = date('H:i:s', strtotime($timestamp));
        return "$tanggal $bulan $tahun $jam";
    }
    ?>
    <table style="font-size: 12px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Umur</th>
                <th>Alamat</th>
                <th>GDP (mg/dL)</th>
                <th>Tekanan Darah (mmHg)</th>
                <th>Berat (kg)</th>
                <th>Tinggi (cm)</th>
                <th>IMT (kg/cm2)</th>
                <th>Hasil</th>
                <th>Waktu</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($riwayat_all as $index => $data): ?>
                <?php
                $tanggalLahir = new DateTime($data['tanggal_lahir']);
                $sekarang = new DateTime();
                $umur = $sekarang->diff($tanggalLahir)->y;
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($data['nama_pasien']) ?></td>
                    <td><?= $umur ?> tahun</td>
                    <td><?= htmlspecialchars($data['alamat']) ?></td>
                    <td><?= $data['gdp'] ?></td>
                    <td><?= $data['tekanan_darah'] ?></td>
                    <td><?= $data['berat'] ?></td>
                    <td><?= $data['tinggi'] ?></td>
                    <td><?= number_format($data['imt'], 2) ?></td>
                    <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                    <td><?= formatTanggalIndoLengkap($data['created_at'], $bulanIndo) ?></td>
                    <td><?= $data['nama_petugas'] ?? '<span class="text-muted">Tidak Diketahui</span>' ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($riwayat_all)): ?>
                <tr>
                    <td colspan="11" class="text-center">Belum ada data riwayat.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    $jam = date('H:i:s');
    echo "<p>Dicetak pada {$tgl} {$bln} {$thn} Pukul {$jam}</p>";
    ?>

</body>

</html>