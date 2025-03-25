<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

if (!isset($_GET['token'])) {
    echo "<div class='alert alert-danger'>Token tidak ditemukan.</div>";
    include '../templates/footer.php';
    exit();
}

$token = $_GET['token'];

// Cek apakah token valid dan belum kedaluwarsa
$stmt = $conn->prepare("SELECT id, reset_token_expires FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Token tidak valid.</div>";
    include '../templates/footer.php';
    exit();
}

$user = $result->fetch_assoc();
if (strtotime($user['reset_token_expires']) < time()) {
    echo "<div class='alert alert-danger'>Token sudah kedaluwarsa.</div>";
    include '../templates/footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($newPassword) || empty($confirmPassword)) {
        $error = "Password tidak boleh kosong.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Password tidak cocok.";
    } else {
        // Hash password baru
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        // Update password dan hapus token reset
        $stmtUpdate = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        $stmtUpdate->bind_param("si", $passwordHash, $user['id']);
        if ($stmtUpdate->execute()) {
            echo "<div class='alert alert-success'>Password berhasil diubah. Silakan <a class='fw-bold link-primary link-underline-opacity-0' href='login.php'>login</a>.</div>";
            $stmtUpdate->close();
            include '../templates/footer.php';
            exit();
        } else {
            $error = "Terjadi kesalahan saat mengubah password.";
        }
        $stmtUpdate->close();
    }
}
?>

<h2>Reset Password</h2>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
<form method="POST">
    <div class="mb-3">
        <label>Password Baru</label>
        <input type="password" name="new_password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Ubah Password</button>
</form>

<?php include '../templates/footer.php'; ?>