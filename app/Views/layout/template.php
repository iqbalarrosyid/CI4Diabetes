<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Aplikasi Prediksi Diabetes' ?></title>
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/6bb49a14fa.js" crossorigin="anonymous"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding-top: 50px;
        }

        .content {
            flex: 1;
        }

        .navbar {
            transition: all 0.3s;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .navbar-nav .nav-item .nav-link {
            color: white;
            font-weight: 500;
            transition: color 0.3s ease-in-out;
        }

        .navbar-nav .nav-item .nav-link:hover {
            color: #ffdd57;
        }

        footer {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/petugas/pasien"><i class="fas fa-heartbeat"></i> Prediksi Diabetes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/petugas/pasien"><i class="fas fa-users"></i> Daftar Pasien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/petugas/riwayat/all"><i class="fas fa-history"></i> Riwayat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
    <footer class="text-center py-3 mt-4">
        <p class="mb-0">© <?= date('Y') ?> Petugas Panel - Website Prediksi Diabetes. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>