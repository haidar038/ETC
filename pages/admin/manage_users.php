<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil semua data user
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<h2>Kelola Users</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Jenis User</th>
            <th>Dibuat</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['name']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= ucfirst($row['user_type']); ?></td>
            <td><?= $row['created_at']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../../templates/footer.php'; ?>