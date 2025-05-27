<?= $this->extend('layout/template') ?>

<?= $this->section('pageStyles') ?>
<style>
    .card-icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }

    .dashboard-card {
        transition: transform .2s;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .quick-link-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .quick-link-card i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .list-group-item {
        border-left: 0;
        border-right: 0;
    }

    .list-group-item:first-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .list-group-item:last-child {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    /* CSS .chart-container bisa dihapus jika tidak ada chart lain */
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Dashboard Petugas</h2>
        <span class="text-muted" id="currentDateTime"></span>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card h-100 border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Pasien</h5>
                            <h3 class="fw-bold"><?= esc($totalPasien ?? 0) ?></h3>
                        </div>
                        <i class="fas fa-users card-icon"></i>
                    </div>
                </div>
                <a href="<?= base_url('petugas/pasien') ?>" class="card-footer text-decoration-none d-flex justify-content-between border-0">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card h-100 border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Prediksi</h5>
                            <h3 class="fw-bold"><?= esc($totalPrediksi ?? 0) ?></h3>
                        </div>
                        <i class="fas fa-file-medical-alt card-icon"></i>
                    </div>
                </div>
                <a href="<?= base_url('petugas/riwayat/all') ?>" class="card-footer text-decoration-none d-flex justify-content-between border-0">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card h-100 border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Pasien Diabetes</h5>
                            <h3 class="fw-bold"><?= esc($pasienDiabetes ?? 0) ?></h3>
                        </div>
                        <i class="fas fa-procedures card-icon"></i>
                    </div>
                </div>
                <a href="<?= base_url('petugas/riwayat/all?status=diabetes') ?>" class="card-footer text-decoration-none d-flex justify-content-between border-0">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card dashboard-card h-100 border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Pasien Non-Diabetes</h5>
                            <h3 class="fw-bold"><?= esc($pasienNonDiabetes ?? 0) ?></h3>
                        </div>
                        <i class="fas fa-heartbeat card-icon"></i>
                    </div>
                </div>
                <a href="<?= base_url('petugas/riwayat/all?status=non-diabetes') ?>" class="card-footer text-decoration-none d-flex justify-content-between border-0">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card dashboard-card quick-link-card border-0 shadow">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-bolt me-1"></i>
                    Akses Cepat
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="<?= base_url('petugas/pasien/create') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Tambah Pasien Baru
                    </a>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-2"></i>Import Data Pasien
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card dashboard-card quick-link-card border-0 shadow">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-user-clock me-1"></i>
                    Pasien Baru Ditambahkan
                </div>
                <div class="list-group list-group-flush" id="listPasienBaru">
                    <?php if (!empty($pasienBaru) && is_array($pasienBaru)): ?>
                        <?php foreach ($pasienBaru as $pasien): ?>
                            <a href="<?= base_url('petugas/riwayat/' . esc($pasien['id'], 'url')) // Path ke riwayat per pasien 
                                        ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= esc($pasien['nama']) ?></div>
                                    <small class="text-muted">Ditambahkan: <?= esc(date('d M Y', strtotime($pasien['created_at'] ?? $pasien['id']))) // Fallback ke ID jika created_at pasien tidak ada 
                                                                            ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Baru</span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="list-group-item">Tidak ada pasien baru.</p>
                    <?php endif; ?>
                    <a href="<?= base_url('petugas/pasien?sort=newest') ?>" class="list-group-item list-group-item-action text-center text-primary fw-bold">Lihat semua...</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card dashboard-card quick-link-card border-0 shadow">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-file-signature me-1"></i>
                    Prediksi Terbaru Dilakukan
                </div>
                <div class="list-group list-group-flush" id="listPrediksiTerbaru">
                    <?php if (!empty($prediksiTerbaru) && is_array($prediksiTerbaru)): ?>
                        <?php foreach ($prediksiTerbaru as $prediksi): ?>
                            <a href="<?= base_url('petugas/riwayat/' . esc($prediksi['pasien_id'], 'url') . '#riwayat-' . esc($prediksi['id'], 'attr')) ?>" class="list-group-item list-group-item-action">
                                <div class="fw-bold">Prediksi untuk <?= esc($prediksi['nama_pasien'] ?? 'N/A') ?></div>
                                <small class="text-muted">
                                    Hasil: <span class="fw-bold <?= ($prediksi['hasil'] == 1 || strtolower($prediksi['hasil']) === 'diabetes') ? 'text-danger' : 'text-success' ?>">
                                        <?= ($prediksi['hasil'] == 1 || strtolower($prediksi['hasil']) === 'diabetes') ? 'Diabetes' : 'Tidak Diabetes' ?>
                                    </span>
                                    | Oleh: <?= esc($prediksi['nama_petugas_pemeriksa'] ?? 'Sistem') ?> | <?= esc(date('d M Y, H:i', strtotime($prediksi['created_at']))) ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="list-group-item">Tidak ada prediksi baru.</p>
                    <?php endif; ?>
                    <a href="<?= base_url('petugas/riwayat/all') ?>" class="list-group-item list-group-item-action text-center text-primary fw-bold">Lihat semua...</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Pasien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/petugas/import/upload" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel" class="form-label">Pilih File Excel (.xlsx, .xls)</label>
                        <input type="file" name="excel" accept=".xlsx,.xls" required class="form-control" id="excel">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-upload"></i> Upload</button>
                </form>
                <hr>
                <p class="text-muted small">
                    Pastikan format file Excel Anda sesuai dengan template yang dibutuhkan.
                    Kolom yang diharapkan: <strong>Nama</strong>, <strong>Alamat</strong>, <strong>Tanggal Lahir (YYYY-MM-DD)</strong>, <strong>Jenis Kelamin (Laki-laki/Perempuan)</strong>.
                    Baris pertama (header) akan diabaikan.
                </p>
                <a href="<?= base_url('template/template_import_pasien.xlsx') ?>" class="btn btn-sm btn-outline-secondary" download>
                    <i class="fas fa-file-excel me-1"></i> Unduh Template Excel
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menampilkan tanggal dan waktu saat ini
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const currentDateTimeEl = document.getElementById('currentDateTime');
            if (currentDateTimeEl) {
                currentDateTimeEl.textContent = now.toLocaleDateString('id-ID', options);
            }
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);
    });
</script>
<?= $this->endSection() ?>