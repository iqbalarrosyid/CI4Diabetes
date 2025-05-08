<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Prediksi Diabetes</title>

    <!-- Bootstrap & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/6bb49a14fa.js" crossorigin="anonymous"></script>

    <style>
        /* Background putih & font modern */
        body {
            background-color: #ffffff;
            font-family: 'Poppins', sans-serif;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Card login dengan shadow lembut */
        .login-card {
            max-width: 900px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        /* Gambar login */
        .login-img {
            border-radius: 12px 0 0 12px;
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        /* Style untuk input form */
        .form-control {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px;
            transition: 0.3s;
        }

        .form-control:focus {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
            border-color: #007bff;
        }

        /* Tombol login lebih menarik */
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        /* Link register */
        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container-sm px-4">
        <div class="row justify-content-center">
            <div class="card login-card p-0">
                <div class="row g-0">

                    <!-- Gambar Login -->
                    <div class="col-md-6 d-none d-md-block">
                        <img src="https://img.freepik.com/free-photo/side-view-diabetic-woman-checking-her-glucose-level_23-2150756364.jpg?t=st=1741597734~exp=1741601334~hmac=4320d1d48dc20804703d849ffff0182201a25608bbfe7dc6022ad69265b6fb6e&w=1060" alt="Login Image" class="img-fluid login-img">
                    </div>

                    <!-- Form Login -->
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="card-body p-4 ">
                            <h3 class="mb-3 text-center"><i class="fa-solid fa-arrow-right-to-bracket"></i> <b>Login</b></h3>
                            <p class="text-center">Silakan login untuk masuk ke <b>Sistem Prediksi Diabetes</b>.</p>
                            <form action="/login" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                            <i id="toggleIcon" class="fa-solid fa-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
                            </form>

                            <p class="mt-3 register-link">Belum punya akun? <a href="/register">Daftar di sini</a></p>
                        </div>
                    </div>

                    <!-- Modal Gagal Login -->
                    <div class="modal fade" id="loginErrorModal" tabindex="-1" aria-labelledby="loginErrorModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content text-center p-4">
                                <div class="mx-auto mb-3" style="font-size: 40px; color: #dc3545;">
                                    <i class="fa-solid fa-circle-xmark fa-beat"></i>
                                </div>
                                <h5 class="modal-title mb-2" id="loginErrorModalLabel">Login Gagal</h5>
                                <p>Username atau password salah. Silakan coba lagi.</p>
                                <button type="button" class="btn btn-danger mt-2" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toggle Password -->
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = document.getElementById("toggleIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            }
        }
    </script>

    <!-- Show Modal if Error -->
    <?php if (session()->getFlashdata('error')): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const loginErrorModal = new bootstrap.Modal(document.getElementById('loginErrorModal'));
                loginErrorModal.show();
            });
        </script>
    <?php endif; ?>

</body>

</html>