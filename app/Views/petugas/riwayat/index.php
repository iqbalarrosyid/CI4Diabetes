<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<!-- Bootstrap DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<style>
    .justify-text {
        text-align: justify;
    }
</style>


<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Riwayat Pasien: <?= esc($pasien['nama']) ?></h2>
        <a href="/petugas/riwayat/create/<?= esc($pasien['id']) ?>" class="btn btn-success"><i class="fa-solid fa-plus"></i></a>
    </div>

    <?php
    // Hitung umur secara otomatis
    $tanggalLahir = new DateTime($pasien['tanggal_lahir']);
    $sekarang = new DateTime();
    $umur = $sekarang->diff($tanggalLahir);
    ?>
    <p>Tanggal Lahir: <?= $tanggalLahir->format('d-m-Y'); ?> / <?= $umur->y; ?> tahun</p>
    <p>Jenis Kelamin: <?= esc($pasien['jenis_kelamin']); ?></p>
    <p>Alamat: <?= esc($pasien['alamat']); ?></p>

    <!-- Dropdown "Tampilkan _MENU_ data" sekarang ada di bawah form cari -->
    <div class="d-flex justify-content-start mb-2">
        <div id="riwayatTable_length"></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="riwayatTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>GDP</th>
                    <th>Tekanan Darah</th>
                    <th>Berat</th>
                    <th>Tinggi</th>
                    <th>IMT</th>
                    <th>Hasil*</th>
                    <th>Waktu</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($riwayat as $data) : ?>
                    <tr>
                        <td></td>
                        <td><?= esc($data['gdp']) ?></td>
                        <td><?= esc($data['tekanan_darah']) ?></td>
                        <td><?= esc($data['berat']) ?> kg</td>
                        <td><?= esc($data['tinggi']) ?> cm</td>
                        <td><?= number_format($data['imt'], 2) ?></td>
                        <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                        <td><?= date('d-m-Y H:i:s', strtotime($data['created_at'])) ?></td>
                        <td><?= esc($data['nama_petugas'] ?? 'Tidak Diketahui') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted justify-text">
            <i>(*) Jika sebelumnya hasil prediksi menunjukan pasien Diabetes, kemudian prediksi berubah menjadi Tidak Diabetes, ini menunjukkan bahwa pasien dapat mengendalikan kadar gula darahnya. Bukan berarti sembuh dari diabetes.</i>
        </p>

    </div>

    <!-- Tombol sejajar -->
    <div class="d-flex gap-2 mt-2">
        <a href="/petugas/pasien" class="btn btn-secondary">Kembali</a>
        <button class="btn btn-primary" onclick="toggleChart()"><i class="fa-solid fa-square-poll-vertical"></i></button>
        <a href="<?= base_url('petugas/riwayat/exportPdf/' . $pasien['id']) ?>" target="_blank" class="btn btn-danger">
            <i class="fa-solid fa-file-pdf"></i>
        </a>
        <a href="<?= base_url('petugas/riwayat/exportExcel/' . $pasien['id']) ?>" class="btn btn-success">
            <i class="fa-solid fa-file-excel"></i>
        </a>
    </div>

    <!-- Grafik -->
    <div id="chartContainer" class="mt-4" style="display: none;">
        <h4>Grafik Riwayat Pasien</h4>
        <div style="width: 100%; max-width: 800px; height: 350px;">
            <canvas id="riwayatChart"></canvas>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let table = $('#riwayatTable').DataTable({
            "ordering": true, // Aktifkan sorting dengan ikon sort
            "searching": false, // Matikan pencarian
            "paging": true, // Aktifkan pagination
            "lengthChange": true, // Dropdown jumlah data
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
                "targets": 0 // Nomor tidak bisa diurutkan
            }],
            "order": [
                [7, 'desc']
            ], // Urutkan berdasarkan waktu (kolom ke-8)
            "rowCallback": function(row, data, index) {
                let api = this.api();
                let info = api.page.info();
                let globalIndex = index + 1 + info.start;
                $('td:eq(0)', row).html(globalIndex); // Nomor urut otomatis
            }
        });
    });

    function toggleChart() {
        $("#chartContainer").toggle();
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk Grafik
    let labels = [];
    let gdpData = [];
    let tekananDarahData = [];
    let imtData = [];

    <?php
    // Ambil maksimal 20 data terakhir
    $riwayatTerbatas = array_slice($riwayat, -20);
    foreach ($riwayatTerbatas as $data) :
    ?>
        labels.push("<?= date('d M Y H:i', strtotime($data['created_at'])) ?>");
        gdpData.push(<?= $data['gdp'] ?>);
        tekananDarahData.push("<?= $data['tekanan_darah'] ?>");
        imtData.push(<?= $data['imt'] ?>);
    <?php endforeach; ?>

    let chartInstance = null;

    function toggleChart() {
        let chartContainer = document.getElementById("chartContainer");
        if (chartContainer.style.display === "none") {
            chartContainer.style.display = "block";
            renderChart();
        } else {
            chartContainer.style.display = "none";
        }
    }

    function renderChart() {
        let ctx = document.getElementById('riwayatChart').getContext('2d');

        // Hapus chart lama jika ada
        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'GDP',
                        data: gdpData,
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 0, 0, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Tekanan Darah',
                        data: tekananDarahData,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 0, 255, 0.2)',
                        fill: true
                    },
                    {
                        label: 'IMT',
                        data: imtData,
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 255, 0, 0.2)',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    decimation: {
                        enabled: true,
                        algorithm: 'min-max' // Mengoptimalkan rendering data
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Waktu Pemeriksaan'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nilai'
                        }
                    }
                }
            }
        });
    }
</script>

<?= $this->endSection() ?>