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
                    <th>GDP (mg/dL)</th>
                    <th>Tekanan Darah (mmHg)</th>
                    <th>Berat (kg)</th>
                    <th>Tinggi (cm)</th>
                    <th>IMT (kg/cm2)</th>
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
                        <td><?= esc($data['berat']) ?></td>
                        <td><?= esc($data['tinggi']) ?></td>
                        <td><?= number_format($data['imt'], 2) ?></td>
                        <td><?php if ($data['hasil'] == 1): ?>
                                <span class="badge bg-danger">Diabetes</span>
                            <?php elseif ($data['hasil'] == 0 && $data['hasil'] !== null && $data['hasil'] !== ''): ?>
                                <span class="badge bg-success">Tidak Diabetes</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d-m-Y H:i:s', strtotime($data['created_at'])) ?></td>
                        <td><?= esc($data['nama_petugas'] ?? 'Tidak Diketahui') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted justify-text px-2">
            <i>(*) Jika sebelumnya hasil prediksi menunjukan pasien Diabetes, kemudian prediksi berubah menjadi Tidak Diabetes, ini menunjukkan bahwa pasien dapat mengendalikan kadar gula darahnya. Bukan berarti sembuh dari diabetes.</i>
        </p>

    </div>

    <!-- Tombol sejajar -->
    <div class="d-flex justify-content-end">
        <a href="/petugas/pasien" class="btn btn-outline-secondary me-2">Kembali</a>
        <button class="btn btn-primary me-2" onclick="toggleChart()"><i class="fa-solid fa-square-poll-vertical"></i></button>
        <a href="<?= base_url('petugas/riwayat/exportPdf/' . $pasien['id']) ?>" target="_blank" class="btn btn-danger me-2">
            <i class="fa-solid fa-file-pdf"></i>
        </a>
        <a href="<?= base_url('petugas/riwayat/exportExcel/' . $pasien['id']) ?>" class="btn btn-success">
            <i class="fa-solid fa-file-excel"></i>
        </a>
    </div>

    <!-- Grafik -->
    <div id="chartContainer" class="mt-4" style="display: none;">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Grafik Riwayat Pemeriksaan</h5>
                <small class="text-muted">(Menampilkan maksimal 20 data terakhir)</small>
            </div>
            <div class="card-body">
                <div style="position: relative; height:400px; width:100%; max-width:850px; margin:auto;">
                    <canvas id="riwayatChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="mx-auto mb-3" style="font-size: 40px; color: #198754;">
                <i class="fa-solid fa-check-circle fa-beat"></i>
            </div>
            <h5 class="modal-title mb-2" id="successModalLabel">Data prediksi berhasil disimpan.</h5>
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

    $(document).ready(function() {
        <?php if (session()->getFlashdata('success')): ?>
            $('#successModal').modal('show');
            setTimeout(() => {
                $('#successModal').modal('hide');
            }, 3000); // hilang dalam 3 detik
        <?php endif; ?>
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Data untuk Grafik
    let labels = [];
    let gdpData = [];
    let tekananDarahData = [];
    let imtData = [];

    <?php
    // Ambil maksimal 20 data terakhir dan urutkan dari yang terlama ke terbaru untuk grafik
    $riwayatGrafik = array_slice($riwayat, -20);
    // Jika ingin urutan di grafik dari kiri (lama) ke kanan (baru), data $riwayat perlu di-sort ascending by created_at
    // Jika $riwayat sudah descending (terbaru dulu), kita perlu reverse array untuk grafik agar waktu berjalan dari kiri ke kanan
    // $riwayatGrafik = array_reverse($riwayatGrafik); // Uncomment jika $riwayat awalnya descending

    foreach ($riwayatGrafik as $data) :
    ?>
        labels.push("<?= htmlspecialchars(date('d M Y H:i', strtotime($data['created_at'])), ENT_QUOTES, 'UTF-8') ?>");
        // Pastikan data numerik adalah angka atau null
        gdpData.push(<?= filter_var($data['gdp'], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) ?? 'null' ?>);
        tekananDarahData.push(<?= filter_var($data['tekanan_darah'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 'null' ?>);
        imtData.push(<?= filter_var($data['imt'], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) ?? 'null' ?>);
    <?php endforeach; ?>

    let chartInstance = null;

    // Fungsi toggleChart tetap sama dari kode Anda
    function toggleChart() {
        let chartContainer = document.getElementById("chartContainer");
        if (chartContainer.style.display === "none" || !chartContainer.style.display) { // Cek juga jika display belum diset
            chartContainer.style.display = "block";
            renderChart();
        } else {
            chartContainer.style.display = "none";
        }
    }

    function renderChart() {
        let ctx = document.getElementById('riwayatChart').getContext('2d');

        if (chartInstance) {
            chartInstance.destroy();
        }

        // Palet warna yang lebih menarik
        const colors = {
            gdp: {
                border: 'rgb(54, 162, 235)', // Biru
                bg: 'rgba(54, 162, 235, 0.2)'
            },
            tekananDarah: {
                border: 'rgb(255, 99, 132)', // Merah muda
                bg: 'rgba(255, 99, 132, 0.2)'
            },
            imt: {
                border: 'rgb(75, 192, 192)', // Teal
                bg: 'rgba(75, 192, 192, 0.2)'
            }
        };

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'GDP (mg/dL)',
                        data: gdpData,
                        borderColor: colors.gdp.border,
                        backgroundColor: colors.gdp.bg,
                        fill: true,
                        tension: 0.3, // Garis sedikit melengkung
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: colors.gdp.border,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 2,
                        pointStyle: 'rectRounded'
                    },
                    {
                        label: 'Tekanan Darah (mmHg)',
                        data: tekananDarahData,
                        borderColor: colors.tekananDarah.border,
                        backgroundColor: colors.tekananDarah.bg,
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: colors.tekananDarah.border,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 2,
                        pointStyle: 'circle'
                    },
                    {
                        label: 'IMT (kg/m²)',
                        data: imtData,
                        borderColor: colors.imt.border,
                        backgroundColor: colors.imt.bg,
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: colors.imt.border,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 2,
                        pointStyle: 'triangle'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index', // Menampilkan tooltip untuk semua dataset pada index waktu yang sama
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true, // Menggunakan style titik sebagai ikon legenda
                            padding: 20,
                            font: {
                                size: 13,
                                family: 'Poppins' // Sesuaikan dengan font utama
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: {
                            size: 14,
                            weight: '600',
                            family: 'Poppins'
                        },
                        bodyFont: {
                            size: 12,
                            family: 'Poppins'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 3,
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label; // Judul tooltip dari label waktu
                            },
                            label: function(tooltipItem) {
                                let label = tooltipItem.dataset.label || '';
                                if (label) {
                                    label = label.split('(')[0].trim() + ': '; // Ambil nama metrik saja
                                }
                                if (tooltipItem.parsed.y !== null) {
                                    label += tooltipItem.parsed.y;
                                }
                                return label;
                            }
                        }
                    },
                    decimation: { // Plugin ini sudah ada, biarkan
                        enabled: true,
                        algorithm: 'min-max'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Waktu Pemeriksaan',
                            font: {
                                size: 14,
                                weight: '500',
                                family: 'Poppins'
                            },
                            padding: {
                                top: 10
                            }
                        },
                        grid: {
                            drawOnChartArea: false, // Sembunyikan grid vertikal untuk tampilan lebih bersih
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: 'Poppins'
                            },
                            maxRotation: 45, // Rotasi label jika terlalu panjang
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nilai',
                            font: {
                                size: 14,
                                weight: '500',
                                family: 'Poppins'
                            },
                            padding: {
                                bottom: 10
                            }
                        },
                        beginAtZero: false, // Sumbu Y bisa dimulai dari nilai terdekat data, bukan 0
                        grid: {
                            color: 'rgba(0, 0, 0, 0.08)', // Warna grid horizontal lebih halus
                            borderDash: [3, 4], // Garis putus-putus
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: 'Poppins'
                            },
                            padding: 8
                        }
                    }
                }
            }
        });
    }

    // Pemanggilan modal sukses (jika ada dari PHP)
    $(document).ready(function() {
        <?php if (session()->getFlashdata('success')): ?>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            setTimeout(() => {
                if (bootstrap.Modal.getInstance(document.getElementById('successModal'))) {
                    successModal.hide();
                }
            }, 3000);
        <?php endif; ?>
    });
</script>

<?= $this->endSection() ?>