<?= $this->extend('layout/templateAdmin') // Pastikan ini adalah layout yang benar untuk admin 
?>

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
        /* box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075); */
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
        <h2 class="mb-0">Dashboard Admin</h2>
        <span class="text-muted" id="currentDateTimeAdmin"></span>
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
                        <i class="fas fa-user-plus me-2"></i>Tambah Pasien (Admin)
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
                    <?php if (!empty($petugasBaru) && is_array($petugasBaru)): ?>
                        <?php foreach ($petugasBaru as $petugas): ?>
                            <a href="<?= base_url('admin/petugas/edit/' . esc($petugas['id'], 'url')) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= esc($petugas['nama']) ?></div>
                                    <small class="text-muted">Username: <?= esc($petugas['username']) ?> | Ditambahkan: <?= esc(date('d M Y', strtotime($petugas['created_at']))) ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Baru</span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
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
                    <?php if (!empty($pasienBaru) && is_array($pasienBaru)): ?>
                        <?php foreach ($pasienBaru as $pasien): ?>
                            <a href="<?= base_url('admin/pasien/edit/' . esc($pasien['id'], 'url')) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?= esc($pasien['nama']) ?></div>
                                    <small class="text-muted">Ditambahkan: <?= esc(date('d M Y', strtotime($pasien['created_at']))) ?></small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Baru</span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="list-group-item">Tidak ada pasien baru.</p>
                    <?php endif; ?>
                    <a href="<?= base_url('admin/pasien?sort=newest') ?>" class="list-group-item list-group-item-action text-center text-primary fw-bold">Lihat semua...</a>
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
                <form action="/admin/import/upload" method="post" enctype="multipart/form-data">
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
    });
</script>
<?= $this->endSection() ?>