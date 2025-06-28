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

    <?php
    // Array bulan Indonesia
    $bulanIndo = [
        '01' => 'Jan',
        '02' => 'Feb',
        '03' => 'Mar',
        '04' => 'Apr',
        '05' => 'Mei',
        '06' => 'Jun',
        '07' => 'Jul',
        '08' => 'Ags',
        '09' => 'Sep',
        '10' => 'Okt',
        '11' => 'Nov',
        '12' => 'Des',
    ];

    // Fungsi format tanggal
    function formatTanggalIndoLengkap($timestamp, $bulanIndo)
    {
        $tanggal = date('d', strtotime($timestamp));
        $bulan = $bulanIndo[date('m', strtotime($timestamp))];
        $tahun = date('Y', strtotime($timestamp));
        $jam = date('H:i:s', strtotime($timestamp));
        return "$tanggal $bulan $tahun $jam";
    }
    ?>

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
                        <td><?php if ($data['hasil'] == 1): ?>
                                <span class="badge bg-danger">Diabetes</span>
                            <?php elseif ($data['hasil'] == 0 && $data['hasil'] !== null && $data['hasil'] !== ''): ?>
                                <span class="badge bg-success">Tidak Diabetes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc(formatTanggalIndoLengkap($data['created_at'], $bulanIndo)) ?></td>
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

    <div class="d-flex justify-content-end mt-2">
        <a href="/petugas" class="btn btn-outline-secondary me-2"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        <div class="btn-group me-2">
            <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Pilih Jenis Export PDF">
                <i class="fa-solid fa-file-pdf"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="<?= base_url('/petugas/riwayat-terbaru/all/pdf') ?>" target="_blank">
                        Riwayat Terbaru Saja
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('/petugas/riwayat-historis/all/pdf') ?>" target="_blank">
                        Seluruh Riwayat (Semua Pasien)
                    </a>
                </li>
            </ul>
        </div>

        <div class="btn-group me-2">
            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Pilih Jenis Export Excel">
                <i class="fa-solid fa-file-excel"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="<?= base_url('/petugas/riwayat-terbaru/all/excel') ?>" target="_blank">
                        Riwayat Terbaru Saja
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('/petugas/riwayat-historis/all/excel') ?>" target="_blank">
                        Seluruh Riwayat (Semua Pasien)
                    </a>
                </li>
            </ul>
        </div>
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