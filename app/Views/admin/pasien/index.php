<?= $this->extend('layout/templateAdmin') ?>

<?= $this->section('content') ?>

<!-- Bootstrap DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?= $title ?></h2>
        <div>
            <a href="/admin/pasien/create" class="btn btn-success me-2">
                <i class="fa-solid fa-user-plus"></i>
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa-solid fa-file-import"></i>
            </button>
        </div>
    </div>

    <!-- Import Section -->
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
                </div>
            </div>
        </div>
    </div>


    <!-- Form Search -->
    <div class="mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Cari">
    </div>

    <!-- Dropdown "Tampilkan _MENU_ data" sekarang ada di bawah form cari -->
    <div class="d-flex justify-content-start mb-2">
        <div id="pasienTable_length"></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pasienTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Umur</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th class="text-start" style="width: 150px; white-space: nowrap;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pasien as $p) : ?>
                    <tr>
                        <td></td>
                        <td><?= esc($p['nama']) ?></td>
                        <td><?= date_diff(date_create($p['tanggal_lahir']), date_create('today'))->y ?> tahun</td>
                        <td><?= esc($p['jenis_kelamin']) ?></td>
                        <td><?= esc($p['alamat']) ?></td>
                        <td class="text-start" style="white-space: nowrap;">
                            <a href="/admin/pasien/edit/<?= $p['id'] ?>" class="text-warning me-2"><i class="fas fa-edit fa-lg"></i></a>
                            <form action="/admin/pasien/delete/<?= $p['id'] ?>" method="post" class="delete-form" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="button" class="border-0 bg-transparent text-danger btn-delete">
                                    <i class="fas fa-trash fa-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="mx-auto mb-3" style="font-size: 40px; color: #dc3545;">
                <i class="fa-solid fa-trash fa-beat"></i>
            </div>
            <h5 class="modal-title mb-2" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger mt-2" id="confirmDelete">Hapus</button>
        </div>
    </div>
</div>

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

<script>
    $(document).ready(function() {
        let table = $('#pasienTable').DataTable({
            "ordering": true,
            "searching": true,
            "paging": true,
            "lengthChange": true, // Dropdown tetap ada
            "dom": "<'d-flex justify-content-between align-items-center mb-2'l><'table-responsive't><'d-flex justify-content-end mt-2'p>",
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "next": ">",
                    "previous": "<"
                }
            },
            "responsive": true,
            "columnDefs": [{
                "orderable": false,
                "targets": 0
            }],
            "order": [
                [1, 'asc']
            ],
            "rowCallback": function(row, data, index) {
                let api = this.api();
                let info = api.page.info();
                let globalIndex = index + 1 + info.start; // Urutkan tanpa lompat
                $('td:eq(0)', row).html(globalIndex);
            }
        });

        // Custom search hanya di kolom Nama (kolom ke-1, indeks 1)
        $('#searchBox').on('keyup', function() {
            table.column(1).search(this.value).draw();
        });

        // Modal delete
        let formToSubmit = null;

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            formToSubmit = $(this).closest('form');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').on('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });

        <?php if (session()->getFlashdata('success')) : ?>
            $('#successModal').modal('show');
            setTimeout(() => {
                $('#successModal').modal('hide');
            }, 3000); // hilang dalam 3 detik
        <?php endif; ?>

        <?php if (session()->getFlashdata('successLogin')) : ?>
            $('#successModalLogin').modal('show');
            setTimeout(() => {
                $('#successModalLogin').modal('hide');
            }, 3000); // hilang dalam 3 detik
        <?php endif; ?>
    });
</script>


<?= $this->endSection() ?>