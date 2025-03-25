<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya visitor yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data profil visitor
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$visitor = $result->fetch_assoc();
$stmt->close();
?>

<a href="dashboard.php" class="btn btn-sm btn-secondary mb-4">&larr; Kembali</a>

<h2 class="fw-bold text-primary mb-4">Profil Visitor</h2>

<div class="card mb-3">
    <div class="card-body">
        <p><strong>Nama:</strong> <?= htmlspecialchars($visitor['name']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($visitor['email']); ?></p>
        <p><strong>Poin:</strong> <?= number_format($visitor['points'], 0, ',', '.'); ?></p>
        <p><strong>Dibuat:</strong> <?= $visitor['created_at']; ?></p>
    </div>
</div>

<!-- Contoh link untuk fitur change password atau update profil -->
<div class="mb-3">
    <a href="../change_password.php" class="btn btn-warning">Ganti Password</a>
</div>

<?php include '../../templates/footer.php'; ?>