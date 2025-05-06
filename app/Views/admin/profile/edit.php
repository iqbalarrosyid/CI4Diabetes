<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('content') ?>
<div class="container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2>Edit Profil</h2>

    <form method="post" action="<?= site_url('profile/update') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" name="nama" value="<?= esc($user['nama']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?= esc($user['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (kosongkan jika tidak ingin mengubah)</label>
            <input type="password" class="form-control" name="password" placeholder="Masukkan password baru jika ingin mengubah">
        </div>


        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="/admin/pasien" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<?= $this->endSection() ?>