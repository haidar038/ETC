<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya tenant yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'tenant') {
    header("Location: ../login.php");
    exit();
}

$tenant_id = $_SESSION['user_id'];

// Ambil semua transaksi pending
$sqlOrders = "SELECT * FROM transactions WHERE tenant_id = $tenant_id AND status = 'pending'";
$resultOrders = $conn->query($sqlOrders);
?>
<h2>Pesanan Pending</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Visitor</th>
            <th>Detail Pesanan</th>
            <th>Total Poin</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = $resultOrders->fetch_assoc()) : ?>
            <tr>
                <td><?= $order['id']; ?></td>
                <td><?= $order['user_id']; ?></td>
                <td><?= htmlspecialchars($order['order_data']); ?></td>
                <td><?= $order['nominal']; ?> Poin</td>
                <td>
                    <form method="POST" action="generate_qr.php">
                        <input type="hidden" name="transaction_id" value="<?= $order['id']; ?>">
                        <button type="submit" class="btn btn-primary">Konfirmasi & Generate QR</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../../templates/footer.php'; ?>