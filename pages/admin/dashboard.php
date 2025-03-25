<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Query statistik
$totalUsersResult = $conn->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $totalUsersResult->fetch_assoc()['total'];

$pendingTopupResult = $conn->query("SELECT COUNT(*) as total FROM transactions WHERE (transaction_type='topup_mandiri' OR transaction_type='topup_admin') AND status='pending'");
$pendingTopup = $pendingTopupResult->fetch_assoc()['total'];

$totalTransactionsResult = $conn->query("SELECT COUNT(*) as total FROM transactions");
$totalTransactions = $totalTransactionsResult->fetch_assoc()['total'];
?>

<h2 class="fw-bold text-primary mb-4">Dashboard Admin</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header"><i class="bi bi-people-fill"></i> Total Users</div>
            <div class="card-body">
                <h5 class="card-title"><?= $totalUsers; ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header"><i class="bi bi-hourglass-split"></i> Topup Pending</div>
            <div class="card-body">
                <h5 class="card-title"><?= $pendingTopup; ?></h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header"><i class="bi bi-cash-stack"></i> Total Transaksi</div>
            <div class="card-body">
                <h5 class="card-title"><?= $totalTransactions; ?></h5>
            </div>
        </div>
    </div>
</div>

<div class="list-group">
    <a href="manage_users.php" class="list-group-item list-group-item-action">Kelola Users</a>
    <a href="topup.php" class="list-group-item list-group-item-action">Topup</a>
    <a href="transaction_logs.php" class="list-group-item list-group-item-action">Riwayat Transaksi</a>
</div>

<?php include '../../templates/footer.php'; ?>