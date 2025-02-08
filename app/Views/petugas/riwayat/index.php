<?php if (!session()->get('logged_in')) {
    return redirect()->to('/login');
} ?>
<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3>Riwayat Pasien: <?= $pasien['nama']; ?></h3>
        <a href="/riwayat/create/<?= $pasien['id'] ?>" class="btn btn-success mb-3">+ Riwayat</a>
    </div>
    <?php
    // Hitung umur secara otomatis
    $tanggalLahir = new DateTime($pasien['tanggal_lahir']);
    $sekarang = new DateTime();
    $umur = $sekarang->diff($tanggalLahir);
    ?>
    <p>Tanggal Lahir: <?= $tanggalLahir->format('d-m-Y'); ?> / <?= $umur->y; ?> tahun</p>
    <p>Jenis Kelamin: <?= $pasien['jenis_kelamin']; ?></p>
    <p>Alamat: <?= $pasien['alamat']; ?></p>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>GDP</th>
                    <th>Tekanan Darah</th>
                    <th>Berat</th>
                    <th>Tinggi</th>
                    <th>IMT</th>
                    <th>Hasil</th>
                    <th>Waktu</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($riwayat as $index => $data): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['gdp'] ?></td>
                        <td><?= $data['tekanan_darah'] ?></td>
                        <td><?= $data['berat'] ?> kg</td>
                        <td><?= $data['tinggi'] ?> cm</td>
                        <td><?= number_format($data['imt'], 2) ?></td>
                        <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                        <td><?= $data['created_at'] ?></td>
                        <td><?= $data['nama_petugas'] ?? 'Tidak Diketahui' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($riwayat)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Belum ada data riwayat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="/pasien" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<?= $this->endSection() ?>