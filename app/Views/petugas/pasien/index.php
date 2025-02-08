<?php if (!session()->get('logged_in')) {
    return redirect()->to('/login');
} ?>

<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?= $title ?></h2>
        <a href="/pasien/create" class="btn btn-success">+ Pasien</a>
    </div>
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Umur</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pasien as $index => $p) : ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($p['nama']) ?></td>
                        <td><?= date_diff(date_create($p['tanggal_lahir']), date_create('today'))->y ?> tahun</td>
                        <td><?= esc($p['jenis_kelamin']) ?></td>
                        <td><?= esc($p['alamat']) ?></td>
                        <td>
                            <div class="d-flex flex-sm-row flex-column gap-2">
                                <a href="/riwayat/<?= $p['id'] ?>" class="btn btn-info btn-sm w-100">Riwayat</a>
                                <a href="/pasien/edit/<?= $p['id'] ?>" class="btn btn-warning btn-sm w-100">Edit</a>
                                <form action="/pasien/delete/<?= $p['id'] ?>" method="post" onsubmit="return confirm('Yakin ingin menghapus?');" style="width: 100%;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm w-100">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pasien)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Belum ada data riwayat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>