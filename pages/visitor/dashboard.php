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
$sql    = "SELECT * FROM users WHERE id=$user_id";
$result = $conn->query($sql);
$user   = $result->fetch_assoc();
?>
<h2>Visitor Dashboard</h2>
<p>Selamat datang, <?= $user['name']; ?>!</p>
<p>Poin kamu saat ini: <?= $user['points'] ?? 0; ?></p>

<ul>
    <li><a href="topup.php">Top Up Poin</a></li>
    <li><a href="transactions.php">Transaksi / Pembelian</a></li>
</ul>

<?php include '../../templates/footer.php'; ?>