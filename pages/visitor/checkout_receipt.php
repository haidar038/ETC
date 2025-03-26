<?php
// pages/visitor/checkout_receipt.php

include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya visitor yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['transaction_id'])) {
    echo "<div class='alert alert-danger'>Transaksi tidak ditemukan.</div>";
    include '../../templates/footer.php';
    exit();
}

$transaction_id = intval($_GET['transaction_id']);

// Ambil data transaksi
$stmt = $conn->prepare("SELECT t.*, u.name AS tenant_name FROM transactions t LEFT JOIN users u ON t.tenant_id = u.id WHERE t.id = ? AND t.user_id = ?");
$stmt->bind_param("ii", $transaction_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Transaksi tidak ditemukan.</div>";
    include '../../templates/footer.php';
    exit();
}
$transaction = $result->fetch_assoc();
$stmt->close();

// Tampilkan receipt
?>
<h2 class="mb-4">Receipt Checkout</h2>
<div class="card">
    <div class="card-header">
        Detail Transaksi
    </div>
    <div class="card-body">
        <p><strong>ID Transaksi:</strong> <?= $transaction['id']; ?></p>
        <p><strong>Tenant:</strong> <?= $transaction['tenant_name']; ?></p>
        <p><strong>Nominal:</strong> Rp <?= number_format($transaction['nominal'], 0, ',', '.'); ?></p>
        <p><strong>Status:</strong> <?= ucfirst($transaction['status']); ?></p>
        <p><strong>Tanggal Transaksi:</strong> <?= $transaction['transaction_date']; ?></p>
        <a href="confirm_payment.php" class="btn btn-success"><i class="bi bi-credit-card"></i> Lakukan Pembayaran</a>
    </div>
</div>
<div class="mt-3">
    <a href="../visitor/dashboard.php" class="btn btn-primary">&larr; Kembali ke Dashboard</a>
</div>

<?php include '../../templates/footer.php'; ?>