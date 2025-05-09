<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container">

    <h2><?= $title ?></h2>

    <form action="/petugas/pasien/update/<?= $pasien['id'] ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" name="nama" value="<?= esc($pasien['nama']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" name="alamat" value="<?= esc($pasien['alamat']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" name="tanggal_lahir" value="<?= esc($pasien['tanggal_lahir']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" required>
                <option value="Laki-laki" <?= $pasien['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $pasien['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="/petugas/pasien" class="btn btn-secondary">Kembali</a>
    </form>

    <?php if (session()->getFlashdata('success')): ?>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            <?php if (session()->getFlashdata('success')): ?>
                $('#successModal').modal('show');
                setTimeout(() => {
                    $('#successModal').modal('hide');
                }, 3000); // hilang dalam 3 detik
            <?php endif; ?>
        });
    </script>

</div>
<?= $this->endSection() ?>