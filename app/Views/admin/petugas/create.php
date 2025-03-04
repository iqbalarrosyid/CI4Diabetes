<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('content') ?>
<h2>Tambah Petugas Baru</h2>

<form action="/admin/petugas/store" method="post">
    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama" name="nama" required>
    </div>
    <div class="mb-3">
        <label for="nama" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="nama" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="/admin/petugas" class="btn btn-secondary">Kembali</a>
</form>
<?= $this->endSection() ?>