<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya tenant yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'tenant') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql    = "SELECT * FROM users WHERE id=$user_id";
$result = $conn->query($sql);
$user   = $result->fetch_assoc();
?>
<h2>Tenant Dashboard</h2>
<p>Selamat datang, <?= $user['name']; ?>!</p>
<p>Di sini kamu dapat mengelola produk dan melihat order yang masuk.</p>

<ul>
    <li><a href="product_list.php">Kelola Produk</a></li>
    <li><a href="order_list.php">Lihat Order</a></li>
</ul>
<?php include '../../templates/footer.php'; ?>