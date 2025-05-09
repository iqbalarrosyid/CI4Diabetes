<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('content') ?>
<div class="container">

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

    <!-- Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="mx-auto mb-3" style="font-size: 40px; color: #198754;">
                    <i class="fa-solid fa-check-circle fa-beat"></i>
                </div>
                <h5 class="modal-title mb-2" id="successModalLabel">Profil berhasil diperbarui.</h5>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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

<?= $this->endSection() ?>