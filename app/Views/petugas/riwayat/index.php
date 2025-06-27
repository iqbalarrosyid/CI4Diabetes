<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<style>
    .justify-text {
        text-align: justify;
    }

    .patient-info-card {
        border: none;
        /* box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); */
        border-radius: 0.5rem;
    }

    .patient-info-card .card-body .d-flex {
        padding-bottom: 0.25rem;
    }

    .patient-info-card strong {
        display: block;
        line-height: 1.4;
    }

    .patient-info-card small.text-muted {
        font-size: 0.8rem;
    }

    .patient-info-card .fas {
        font-size: 1.3em;
        width: 25px;
    }
</style>


<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Riwayat Pasien: <?= esc($pasien['nama']) ?></h2>
        <a href="/petugas/riwayat/create/<?= esc($pasien['id'], 'attr') ?>" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Pemeriksaan Baru">
            <i class="fa-solid fa-plus"></i>
        </a>
    </div>

    <?php
    // Hitung umur secara otomatis
    $umurDisplay = "N/A";
    $tanggalLahirFormatted = "N/A";
    if (!empty($pasien['tanggal_lahir'])) {
        try {
            $tanggalLahir = new DateTime($pasien['tanggal_lahir']);
            $sekarang = new DateTime();
            $umur = $sekarang->diff($tanggalLahir);
            $umurDisplay = $umur->y . " tahun";
            $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'd MMMM yyyy');
            $tanggalLahirFormatted = $formatter->format($tanggalLahir);
        } catch (Exception $e) {
        }
    }
    ?>

    <div class="card mb-4 patient-info-card">
        <div class="card-body p-3 p-md-4">
            <div class="row gx-lg-5 gy-3">
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-calendar-alt me-3 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Tanggal Lahir</small>
                            <strong><?= esc($tanggalLahirFormatted) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-birthday-cake me-3 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Usia</small>
                            <strong><?= esc($umurDisplay) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fas <?= ($pasien['jenis_kelamin'] == 'Laki-laki' ? 'fa-mars' : 'fa-venus') ?> me-3 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Jenis Kelamin</small>
                            <strong><?= esc($pasien['jenis_kelamin']) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-map-marker-alt me-3 mt-1"></i>
                        <div>
                            <small class="text-muted d-block">Alamat</small>
                            <strong><?= esc($pasien['alamat']) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    <th>IMT (kg/m²)</th>
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
                        <td><?= esc(date('d-m-Y H:i:s', strtotime($data['created_at']))) ?></td>
                        <td><?= esc($data['nama_petugas'] ?? 'Tidak Diketahui') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="text-muted justify-text px-2 mt-2">
            <i>(*) Jika sebelumnya hasil prediksi menunjukan pasien Diabetes, kemudian prediksi berubah menjadi Tidak Diabetes, ini menunjukkan bahwa pasien dapat mengendalikan kadar gula darahnya. Bukan berarti sembuh dari diabetes.</i>
        </p>

    </div>

    <div class="d-flex justify-content-end mt-3">
        <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">Kembali</a>
        <button class="btn btn-primary me-2" onclick="toggleChart()" data-bs-toggle="tooltip" data-bs-placement="top" title="Tampilkan/Sembunyikan Grafik">
            <i class="fa-solid fa-square-poll-vertical"></i>
        </button>
        <a href="<?= base_url('petugas/riwayat/pdf/' . esc($pasien['id'], 'attr')) ?>" target="_blank" class="btn btn-danger me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Export PDF">
            <i class="fa-solid fa-file-pdf"></i>
        </a>
        <a href="<?= base_url('petugas/riwayat/exportExcel/' . esc($pasien['id'], 'attr')) ?>" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Excel">
            <i class="fa-solid fa-file-excel"></i>
        </a>
    </div>

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
    // Skrip DataTables (tetap sama)
    $(document).ready(function() {

        let table = $('#riwayatTable').DataTable({
            "ordering": true,
            "searching": false,
            "paging": true,
            "lengthChange": true,
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
                [7, 'desc']
            ],
            "rowCallback": function(row, data, index) {
                let api = this.api();
                let info = api.page.info();
                let globalIndex = index + 1 + info.start;
                $('td:eq(0)', row).html(globalIndex);
            }
        });
    });

    let chartInstance = null;

    function toggleChart() {
        let chartContainer = document.getElementById("chartContainer");
        if (typeof renderChart === "function") {
            if (chartContainer.style.display === "none" || !chartContainer.style.display) {
                chartContainer.style.display = "block";
                renderChart();
            } else {
                chartContainer.style.display = "none";
            }
        } else {
            $("#chartContainer").toggle();
        }
    }

    // Skrip modal sukses
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    let labels = [];
    let gdpData = [];
    let tekananDarahData = [];
    let imtData = [];

    <?php
    $riwayatGrafik = array_slice($riwayat, -20);
    foreach ($riwayatGrafik as $data) :
    ?>
        labels.push("<?= htmlspecialchars(date('d M Y H:i', strtotime($data['created_at'])), ENT_QUOTES, 'UTF-8') ?>");
        gdpData.push(<?= filter_var($data['gdp'], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) ?? 'null' ?>);
        tekananDarahData.push(<?= filter_var($data['tekanan_darah'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) ?? 'null' ?>);
        imtData.push(<?= filter_var($data['imt'], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) ?? 'null' ?>);
    <?php endforeach; ?>

    function renderChart() {
        let ctx = document.getElementById('riwayatChart').getContext('2d');
        if (chartInstance) {
            chartInstance.destroy();
        }
        const colors = {
            gdp: {
                border: 'rgb(54, 162, 235)',
                bg: 'rgba(54, 162, 235, 0.2)'
            },
            tekananDarah: {
                border: 'rgb(255, 99, 132)',
                bg: 'rgba(255, 99, 132, 0.2)'
            },
            imt: {
                border: 'rgb(75, 192, 192)',
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
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: colors.gdp.border,
                    pointHoverRadius: 6,
                    pointHoverBorderWidth: 2,
                    pointStyle: 'rectRounded'
                }, {
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
                }, {
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
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                family: 'Poppins'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.85)',
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
                                return tooltipItems[0].label;
                            },
                            label: function(tooltipItem) {
                                let label = tooltipItem.dataset.label || '';
                                if (label) {
                                    label = label.split('(')[0].trim() + ': ';
                                }
                                if (tooltipItem.parsed.y !== null) {
                                    label += tooltipItem.parsed.y;
                                }
                                return label;
                            }
                        }
                    },
                    decimation: {
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
                            drawOnChartArea: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: 'Poppins'
                            },
                            maxRotation: 45,
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
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0,0,0,0.08)',
                            borderDash: [3, 4]
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
</script>

<?= $this->endSection() ?>