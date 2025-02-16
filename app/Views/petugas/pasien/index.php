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

    /* Style untuk tombol sorting */
    .sort-button {
        border: none;
        background: none;
        cursor: pointer;
        font-size: 16px;
        padding: 0;
        margin-left: 5px;
        color: black;
    }
</style>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?= $title ?></h2>
        <a href="/pasien/create" class="btn btn-success">+ Pasien</a>
    </div>

    <!-- Form Pencarian -->
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama pasien..." onkeyup="searchTable()">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pasienTable">
            <thead>
                <tr>
                    <th class="sticky-column">No</th>
                    <th class="sticky-column">
                        Nama
                        <button class="sort-button" onclick="sortTable()">
                            <i id="sortIcon" class="fas fa-sort"></i>
                        </button>
                    </th>
                    <th>Umur</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="pasienBody">
                <?php foreach ($pasien as $index => $p) : ?>
                    <tr data-original-index="<?= $index + 1 ?>">
                        <td class="sticky-column"><?= $index + 1 ?></td>
                        <td class="sticky-column"><?= esc($p['nama']) ?></td>
                        <td><?= date_diff(date_create($p['tanggal_lahir']), date_create('today'))->y ?> tahun</td>
                        <td><?= esc($p['jenis_kelamin']) ?></td>
                        <td><?= esc($p['alamat']) ?></td>
                        <td>
                            <div class="d-flex flex-sm-row flex-column gap-2">
                                <a href="/riwayat/<?= $p['id'] ?>" class="text-info">
                                    <i class="fas fa-clock fa-lg"></i>
                                </a>
                                <a href="/pasien/edit/<?= $p['id'] ?>" class="text-warning">
                                    <i class="fas fa-edit fa-lg"></i>
                                </a>
                                <form action="/pasien/delete/<?= $p['id'] ?>" method="post" onsubmit="return confirm('Yakin ingin menghapus?');" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="border-0 bg-transparent text-danger">
                                        <i class="fas fa-trash fa-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pasien)) : ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data pasien.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        let sortOrder = 1; // 1: A-Z, -1: Z-A
        const rowsPerPage = 10;
        let currentPage = 1;

        function sortTable() {
            let tbody = document.getElementById("pasienBody");
            let rows = Array.from(tbody.rows);

            // Simpan urutan nomor sebelum sorting
            let originalIndexes = rows.map(row => row.cells[0].textContent.trim());

            // Sorting berdasarkan Nama
            rows.sort((rowA, rowB) => {
                let nameA = rowA.cells[1].textContent.trim().toLowerCase();
                let nameB = rowB.cells[1].textContent.trim().toLowerCase();
                return nameA.localeCompare(nameB) * sortOrder;
            });

            // Hapus semua baris dari tbody
            tbody.innerHTML = '';

            // Tambahkan kembali baris ke tbody dan atur nomor berdasarkan urutan asli
            rows.forEach((row, index) => {
                row.cells[0].textContent = originalIndexes[index]; // Tetapkan nomor berdasarkan urutan asli
                tbody.appendChild(row);
            });

            // Ubah ikon sorting
            document.getElementById("sortIcon").className = sortOrder === 1 ? "fas fa-sort-alpha-down" : "fas fa-sort-alpha-up";

            // Balik arah sorting untuk klik berikutnya
            sortOrder *= -1;

        }


        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.getElementById("pasienBody").getElementsByTagName("tr");

            for (let row of rows) {
                let namaPasien = row.cells[1].textContent.toLowerCase();
                row.style.display = namaPasien.includes(input) ? "" : "none";
            }
        }
    </script>

    <?= $this->endSection() ?>