<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= esc($title) ?></h2>
    </div>

    <form action="<?= site_url('/admin/petugas/update/' . esc($petugas['id'], 'attr')) ?>" method="post">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?= (session('errors.nama')) ? 'is-invalid' : '' ?>" id="nama" name="nama" value="<?= old('nama', esc($petugas['nama'])) ?>" required>
                    <?php if (session('errors.nama')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.nama') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control <?= (session('errors.username')) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username', esc($petugas['username'])) ?>" required>
                    <?php if (session('errors.username')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.username') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" class="form-control <?= (session('errors.password')) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
            <div class="form-text">Masukkan password baru jika Anda ingin mengubahnya.</div>
            <?php if (session('errors.password')) : ?>
                <div class="invalid-feedback">
                    <?= session('errors.password') ?>
                </div>
            <?php endif; ?>
        </div>


        <hr class="my-4">

        <div class="d-flex justify-content-end">
            <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-save me-2"></i>Simpan
            </button>
        </div>
    </form>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="mx-auto mb-3" style="font-size: 40px; color: #198754;">
                        <i class="fa-solid fa-check-circle fa-beat"></i>
                    </div>
                    <h5 class="modal-title mb-2" id="successModalLabel"><?= session()->getFlashdata('success') ?></h5>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk menampilkan modal sukses dari Bootstrap 5 (vanilla JS)
        <?php if (session()->getFlashdata('success')) : ?>
            var successModalElement = document.getElementById('successModal');
            if (successModalElement) {
                var successModal = new bootstrap.Modal(successModalElement);
                successModal.show();
                setTimeout(function() {
                    // Pastikan modal masih ada sebelum mencoba menyembunyikannya
                    if (bootstrap.Modal.getInstance(successModalElement)) {
                        successModal.hide();
                    }
                }, 3000); // Hilang setelah 3 detik
            }
        <?php endif; ?>
    });
</script>

<?= $this->endSection() ?>