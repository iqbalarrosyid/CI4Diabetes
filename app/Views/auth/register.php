<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Prediksi Diabetes</title>

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

        /* Card register dengan shadow lembut */
        .register-card {
            max-width: 900px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        /* Gambar register */
        .register-img {
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

        /* Tombol register lebih menarik */
        .btn-success {
            background-color: #28a745;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        /* Link login */
        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container-sm px-4 py-5">
        <div class="row justify-content-center">
            <div class="card register-card p-0">
                <div class="row g-0">

                    <!-- Gambar Register -->
                    <div class="col-md-6 d-none d-md-block">
                        <img src="https://images.unsplash.com/photo-1556741533-411cf82e4e2d?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Register Image" class="img-fluid register-img">
                    </div>

                    <!-- Form Register -->
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="card-body p-4 ">
                            <h3 class="mb-3 text-center">📝 Register</h3>
                            <p class="text-center">Daftarkan akun Anda untuk masuk ke <b>Sistem Prediksi Diabetes</b>.</p>

                            <!-- Notifikasi Error -->
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                            <?php endif; ?>

                            <form action="/register" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                                </div>

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

                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="admin">Admin</option>
                                        <option value="petugas">Petugas</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success w-100 mt-2">Register</button>
                            </form>

                            <p class="mt-3 login-link">Sudah punya akun? <a href="/">Login di sini</a></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Script untuk Toggle Password -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>