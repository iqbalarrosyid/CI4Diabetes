<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin - Aplikasi Prediksi Diabetes' ?></title>
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/6bb49a14fa.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #f7f7f7;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding-top: 70px;
        }

        .container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0, 0, 0, .04);
            padding: 10px;
        }

        .content {
            flex: 1;
        }

        .navbar {
            transition: all 0.3s;
            background: white;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .navbar-nav .nav-item .nav-link {
            color: black;
            font-weight: 500;
            transition: color 0.3s ease-in-out;
        }

        .navbar-nav .nav-item .nav-link:hover {
            background-color: rgba(24, 24, 24, 0.04);
        }

        footer {
            background-color: #fff;
            color: #888;
            border-top: 1px solid #eee;
        }

        a:hover i,
        button:hover i {
            transform: scale(1.2);
            transition: transform 0.2s ease-in-out;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow">
        <div class="container">
            <a class="navbar-brand" href="/admin/pasien"><i class="fas fa-heartbeat"></i> Prediksi Diabetes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/pasien"><i class="fas fa-users"></i> Daftar Pasien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/petugas/"><i class="fas fa-user-md"></i> Daftar Petugas</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user"></i> Profile
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="/profile/edit"><i class="fa-solid fa-pen"></i> Edit</a></li>
                            <li>
                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                </button>
                            </li>
                        </ul>
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
        <p class="mb-0">© <?= date('Y') ?> Admin Panel - Website Prediksi Diabetes. All rights reserved.</p>
    </footer>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="mx-auto mb-3" style="font-size: 40px; color: #dc3545;">
                    <i class="fa-solid fa-arrow-right-from-bracket fa-beat"></i>
                </div>
                <h5 class="modal-title mb-2" id="logoutModalLabel">Konfirmasi Logout</h5>
                <p>Apakah Anda yakin ingin logout?</p>
                <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Batal</button>
                <a href="/logout" class="btn btn-danger mt-2">Logout</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>