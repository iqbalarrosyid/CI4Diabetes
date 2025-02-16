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
    <a href="/pasien" class="btn btn-secondary">Kembali</a>
</div>

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
</script>

<?= $this->endSection() ?>