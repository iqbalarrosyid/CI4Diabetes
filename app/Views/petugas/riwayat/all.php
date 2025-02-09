<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="container">
    <h2 class="mb-4">Riwayat Terbaru Semua Pasien</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pasien</th>
                    <th>Umur</th>
                    <th>GDP</th>
                    <th>Tekanan Darah</th>
                    <th>Berat (kg)</th>
                    <th>Tinggi (cm)</th>
                    <th>IMT</th>
                    <th>Hasil</th>
                    <th>Waktu</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($riwayat as $index => $data): ?>
                    <?php
                    // Hitung umur berdasarkan tanggal_lahir
                    $tanggalLahir = new DateTime($data['tanggal_lahir']);
                    $sekarang = new DateTime();
                    $umur = $sekarang->diff($tanggalLahir)->y;
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['nama_pasien'] ?></td>
                        <td><?= $umur ?> tahun</td>
                        <td><?= $data['gdp'] ?></td>
                        <td><?= $data['tekanan_darah'] ?></td>
                        <td><?= $data['berat'] ?></td>
                        <td><?= $data['tinggi'] ?></td>
                        <td><?= number_format($data['imt'], 2) ?></td>
                        <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                        <td><?= $data['created_at'] ?></td>
                        <td><?= $data['nama_petugas'] ?? 'Tidak Diketahui' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($riwayat)): ?>
                    <tr>
                        <td colspan="11" class="text-center">Belum ada data riwayat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="/pasien" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

<?= $this->endSection() ?>