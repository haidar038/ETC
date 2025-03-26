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

// Proses hapus produk (jika tombol hapus diklik)
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $delete_id AND tenant_id = $tenant_id");
    header("Location: product_list.php");
    exit();
}

// Ambil data produk milik tenant
$sql = "SELECT * FROM products WHERE tenant_id = $tenant_id ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="row">
    <div class="col-12">
        <h2 class="fw-bold text-primary mb-4">Kelola Produk</h2>
        <a href="add_product.php" class="btn btn-primary mb-3">Tambah Produk Baru</a>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Gambar</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['product_name']; ?></td>
                            <td><?= $row['description']; ?></td>
                            <td><?= number_format($row['price'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="../../public/assets/uploads/<?= $row['image']; ?>" alt="Gambar Produk"
                                        style="max-width:80px;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= $row['created_at']; ?></td>
                            <td>
                                <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="product_list.php?delete=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Belum ada produk yang ditambahkan.</div>
        <?php endif; ?>
    </div>
</div>


<?php include '../../templates/footer.php'; ?>