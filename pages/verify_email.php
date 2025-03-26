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
$stmt = $conn->prepare("SELECT id, confirmed_status FROM users WHERE email_verification_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Token tidak valid.</div>";
    include '../templates/footer.php';
    exit();
}

$user = $result->fetch_assoc();

if ($user['confirmed_status'] == 1) {
    echo "<div class='alert alert-info'>Email Anda sudah dikonfirmasi sebelumnya.</div>";
} else {
    // Update status menjadi terkonfirmasi dan set waktu verifikasi
    $stmtUpdate = $conn->prepare("UPDATE users SET confirmed_status = 1, email_verified_at = NOW(), email_verification_token = NULL WHERE id = ?");
    $stmtUpdate->bind_param("i", $user['id']);
    if ($stmtUpdate->execute()) {
        echo "<div class='alert alert-success'>Email berhasil dikonfirmasi. Silakan <a href='login.php'>login</a>.</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan saat konfirmasi email.</div>";
    }
    $stmtUpdate->close();
}
$stmt->close();

include '../templates/footer.php';
