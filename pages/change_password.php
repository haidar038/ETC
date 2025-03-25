<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "Semua field wajib diisi.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Password baru tidak cocok.";
    } else {
        // Ambil password lama dari database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (!password_verify($currentPassword, $user['password'])) {
                $error = "Password saat ini salah.";
            } else {
                // Update password baru
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmtUpdate = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmtUpdate->bind_param("si", $newPasswordHash, $user_id);
                if ($stmtUpdate->execute()) {
                    $success = "Password berhasil diubah.";
                } else {
                    $error = "Terjadi kesalahan saat mengubah password.";
                }
                $stmtUpdate->close();
            }
        }
        $stmt->close();
    }
}
?>

<h2>Ganti Password</h2>
<?php
if (isset($error)) echo "<div class='alert alert-danger'>$error</div>";
if (isset($success)) echo "<div class='alert alert-success'>$success</div>";
?>
<form method="POST">
    <div class="mb-3">
        <label>Password Saat Ini</label>
        <input type="password" name="current_password" class="form-control" required>
    </div>
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