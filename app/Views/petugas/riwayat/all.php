<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<style>
    .table-responsive {
        overflow-x: auto;
        white-space: nowrap;
    }

    /* Kunci posisi kolom No dan Nama */
    .sticky-column {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 2;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    /* Kunci kolom Nama agar tetap di sebelah kanan No */
    .sticky-column:nth-child(2) {
        left: 35px;
    }
</style>

<div class="container">
    <h2 class="mb-4">Riwayat Terbaru Semua Pasien</h2>

    <!-- Form Search -->
    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Cari Nama Pasien...">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="riwayatTable">
            <thead>
                <tr>
                    <th class="sticky-column">No</th>
                    <th class="sortable sticky-column" data-col="1">Nama <i id="sortIconNama" class="fas fa-sort-alpha-down"></i></th>
                    <th>Umur</th>
                    <th>GDP</th>
                    <th>Tekanan Darah</th>
                    <th>Berat (kg)</th>
                    <th>Tinggi (cm)</th>
                    <th>IMT</th>
                    <th>Hasil</th>
                    <th class="sortable" data-col="9">Waktu <i id="sortIconWaktu" class="fas fa-sort-down"></i></th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php foreach ($riwayat as $index => $data): ?>
                    <?php
                    $tanggalLahir = new DateTime($data['tanggal_lahir']);
                    $sekarang = new DateTime();
                    $umur = $sekarang->diff($tanggalLahir)->y;
                    ?>
                    <tr>
                        <td class="sticky-column"><?= $index + 1 ?></td>
                        <td class="sticky-column"><?= htmlspecialchars($data['nama_pasien']) ?></td>
                        <td><?= $umur ?> tahun</td>
                        <td><?= $data['gdp'] ?></td>
                        <td><?= $data['tekanan_darah'] ?></td>
                        <td><?= $data['berat'] ?></td>
                        <td><?= $data['tinggi'] ?></td>
                        <td><?= number_format($data['imt'], 2) ?></td>
                        <td><?= $data['hasil'] == 1 ? 'Diabetes' : 'Tidak Diabetes' ?></td>
                        <td><?= $data['created_at'] ?></td>
                        <td><?= $data['nama_petugas'] ?? 'Tidak Diketahui' ?></td>
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

    <a href="/pasien" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

<!-- JavaScript untuk Sorting & Search -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let table = document.getElementById("riwayatTable");
        let tbody = document.getElementById("tableBody");
        let rows = Array.from(tbody.getElementsByTagName("tr"));
        let searchInput = document.getElementById("search");
        let sortOrder = {
            1: 1,
            9: 1
        };

        function sortTable(columnIndex, iconId, isAlpha) {
            sortOrder[columnIndex] *= -1;
            let sortedRows = rows.slice().sort((a, b) => {
                let aText = a.cells[columnIndex].textContent.trim();
                let bText = b.cells[columnIndex].textContent.trim();

                if (columnIndex === 9) { // If column is Waktu, sort as date
                    return sortOrder[columnIndex] * (new Date(aText) - new Date(bText));
                } else {
                    return sortOrder[columnIndex] * aText.localeCompare(bText);
                }
            });

            tbody.innerHTML = "";
            rows = sortedRows;

            // Update row numbers
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1; // Update row number
                tbody.appendChild(row);
            });

            if (columnIndex === 9) {
                document.getElementById(iconId).className = sortOrder[columnIndex] === -1 ? "fas fa-sort-down" : "fas fa-sort-up";
            } else {
                document.getElementById(iconId).className = sortOrder[columnIndex] === -1 ? "fas fa-sort-alpha-up" : "fas fa-sort-alpha-down";
            }
        }

        searchInput.addEventListener("input", function() {
            let searchValue = searchInput.value.toLowerCase();
            rows.forEach(row => {
                let nameCell = row.cells[1].textContent.toLowerCase();
                row.style.display = nameCell.includes(searchValue) ? "table-row" : "none";
            });
        });

        document.querySelector(".sortable[data-col='1']").addEventListener("click", function() {
            sortTable(1, "sortIconNama", true);
        });
        document.querySelector(".sortable[data-col='9']").addEventListener("click", function() {
            sortTable(9, "sortIconWaktu", false);
        });
    });
</script>

<?= $this->endSection() ?>