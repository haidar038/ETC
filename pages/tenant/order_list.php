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
$sql    = "SELECT * FROM transactions WHERE tenant_id = $user_id";
$result = $conn->query($sql);
?>
<h2>Daftar Order</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID Order</th>
            <th>ID Visitor</th>
            <th>Poin</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['user_id']; ?></td>
            <td><?= $row['nominal']; ?></td>
            <td><?= $row['transaction_date']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php include '../../templates/footer.php'; ?>