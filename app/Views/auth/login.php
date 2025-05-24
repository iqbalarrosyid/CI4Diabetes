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
        :root {
            --primary-color: #007bff;
            --primary-hover-color: #0056b3;
            --light-gray-bg: #f4f7f6;
            --text-color: #333;
            --text-muted-color: #666;
            --border-color: #ddd;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --card-border-radius: 20px;
        }

        body {
            background-color: var(--light-gray-bg);
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
        }

        .login-card-wrapper {
            width: 100%;
            max-width: 950px;
        }

        .login-card {
            background: #ffffff;
            border-radius: var(--card-border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            width: 100%;
        }

        .login-image-side {
            background: url('https://images.pexels.com/photos/6941879/pexels-photo-6941879.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1') no-repeat center center;
            background-size: cover;
        }

        .login-form-container {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100%;
        }

        .login-form-container .brand-logo {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .login-form-container h3 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-size: 1.75rem;
        }

        .login-form-container .sub-text {
            font-size: 0.9rem;
            color: var(--text-muted-color);
            margin-bottom: 2.5rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
            color: #495057;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid var(--border-color);
            padding: 0.8rem 1rem;
            transition: all 0.2s ease-in-out;
            font-size: 0.9rem;
            background-color: #fdfdfd;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
            border-color: var(--primary-color);
            background-color: #fff;
        }

        .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group .btn-toggle-password {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left: 0;
            border-color: var(--border-color);
            background-color: #fff;
            color: var(--text-muted-color);
        }

        .input-group .btn-toggle-password:hover,
        .input-group .btn-toggle-password:focus {
            background-color: #e9ecef;
            border-color: var(--border-color);
            box-shadow: none;
        }

        .input-group .btn-toggle-password i {
            transition: color 0.2s ease-in-out;
        }


        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.25);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover-color);
            box-shadow: 0 6px 15px rgba(0, 86, 179, 0.35);
            transform: translateY(-2px);
        }

        .modal-content {
            border-radius: var(--card-border-radius);
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .modal-header.error-modal-header {
            border-bottom: none;
            padding-bottom: 0;
            justify-content: center;
        }

        .modal-title.error-modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .modal-body.error-modal-body p {
            font-size: 1rem;
            color: var(--text-muted-color);
        }

        .modal-dialog-centered .fa-circle-xmark {
            font-size: 4rem;
            color: #dc3545;
        }

        @media (max-width: 767.98px) {
            .login-form-container {
                padding: 2rem 1.5rem;
                background: #ffffff;
            }

            .login-form-container h3 {
                font-size: 1.5rem;
            }

            .login-form-container .sub-text {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-card-wrapper">
        <div class="card login-card">
            <div class="row g-0">
                <div class="col-md-5 d-none d-md-block login-image-side">
                </div>

                <div class="col-md-7">
                    <div class="login-form-container">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-shield-heart brand-logo"></i>
                            <h3>Login Akun</h3>
                            <p class="sub-text mt-3">Selamat datang! Masuk untuk mengakses Sistem Prediksi Diabetes Melitus Tipe 2.</p>
                        </div>

                        <form action="/login" method="POST">
                            <?= csrf_field() ?> <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username Anda" required value="<?= old('username') ?>">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                                    <button type="button" class="btn btn-toggle-password" onclick="togglePassword()" aria-label="Tampilkan atau sembunyikan password">
                                        <i id="toggleIcon" class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-3 py-2">Login</button>
                        </form>
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
                <h5 class="modal-title error-modal-title mb-2" id="errorModalLabel">Login Gagal</h5>
                <div class="modal-body error-modal-body py-0">
                    <p class="mb-3"><?= session()->getFlashdata('error') ?? 'Username atau password salah. Silakan coba lagi.' ?></p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                </div>
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

        // Script untuk menampilkan error modal jika ada flashdata
        <?php if (session()->getFlashdata('error')): ?>
            window.addEventListener('DOMContentLoaded', () => {
                const errorModalElement = document.getElementById('errorModal');
                if (errorModalElement) {
                    const errorModal = new bootstrap.Modal(errorModalElement);
                    errorModal.show();
                }
            });
        <?php endif; ?>
    </script>

</body>

</html>