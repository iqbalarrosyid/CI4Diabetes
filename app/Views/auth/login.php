<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Prediksi Diabetes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/6bb49a14fa.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #f4f7f6;
            /* Latar belakang lebih lembut */
            font-family: 'Poppins', sans-serif;
            color: #333;
            min-height: 100vh;
            /* Menggunakan min-height untuk fleksibilitas */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            /* Padding untuk layar kecil */
        }

        .login-card {
            max-width: 950px;
            /* Sedikit lebih lebar untuk keseimbangan */
            background: #ffffff;
            border-radius: 15px;
            /* Border radius lebih besar */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            /* Shadow lebih soft */
            overflow: hidden;
            display: flex;
            /* Menggunakan flex untuk kolom */
        }

        .login-form-container {
            flex: 1;
            padding: 2.5rem 3rem;
            /* Padding lebih besar */
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(to right, #ffffff, #f9f9f9);
            /* Gradient halus */
        }

        .login-form-container h3 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            /* Penyesuaian margin */
            color: #333;
        }

        .login-form-container .sub-text {
            /* Kelas baru untuk sub-teks */
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 500;
            /* Label sedikit lebih tebal */
            margin-bottom: 0.3rem;
            /* Margin lebih kecil */
            font-size: 0.85rem;
            color: #555;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            /* Border lebih soft */
            padding: 12px 15px;
            /* Padding disesuaikan */
            transition: all 0.3s ease-in-out;
            font-size: 0.9rem;
            background-color: #fdfdfd;
            /* Sedikit off-white */
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
            /* Shadow lebih halus */
            border-color: #007bff;
            background-color: #fff;
        }

        .input-group .form-control {
            /* Pastikan input dalam group tidak punya border radius kanan */
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group .btn {
            /* Pastikan tombol dalam group tidak punya border radius kiri */
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left: 0;
            /* Hilangkan border kiri tombol */
            border-color: #ddd;
            /* Sesuaikan border color */
        }

        .input-group .btn:hover,
        .input-group .btn:focus {
            background-color: #e9ecef;
            /* Hover effect untuk tombol mata */
        }


        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px;
            /* Padding disesuaikan */
            border-radius: 8px;
            font-weight: 600;
            /* Font weight lebih tebal */
            transition: all 0.3s ease-in-out;
            text-transform: uppercase;
            /* Kapital untuk teks tombol */
            letter-spacing: 0.5px;
            /* Sedikit spasi antar huruf */
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 6px 15px rgba(0, 86, 179, 0.3);
            transform: translateY(-2px);
            /* Efek hover naik sedikit */
        }

        .register-link {
            /* Jika Anda ingin menambahkan link registrasi nanti */
            font-size: 0.85rem;
            margin-top: 1.5rem;
            color: #555;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Styling untuk Modal Error */
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            /* Jika menggunakan modal header */
            border-bottom: none;
            padding-bottom: 0;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body p {
            font-size: 0.95rem;
            color: #555;
        }

        .modal-footer {
            /* Jika menggunakan modal footer */
            border-top: none;
        }

        .modal-dialog-centered .fa-circle-xmark {
            font-size: 3.5rem;
            /* Ukuran ikon disesuaikan */
            color: #dc3545;
        }

        .modal-content .btn-danger {
            font-weight: 500;
            padding: 8px 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                /* Stack kolom pada layar kecil */
                max-width: 450px;
                /* Lebar maksimal untuk form saja */
            }

            .login-form-container {
                border-radius: 15px;
                /* Tambahkan border radius karena gambar hilang */
                padding: 2rem;
                /* Padding disesuaikan */
            }

            .login-form-container h3 {
                font-size: 1.5rem;
                /* Ukuran font judul disesuaikan */
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid px-0">
        <div class="row justify-content-center align-items-center min-vh-100 m-0">
            <div class="col-11 col-md-10 col-lg-8 px-0">
                <div class="card login-card">

                    <div class="login-form-container">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-shield-heart fa-2x mb-2" style="color: #007bff;"></i>
                            <h3 class="mb-2"><b>Login Akun</b></h3>
                            <p class="sub-text">Selamat datang! Masuk untuk mengakses Sistem Prediksi Diabetes Melitus Tipe 2.</p>
                        </div>

                        <form action="/login" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username Anda" required>
                            </div>

                            <div class="mb-4"> <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()" aria-label="Toggle Password Visibility">
                                        <i id="toggleIcon" class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-3 py-2">Login</button>
                        </form>
                        <!-- <p class="text-center mt-4 register-link">Belum punya akun? <a href="/register">Daftar di sini</a></p> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="mx-auto mb-3">
                    <i class="fa-solid fa-circle-xmark fa-beat"></i>
                </div>
                <h5 class="modal-title mb-2" id="errorModalLabel">Login Gagal</h5>
                <p class="mb-3"><?= session()->getFlashdata('error') ?? 'Terjadi kesalahan. Silakan coba lagi.' ?></p>
                <button type="button" class="btn btn-danger mt-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const toggleIcon = document.getElementById("toggleIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
            }
        }
    </script>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            });
        </script>
    <?php endif; ?>

</body>

</html>