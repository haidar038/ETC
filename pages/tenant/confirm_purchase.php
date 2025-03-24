<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../includes/functions.php';
include '../../templates/header.php';

// Pastikan hanya tenant yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'tenant') {
    header("Location: ../login.php");
    exit();
}

// Ambil transaksi pembelian yang pending untuk tenant ini
$tenant_id = $_SESSION['user_id'];
$sql = "SELECT * FROM transactions WHERE tenant_id = $tenant_id AND transaction_type='purchase' AND status='pending'";
$result = $conn->query($sql);
?>
<h2>Konfirmasi Pembelian</h2>
<?php if ($result->num_rows > 0): ?>
<table class="table">
    <thead>
        <tr>
            <th>ID Transaksi</th>
            <th>ID Visitor</th>
            <th>Poin</th>
            <th>Token QR</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['user_id']; ?></td>
            <td><?= $row['nominal']; ?></td>
            <td><?= $row['qr_token']; ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="transaction_id" value="<?= $row['id']; ?>">
                    <button type="submit" name="confirm" class="btn btn-success btn-sm">Konfirmasi</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<div class="alert alert-info">Tidak ada transaksi yang menunggu konfirmasi.</div>
<?php endif; ?>

<?php
// Proses konfirmasi oleh tenant
if (isset($_POST['confirm'])) {
    $transaction_id = $_POST['transaction_id'];
    // Update status transaksi menjadi verified
    $conn->query("UPDATE transactions SET status='verified' WHERE id = $transaction_id");

    // Ambil token QR dari transaksi yang sudah dikonfirmasi
    $sqlToken = "SELECT qr_token FROM transactions WHERE id = $transaction_id";
    $resToken = $conn->query($sqlToken);
    $dataToken = $resToken->fetch_assoc();
    $qr_token = $dataToken['qr_token'];

    // Tampilkan QR code untuk pembayaran
    $qr_url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$qr_token&choe=UTF-8";
    echo "<div class='alert alert-success'>Transaksi dikonfirmasi. Tunjukkan QR ini kepada visitor:</div>";
    echo "<img src='$qr_url' alt='QR Code'>";
}
?>

<?php include '../../templates/footer.php'; ?>