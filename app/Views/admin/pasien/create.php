<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Tambah Pasien Baru</h2>
    </div>

    <form action="/admin/pasien/store" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?= (session('errors.nama')) ? 'is-invalid' : '' ?>" id="nama" name="nama" value="<?= old('nama') ?>" required>
                    <?php if (session('errors.nama')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.nama') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control <?= (session('errors.tanggal_lahir')) ? 'is-invalid' : '' ?>" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir') ?>" required>
                    <?php if (session('errors.tanggal_lahir')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.tanggal_lahir') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control <?= (session('errors.alamat')) ? 'is-invalid' : '' ?>" id="alamat" name="alamat" rows="3" required><?= old('alamat') ?></textarea>
            <?php if (session('errors.alamat')) : ?>
                <div class="invalid-feedback">
                    <?= session('errors.alamat') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select <?= (session('errors.jenis_kelamin')) ? 'is-invalid' : '' ?>" required>
                        <option value="" <?= (old('jenis_kelamin') == '') ? 'selected' : '' ?>>-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" <?= (old('jenis_kelamin') == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= (old('jenis_kelamin') == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                    <?php if (session('errors.jenis_kelamin')) : ?>
                        <div class="invalid-feedback">
                            <?= session('errors.jenis_kelamin') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end">
            <a href="/admin/pasien" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button type="submit" class="btn btn-primary"> <i class="fas fa-save me-2"></i>Simpan
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
        // Logika untuk menampilkan modal sukses dari Bootstrap 5
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