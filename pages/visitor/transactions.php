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

// Ambil transaksi pembelian dengan tipe 'purchase' yang dilakukan oleh visitor
$sql = "SELECT t.*, u.name AS tenant_name 
        FROM transactions t 
        LEFT JOIN users u ON t.tenant_id = u.id 
        WHERE t.user_id = $user_id AND t.transaction_type = 'purchase'
        ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);
?>

<a href="dashboard.php" class="btn btn-sm btn-secondary mb-4">&larr; Kembali</a>

<h2 class="fw-bold text-primary mb-4">Transaksi / Pembelian</h2>

<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tenant</th>
                <th>Nominal (poin)</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['tenant_name'] ? $row['tenant_name'] : '-'; ?></td>
                    <td><?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                    <td><?= ucfirst($row['status']); ?></td>
                    <td><?= $row['transaction_date']; ?></td>
                    <td>
                        <?php if ($row['transaction_type'] == 'purchase' && $row['status'] == 'verified'): ?>
                            <a href="confirm_payment.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Konfirmasi Pembayaran</a>
                        <?php elseif ($row['status'] == 'completed'): ?>
                            <a href="receipt.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-success">Lihat Bukti Pembayaran</a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled>Menunggu Konfirmasi</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info">Belum ada transaksi pembelian.</div>
<?php endif; ?>

<?php include '../../templates/footer.php'; ?>