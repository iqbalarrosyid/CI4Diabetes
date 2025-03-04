<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<!-- Bootstrap DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<div class="container">
    <h2 class="mb-4">Riwayat Terbaru Semua Pasien</h2>

    <!-- Form Search -->
    <div class="mb-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Cari">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="riwayatTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Umur</th>
                    <th>Alamat</th>
                    <th>GDP (mg/dL)</th>
                    <th>Tekanan Darah (mmHg)</th>
                    <th>Berat (kg)</th>
                    <th>Tinggi (cm)</th>
                    <th>IMT (kg/cm2)</th>
                    <th>Hasil</th>
                    <th>Waktu</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($riwayat as $index => $data): ?>
                    <?php
                    $tanggalLahir = new DateTime($data['tanggal_lahir']);
                    $sekarang = new DateTime();
                    $umur = $sekarang->diff($tanggalLahir)->y;
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($data['nama_pasien']) ?></td>
                        <td><?= $umur ?> tahun</td>
                        <td><?= htmlspecialchars($data['alamat']) ?></td>
                        <td><?= $data['gdp'] ?></td>
                        <td><?= $data['tekanan_darah'] ?></td>
                        <td><?= $data['berat'] ?></td>
                        <td><?= $data['tinggi'] ?></td>
                        <td><?= number_format($data['imt'], 2) ?></td>
                        <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                        <td><?= date('d-m-Y H:i:s', strtotime($data['created_at'])) ?></td>
                        <td><?= $data['nama_petugas'] ?? '<span class="text-muted">Tidak Diketahui</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($riwayat)): ?>
                    <tr>
                        <td colspan="11" class="text-center">Belum ada data riwayat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex gap-2 mt-2">
        <a href="/petugas/pasien" class="btn btn-secondary">Kembali</a>
        <a href="<?= base_url('/petugas/riwayat/exportAllPdf') ?>" target="_blank" class="btn btn-danger">
            <i class="fa-solid fa-file-pdf"></i>
        </a>
        <a href="<?= base_url('/petugas/riwayat/exportAllExcel') ?>" class="btn btn-success">
            <i class="fa-solid fa-file-excel"></i>
        </a>
    </div>
</div>

<script>
    $(document).ready(function() {
        let table = $('#riwayatTable').DataTable({
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
    });
</script>

<?= $this->endSection() ?>