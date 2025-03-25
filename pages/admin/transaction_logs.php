<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Ambil seluruh transaksi dan join dengan data user (visitor)
$sql = "SELECT t.*, u.name as user_name FROM transactions t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);
?>

<h2 class="fw-bold text-primary mb-4">Riwayat Transaksi</h2>
<!-- Export button -->
<div class="mb-3">
    <a href="export_transactions.php?format=excel" class="btn btn-success">Export to Excel</a>
    <a href="export_transactions.php?format=pdf" class="btn btn-danger">Export to PDF</a>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Visitor</th>
            <th>Tenant</th>
            <th>Nominal</th>
            <th>Tipe Transaksi</th>
            <th>Status</th>
            <th>QR Token</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        <?php $rowNumber = 1; // Inisialisasi nomor urut 
        ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $rowNumber++; ?></td>
                <td><?= $row['user_name']; ?></td>
                <td><?= $row['tenant_id'] ? $row['tenant_id'] : '-'; ?></td>
                <td><?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                <td><?= ucfirst($row['transaction_type']); ?></td>
                <td>
                    <?php
                    $statusClass = '';
                    switch (strtolower($row['status'])) {
                        case 'pending':
                            $statusClass = 'bg-warning';
                            break;
                        case 'verified':
                            $statusClass = 'bg-info';
                            break;
                        case 'completed':
                            $statusClass = 'bg-success';
                            break;
                        default:
                            $statusClass = 'bg-secondary';
                    }
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= ucfirst($row['status']); ?></span>
                </td>
                <td><?= $row['qr_token']; ?></td>
                <td><?= $row['transaction_date']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../../templates/footer.php'; ?>