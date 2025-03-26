<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

// AUTO-LOGIN (jika cookie remember_me ada)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ? AND user_type IN ('visitor','tenant')");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Cek apakah email sudah diverifikasi
        if ($user['confirmed_status'] == 0) {
            $error = "Akun ini belum diverifikasi. Silakan cek email Anda untuk konfirmasi.";
        } else {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];

            // Regenerate token untuk keamanan tambahan
            $newToken = bin2hex(random_bytes(16));
            $stmtUpdate = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmtUpdate->bind_param("si", $newToken, $user['id']);
            $stmtUpdate->execute();
            $stmtUpdate->close();
            setcookie("remember_me", $newToken, time() + 2592000, "/", "", isset($_SERVER["HTTPS"]), true);

            // Redirect ke dashboard sesuai role
            if ($user['user_type'] == 'visitor') {
                header("Location: ../pages/visitor/dashboard.php");
            } elseif ($user['user_type'] == 'tenant') {
                header("Location: ../pages/tenant/dashboard.php");
            }
            exit();
        }
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($login) || empty($password)) {
        $error = "Email/Username atau password tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND user_type IN ('visitor','tenant')");
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Cek apakah email sudah diverifikasi
            if ($user['confirmed_status'] == 0) {
                $error = "Akun ini belum diverifikasi. Silakan cek email Anda untuk konfirmasi.";
            } elseif (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['username'] = $user['username'];

                if ($remember) {
                    $token = bin2hex(random_bytes(16));
                    $stmtToken = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmtToken->bind_param("si", $token, $user['id']);
                    $stmtToken->execute();
                    $stmtToken->close();
                    setcookie("remember_me", $token, time() + 2592000, "/", "", isset($_SERVER["HTTPS"]), true);
                }

                // Redirect ke dashboard
                if ($user['user_type'] == 'visitor') {
                    header("Location: ../pages/visitor/dashboard.php");
                } elseif ($user['user_type'] == 'tenant') {
                    header("Location: ../pages/tenant/dashboard.php");
                }
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email atau username tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-5">
        <h2 class="mb-4 fw-bold text-primary">Login</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <label for="login" class="form-label">Email atau Username</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa fa-envelope text-primary"></i></span>
                <input type="text" name="login" class="form-control" required placeholder="Masukkan email atau username">
            </div>
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa fa-lock text-primary"></i></span>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fa fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat Saya</label>
                </div>
                <a href="forgot_password.php" class="link-primary link-underline-opacity-0">Lupa Kata Sandi?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
        <p class="mt-3 text-center">Belum punya akun? <a class="link-primary fw-bold" href="register.php">Daftar Sekarang</a></p>
        <div class="text-center">
            <a class="link-primary fw-bold link-underline-opacity-0" href="/pages/admin/admin_login.php">Login Sebagai Admin</a>
        </div>

        <script>
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('eyeIcon');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        </script>
    </div>
</div>

<?php include '../templates/footer.php'; ?>