<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?? 'Aplikasi Prediksi Diabetes' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/6bb49a14fa.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary-color: #007bff;
            --primary-hover-color: #0056b3;
            --light-gray-bg: #f4f7f6;
            --white-bg: #ffffff;
            --text-color: #333;
            --text-muted-color: #666;
            --border-color: #ddd;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            --card-border-radius: 15px;
        }

        body {
            background-color: var(--light-gray-bg);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            padding-top: 80px;
        }

        .content {
            flex: 1;
            padding-bottom: 2rem;
        }

        /* Navbar Styling */
        .navbar {
            background-color: var(--white-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            transition: all 0.3s ease-in-out;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .navbar-brand i {
            margin-right: 8px;
        }

        .navbar-nav .nav-item .nav-link {
            color: black;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: color 0.3s ease-in-out, background-color 0.3s ease-in-out;
        }

        .navbar-nav .nav-item .nav-link:hover,
        .navbar-nav .nav-item .nav-link.active {
            /* Menambahkan style untuk link aktif */
            color: var(--primary-color);
            background-color: rgba(0, 123, 255, 0.05);
        }

        .navbar-nav .nav-item .nav-link i {
            margin-right: 6px;
            transition: transform 0.2s ease-in-out;
        }

        .navbar-nav .nav-item .nav-link:hover i {
            transform: scale(1.1);
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .dropdown-menu {
            border-radius: var(--card-border-radius, 10px);
            box-shadow: var(--card-shadow, 0 5px 15px rgba(0, 0, 0, 0.1));
            border: 1px solid #f0f0f0;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            color: black;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 16px;
            /* Agar ikon sejajar */
            text-align: center;
        }

        .dropdown-item:hover {
            background-color: rgba(0, 123, 255, 0.07);
            color: var(--primary-color);
        }

        .dropdown-item:active {
            background-color: rgba(0, 123, 255, 0.1);
        }


        /* Main Content Container Styling */
        .main-content-container {
            /* Mengganti nama kelas .container menjadi lebih spesifik */
            background-color: var(--white-bg);
            border-radius: var(--card-border-radius);
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-top: 1.5rem;
        }

        /* Footer Styling */
        footer {
            background-color: var(--white-bg);
            color: var(--text-muted-color);
            border-top: 1px solid #e9ecef;
            font-size: 0.9rem;
            padding: 1.5rem 0;
        }

        footer p {
            margin-bottom: 0;
        }

        /* Modal Styling (Logout) */
        .modal-content {
            border-radius: var(--card-border-radius);
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            color: var(--text-color);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-body p {
            font-size: 1rem;
            color: var(--text-muted-color);
            margin-bottom: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 1rem 1.5rem;
            gap: 0.5rem;
            /* Jarak antar tombol */
        }

        .modal-dialog-centered .fa-arrow-right-from-bracket,
        .modal-dialog-centered .fa-circle-xmark

        /* Untuk modal error jika ada */
            {
            font-size: 2.8rem;
            /* Ukuran ikon disesuaikan */
            margin-bottom: 0.5rem;
        }

        .modal-content .btn {
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
        }

        .modal-content .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .modal-content .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .modal-content .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .modal-content .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* General Icon Hover Effect (jika belum tercakup) */
        a:hover i,
        button:hover i,
        .nav-link:hover i {
            transform: scale(1.1);
            /* Sedikit perbesar ikon saat hover */
            transition: transform 0.2s ease-in-out;
        }

        @media (max-width: 768px) {
            .main-content-container {
                /* Mengganti nama kelas .container menjadi lebih spesifik */
                background-color: var(--white-bg);
                border-radius: var(--card-border-radius);
                box-shadow: var(--card-shadow);
                padding-top: 2rem;
                padding-bottom: 2rem;
                padding-left: 1rem;
                padding-right: 1rem;
                margin-top: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container"> <a class="navbar-brand" href="/admin/">
                <i class="fas fa-heart-pulse"></i> Prediksi Diabetes T2 </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-sliders" style="color: #000000;"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/pasien"><i class="fas fa-users"></i> Daftar Pasien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/petugas"><i class="fas fa-user"></i> Daftar Petugas</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle"></i> Profil </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="/profile/edit"><i class="fa-solid fa-user-pen"></i> Edit Profil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content">
        <div class="container main-content-container"> <?= $this->renderSection('content') ?>
        </div>
    </div>


    <footer class="text-center py-3 mt-auto">
        <p class="mb-0">© <?= date('Y') ?> Sistem Prediksi Diabetes Melitus Tipe 2. Hak Cipta Dilindungi.</p>
    </footer>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="mx-auto mb-3" style="font-size: 40px; color: #dc3545;">
                    <i class="fa-solid fa-arrow-right-from-bracket fa-beat"></i>
                </div>
                <h5 class="modal-title mb-2" id="logoutModalLabel">Konfirmasi Logout</h5>
                <p>Apakah Anda yakin ingin logout?</p>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    <a href="/logout" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Inisialisasi Tooltip Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const currentLocation = window.location.pathname;
            const navLinks = document.querySelectorAll(".navbar-nav .nav-link");
            navLinks.forEach(link => {
                if (link.getAttribute("href") === currentLocation) {
                    link.classList.add("active");
                }
            });
        });
    </script>
    <?= $this->renderSection('pageScripts') ?>
</body>

</html>