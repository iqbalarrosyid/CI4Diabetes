<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3>Riwayat Pasien: <?= $pasien['nama']; ?></h3>
        <a href="/riwayat/create/<?= $pasien['id'] ?>" class="btn btn-success">Tambah</a>
    </div>

    <?php
    // Hitung umur secara otomatis
    $tanggalLahir = new DateTime($pasien['tanggal_lahir']);
    $sekarang = new DateTime();
    $umur = $sekarang->diff($tanggalLahir);
    ?>
    <p>Tanggal Lahir: <?= $tanggalLahir->format('d-m-Y'); ?> / <?= $umur->y; ?> tahun</p>
    <p>Jenis Kelamin: <?= $pasien['jenis_kelamin']; ?></p>
    <p>Alamat: <?= $pasien['alamat']; ?></p>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>GDP</th>
                    <th>Tekanan Darah</th>
                    <th>Berat</th>
                    <th>Tinggi</th>
                    <th>IMT</th>
                    <th>Hasil</th>
                    <th onclick="sortRiwayat()" style="cursor:pointer;">
                        Waktu <i id="sortIcon" class="fas fa-sort-down"></i>
                    </th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody id="riwayatBody">
                <?php foreach ($riwayat as $index => $data): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $data['gdp'] ?></td>
                        <td><?= $data['tekanan_darah'] ?></td>
                        <td><?= $data['berat'] ?> kg</td>
                        <td><?= $data['tinggi'] ?> cm</td>
                        <td><?= number_format($data['imt'], 2) ?></td>
                        <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                        <td class="waktu"><?= $data['created_at'] ?></td>
                        <td><?= $data['nama_petugas'] ?? 'Tidak Diketahui' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($riwayat)) : ?>
                    <tr>
                        <td colspan="9" class="text-center">Belum ada data riwayat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Tombol sejajar -->
    <div class="d-flex gap-2">
        <a href="/pasien" class="btn btn-secondary">Kembali</a>
        <button class="btn btn-primary" onclick="toggleChart()">Tampilkan Grafik</button>
    </div>

    <!-- Grafik -->
    <div id="chartContainer" class="mt-4" style="display: none;">
        <h4>Grafik Riwayat Pasien</h4>
        <div style="width: 100%; max-width: 800px; height: 350px;">
            <canvas id="riwayatChart"></canvas>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let sortOrder = -1; // Mulai dari DESC (terbaru ke lama)

    function sortRiwayat() {
        let tbody = document.getElementById("riwayatBody");
        let rows = Array.from(tbody.rows);

        rows.sort((rowA, rowB) => {
            let timeA = new Date(rowA.querySelector(".waktu").textContent.trim());
            let timeB = new Date(rowB.querySelector(".waktu").textContent.trim());
            return (timeB - timeA) * sortOrder; // DESC saat sortOrder = -1, ASC saat sortOrder = 1
        });

        tbody.innerHTML = '';
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
            tbody.appendChild(row);
        });

        // Toggle ikon sorting
        document.getElementById("sortIcon").className = sortOrder === -1 ? "fas fa-sort-up" : "fas fa-sort-down";

        // Balik arah sorting untuk klik berikutnya
        sortOrder *= -1;
    }

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