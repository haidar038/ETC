<?php
// pages/tenant/order_list.php

include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';
include '../../includes/functions.php';

// Pastikan hanya tenant yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'tenant') {
    header("Location: ../login.php");
    exit();
}

$tenant_id = $_SESSION['user_id'];

// Proses konfirmasi pesanan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_transaction_id'])) {
    $transaction_id = intval($_POST['confirm_transaction_id']);
    // Generate QR Payment token
    $qr_token = generateQRToken();
    // Update transaksi: set status menjadi 'verified' dan simpan QR token
    $stmt = $conn->prepare("UPDATE transactions SET status='verified', qr_token=? WHERE id=? AND tenant_id=?");
    $stmt->bind_param("sii", $qr_token, $transaction_id, $tenant_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Pesanan telah dikonfirmasi. QR Payment telah dibuat.</div>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengkonfirmasi pesanan: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Ambil daftar transaksi pembelian yang statusnya pending (belum dikonfirmasi)
$sql = "SELECT t.*, u.name AS visitor_name 
        FROM transactions t 
        LEFT JOIN users u ON t.user_id = u.id 
        WHERE t.tenant_id = $tenant_id AND t.transaction_type = 'purchase' AND t.status IN ('pending', 'verified')
        ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);
?>

<h2>Daftar Pesanan (Pending)</h2>
<?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Visitor</th>
                <th>Nominal (Rp)</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['visitor_name']; ?></td>
                    <td><?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                    <td><?= ucfirst($row['status']); ?></td>
                    <td><?= $row['transaction_date']; ?></td>
                    <td>
                        <?php if ($row['status'] == 'pending'): ?>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="confirm_transaction_id" value="<?= $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Konfirmasi Pesanan</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($row['status'] == 'verified'): ?>
                            <?php $qr_url = "https://quickchart.io/qr?text=" . urlencode($row['qr_token']) . "&size=200"; ?>
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#qrModal<?= $row['id']; ?>">
                                Lihat QR Payment
                            </button>

                            <!-- Modal QR Payment -->
                            <div class="modal fade" id="qrModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="qrModalLabel<?= $row['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="qrModalLabel<?= $row['id']; ?>">QR Payment</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="<?= $qr_url; ?>" alt="QR Payment" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class='alert alert-info'>Tidak ada pesanan pending.</div>
<?php endif; ?>

<?php include '../../templates/footer.php'; ?>