<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('content') ?>
<div class="container">
    <h2><?= $title ?></h2>

    <form action="/admin/petugas/update/<?= $petugas['id'] ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" name="nama" value="<?= esc($petugas['nama']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?= esc($petugas['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (kosongkan jika tidak ingin mengubah)</label>
            <input type="password" class="form-control" name="password" placeholder="Masukkan password baru jika ingin mengubah">
        </div>


        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="/admin/petugas" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?= $this->endSection() ?>