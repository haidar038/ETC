<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Tambahkan fungsi untuk mencegah admin setelah login kembali ke halaman login
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'admin') {
    header("Location: dashboard.php");
    exit();
}

// AUTO-LOGIN untuk admin (jika cookie remember_me ada)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ? AND user_type = 'admin'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['username'] = $user['username'];

        // Regenerate token
        $newToken = bin2hex(random_bytes(16));
        $stmtUpdate = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $stmtUpdate->bind_param("si", $newToken, $user['id']);
        $stmtUpdate->execute();
        $stmtUpdate->close();
        setcookie("remember_me", $newToken, time() + 2592000, "/", "", isset($_SERVER["HTTPS"]), true);

        header("Location: ../admin/dashboard.php");
        exit();
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (!$email || empty($password)) {
        $error = "Email atau password tidak valid.";
    } else {
        // Hanya cari akun dengan user_type admin
        $stmt = $conn->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND user_type = 'admin'");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
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
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Akun admin tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>

<h2>Admin Login</h2>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
<form method="POST">
    <div class="mb-3">
        <label>Email</label>
        <input type="text" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" id="remember_admin">
        <label class="form-check-label" for="remember_admin">Remember Me</label>
    </div>
    <button type="submit" class="btn btn-dark">Login Admin</button>
</form>

<?php include '../../templates/footer.php'; ?>