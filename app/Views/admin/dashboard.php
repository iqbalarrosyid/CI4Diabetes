<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('pageStyles') ?>
<style>
    /* Gaya CSS tetap sama seperti yang Anda berikan sebelumnya */
    .card-icon {
        font-size: 2.5rem;
        opacity: 0.3;
    }

    .dashboard-card {
        transition: transform .2s;
        border: none;
        background-color: #fff;
        margin-bottom: 1.5rem;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .dashboard-card .card-header {
        background-color: transparent;
        border-bottom: none;
        padding-top: 0.75rem;
        padding-bottom: 0.5rem;
        font-weight: 600;
        color: #495057;
    }

    .dashboard-card .card-footer {
        background-color: rgba(0, 0, 0, 0.03);
        border-top: none;
    }

    .dashboard-card.text-white .card-footer {
        background-color: rgba(0, 0, 0, 0.15);
    }

    .quick-link-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    .quick-link-card i.fas {
        font-size: 1.2rem;
    }

    .quick-link-card .btn {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .list-group-flush .list-group-item {
        border-top: none;
        border-bottom: none;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .list-group {
        border: none;
    }

    .list-group-flush .list-group-item.text-center {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        background-color: #f8f9fa;
    }

    .list-group-flush .list-group-item-action:hover,
    .list-group-flush .list-group-item-action:focus {
        background-color: #e9ecef;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mb-3">
            <h2 class="mb-0">Dashboard</h2>
            <small class="text-muted" id="currentDateTimeAdmin"></small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center bg-light border rounded-pill px-3 py-1">
                <i class="fa-solid fa-user-shield me-2 text-success"></i>
                <span class="fw-bold" style="font-size: 0.9rem;"><?= esc(session()->get('nama')) ?></span>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card dashboard-card border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Petugas</h5>
                            <h3 class="fw-bold"><?= esc($totalPetugas ?? 0) ?></h3>
                        </div>
                        <i class="fas fa-user-shield card-icon"></i>
                    </div>
                </div>
                <a href="<?= base_url('admin/petugas') ?>" class="card-footer text-decoration-none border-0 d-flex justify-content-between">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="card dashboard-card border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Pasien</h5>
                            <h3 class="fw-bold"><?= esc($totalPasien ?? 0) ?></h3>
                        </div>
                        <i class="fas fa-users card-icon"></i>
                    </div>
                </div>
                <a href="<?= base_url('admin/pasien') ?>" class="card-footer text-decoration-none border-0 d-flex justify-content-between">
                    <span>Lihat Detail</span>
                    <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card dashboard-card quick-link-card border-0 shadow">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-cogs me-1"></i>
                    Manajemen Sistem
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="<?= base_url('admin/petugas/create') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user-shield me-2"></i>Tambah Petugas Baru
                    </a>
                    <a href="<?= base_url('admin/pasien/create') ?>" class="btn btn-outline-info">
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
                    <i class="fas fa-user-plus me-1"></i>
                    Petugas Baru Ditambahkan
                </div>
                <div class="list-group list-group-flush" id="listPetugasBaru">
                    <?php if (!empty($petugasBaru) && is_array($petugasBaru)) : ?>
                        <?php foreach ($petugasBaru as $petugas) : ?>
                            <a href="<?= base_url('admin/petugas/edit/' . esc($petugas['id'], 'url')) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= esc($petugas['nama']) ?></div>
                                    <small class="text-muted">Username: <?= esc($petugas['username']) ?> | Ditambahkan: <?= esc(date('d M Y', strtotime($petugas['created_at']))) ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Baru</span>
                            </a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="list-group-item">Tidak ada petugas baru.</p>
                    <?php endif; ?>
                    <a href="<?= base_url('admin/petugas?sort=newest') ?>" class="list-group-item list-group-item-action text-center text-primary fw-bold">Lihat semua...</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card dashboard-card quick-link-card border-0 shadow">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-user-clock me-1"></i>
                    Pasien Baru Ditambahkan
                </div>
                <div class="list-group list-group-flush" id="listPasienBaruAdmin">
                    <?php if (!empty($pasienBaru) && is_array($pasienBaru)) : ?>
                        <?php foreach ($pasienBaru as $pasien) : ?>
                            <a href="<?= base_url('admin/pasien/edit/' . esc($pasien['id'], 'url')) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= esc($pasien['nama']) ?></div>
                                    <small class="text-muted">Ditambahkan: <?= esc(date('d M Y', strtotime($pasien['created_at']))) ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Baru</span>
                            </a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="list-group-item">Tidak ada pasien baru.</p>
                    <?php endif; ?>
                    <a href="<?= base_url('admin/pasien?sort=newest') ?>" class="list-group-item list-group-item-action text-center text-primary fw-bold">Lihat semua...</a>
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
                <form action="/admin/import/upload" method="post" enctype="multipart/form-data" id="importForm">
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

<?php if (session()->getFlashdata('successLogin')) : ?>
    <div class="modal fade" id="successModalLogin" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="mx-auto mb-3" style="font-size: 40px; color: #198754;">
                    <i class="fa-solid fa-check-circle fa-beat"></i>
                </div>
                <h5 class="modal-title mb-2" id="successModalLabel"><?= session()->getFlashdata('successLogin') ?></h5>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {

        // --- Fungsi untuk update tanggal dan waktu ---
        function updateDateTimeAdmin() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const currentDateTimeEl = document.getElementById('currentDateTimeAdmin');
            if (currentDateTimeEl) {
                currentDateTimeEl.textContent = now.toLocaleDateString('id-ID', options);
            }
        }
        updateDateTimeAdmin();
        setInterval(updateDateTimeAdmin, 60000);


        <?php if (session()->getFlashdata('successLogin')) : ?>
            $('#successModalLogin').modal('show');

            // Atur timer untuk menyembunyikan modal setelah 3 detik
            setTimeout(() => {
                $('#successModalLogin').modal('hide');
            }, 3000);
        <?php endif; ?>

    });
</script>
<?= $this->endSection() ?>