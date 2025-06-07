<?= $this->extend('layout/template') ?>

<?= $this->section('pageStyles') ?>
<style>
    /* Gaya CSS tetap sama seperti yang Anda berikan di versi terakhir */
    .card-icon {
        font-size: 2.5rem;
        opacity: 0.3;
        /* Anda bisa sesuaikan opacity jika ikon kurang terlihat */
    }

    .dashboard-card {
        transition: transform .2s;
        border: none;
        background-color: #fff;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .dashboard-card .card-header {
        border-bottom: none;
        padding-top: 0.75rem;
        padding-bottom: 0.5rem;
        font-weight: 600;
    }

    .dashboard-card .card-body .card-title {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .dashboard-card .card-body .fw-bold {
        color: #212529;
    }

    .dashboard-card .card-footer {
        background-color: #f8f9fa;
        text-decoration: none;
    }

    .dashboard-card .card-footer span,
    .dashboard-card .card-footer .fas {
        color: #007bff;
    }

    .dashboard-card .card-footer:hover span,
    .dashboard-card .card-footer:hover .fas {
        color: #0056b3;
    }

    /* Style lain dari kode Anda sebelumnya */
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
                            <h5 class="card-title">Pasien Belum Ada Riwayat</h5>
                            <h3 class="fw-bold"><?= esc($pasienBelumAdaRiwayat ?? 0) ?></h3>
                        </div>
                        <i class="fas fa-user-plus card-icon"></i>
                    </div>
                </div>
                <a href="<?= base_url('petugas/pasien?filter=belum_ada_riwayat') ?>" class="card-footer text-decoration-none d-flex justify-content-between border-0">
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
                            <a href="<?= base_url('petugas/riwayat/pasien/' . esc($pasien['id'], 'url')) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= esc($pasien['nama']) ?></div>
                                    <small class="text-muted">Ditambahkan: <?= esc(date('d M Y', strtotime($pasien['created_at'] ?? $pasien['id']))) ?></small>
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
                            <a href="<?= base_url('petugas/riwayat/pasien/' . esc($prediksi['pasien_id'], 'url') . '#riwayat-' . esc($prediksi['id'], 'attr')) ?>" class="list-group-item list-group-item-action">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-import me-2"></i>Import Data Pasien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/petugas/import/upload" method="post" enctype="multipart/form-data" id="importForm">
                    <div class="mb-4">
                        <label for="excel" class="form-label fw-bold">Pilih File Excel</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="excel" accept=".xlsx,.xls" required class="form-control" id="excel">
                            <div class="form-text">Format file harus .xlsx atau .xls</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="fas fa-upload me-2"></i> Upload & Import
                    </button>
                </form>
                <hr class="my-4">
                <div class="alert alert-info">
                    <h6 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i>Panduan Import</h6>
                    <ul class="mb-0 ps-3">
                        <li>Gunakan template kami untuk memastikan format benar</li>
                        <li>Kolom wajib: Nama, Alamat, Tanggal Lahir, Jenis Kelamin</li>
                        <li>Baris pertama akan diabaikan (header)</li>
                    </ul>
                </div>
                <a href="<?= base_url('template/template_import_pasien.xlsx') ?>" class="btn btn-outline-secondary w-100 mt-2" download>
                    <i class="fas fa-file-excel me-2"></i> Unduh Template
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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