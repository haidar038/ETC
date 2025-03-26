<?php
// pages/receipt.php

include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

// Pastikan user sudah login (bisa visitor, tenant, atau admin)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['transaction_id'])) {
    echo "<div class='alert alert-danger'>Transaksi tidak ditemukan.</div>";
    include '../templates/footer.php';
    exit();
}

$transaction_id = intval($_GET['transaction_id']);

// Query transaksi dengan join pada tenant (jika purchase)
$sql = "SELECT t.*, 
               u.name AS user_name,
               IFNULL(tenant.name, '-') AS tenant_name 
        FROM transactions t 
        LEFT JOIN users u ON t.user_id = u.id 
        LEFT JOIN users tenant ON t.tenant_id = tenant.id 
        WHERE t.id = ? AND t.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $transaction_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Transaksi tidak ditemukan.</div>";
    include '../templates/footer.php';
    exit();
}
$transaction = $result->fetch_assoc();
$stmt->close();
?>

<h2 class="mb-4">Receipt Transaksi</h2>
<div class="card">
    <div class="card-header">
        Detail Transaksi
    </div>
    <div class="card-body">
        <p><strong>ID Transaksi:</strong> <?= $transaction['id']; ?></p>
        <p><strong>User:</strong> <?= htmlspecialchars($transaction['user_name']); ?></p>
        <?php if ($transaction['transaction_type'] == 'purchase'): ?>
            <p><strong>Tenant:</strong> <?= htmlspecialchars($transaction['tenant_name']); ?></p>
        <?php endif; ?>
        <p><strong>Jenis Transaksi:</strong> <?= ucfirst($transaction['transaction_type']); ?></p>
        <p><strong>Nominal:</strong> Rp <?= number_format($transaction['nominal'], 0, ',', '.'); ?></p>
        <p><strong>Status:</strong> <?= ucfirst($transaction['status']); ?></p>
        <p><strong>Tanggal Transaksi:</strong> <?= $transaction['transaction_date']; ?></p>
        <?php if (!empty($transaction['qr_token'])): ?>
            <p><strong>QR Token:</strong> <?= $transaction['qr_token']; ?></p>
            <?php $qr_url = "https://quickchart.io/qr?text=" . urlencode($transaction['qr_token']) . "&size=200"; ?>
            <p><strong>QR Code:</strong></p>
            <img src="<?= $qr_url; ?>" alt="QR Code" class="img-fluid">
        <?php endif; ?>
    </div>
</div>
<div class="mt-3">
    <a href="transactions.php" class="btn btn-primary">Kembali ke Riwayat Transaksi</a>
</div>

<?php include '../templates/footer.php'; ?>