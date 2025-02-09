<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Aplikasi Pasien' ?></title>

    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Sticky Footer CSS */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Full viewport height */
        }

        .content {
            flex: 1;
            /* Membuat konten utama mengambil ruang tersisa */
        }

        footer {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <?php if (!session()->get('logged_in')) {
        return redirect()->to('/login');
    } ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/pasien">Aplikasi Prediksi Diabetes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/pasien">Daftar Pasien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/riwayat/all">Riwayat Terbaru</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container mt-4 content">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3">
        <p class="mb-0">© <?= date('Y') ?> Aplikasi Pasien. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>