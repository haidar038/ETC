<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        $error = "Email atau password tidak valid.";
    } else {
        // Menggunakan prepared statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Gunakan password_verify jika password sudah di-hash dengan password_hash()
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_type'] = $user['user_type'];
                // Redirect berdasarkan role
                if ($user['user_type'] == 'visitor') {
                    header("Location: ../pages/visitor/dashboard.php");
                } elseif ($user['user_type'] == 'tenant') {
                    header("Location: ../pages/tenant/dashboard.php");
                } elseif ($user['user_type'] == 'admin') {
                    header("Location: ../pages/admin/dashboard.php");
                }
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>

<!-- HTML Form Login -->
<h2>Login</h2>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
<form method="POST">
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<?php include '../templates/footer.php'; ?>