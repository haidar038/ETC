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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cartData']) && isset($_POST['tenant_token'])) {
    // Ambil data cart yang dikirim dalam bentuk JSON
    $cartData = json_decode($_POST['cartData'], true);
    $tenant_token = $conn->real_escape_string($_POST['tenant_token']);

    if (!$cartData || count($cartData) == 0) {
        echo "<div class='alert alert-danger'>Cart kosong.</div>";
        include '../../templates/footer.php';
        exit();
    }

    // Cari tenant berdasarkan tenant_token
    $sqlTenant = "SELECT * FROM users WHERE catalog_token = '$tenant_token' AND user_type = 'tenant'";
    $resultTenant = $conn->query($sqlTenant);
    if ($resultTenant->num_rows == 0) {
        echo "<div class='alert alert-danger'>Tenant tidak ditemukan.</div>";
        include '../../templates/footer.php';
        exit();
    }
    $tenant = $resultTenant->fetch_assoc();

    // Hitung total poin yang harus dibayar
    $totalPoin = 0;
    foreach ($cartData as $item) {
        $totalPoin += $item['price'] * $item['quantity'];
    }

    // Tampilkan ringkasan pembelian dan tombol konfirmasi
?>
<h2>Checkout Pembelian</h2>
<h4>Tenant: <?= $tenant['name']; ?></h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Harga per pcs (poin)</th>
            <th>Subtotal (poin)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cartData as $item): ?>
        <tr>
            <td><?= $item['name']; ?></td>
            <td><?= $item['quantity']; ?></td>
            <td><?= $item['price']; ?></td>
            <td><?= $item['price'] * $item['quantity']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p class="text-end"><strong>Total Poin: <?= $totalPoin; ?></strong></p>
<!-- Form untuk konfirmasi pembelian -->
<form method="POST" action="process_purchase.php">
    <!-- Kirim data pembelian (misalnya sebagai JSON) -->
    <input type="hidden" name="cartData" value='<?= json_encode($cartData); ?>'>
    <input type="hidden" name="tenant_id" value="<?= $tenant['id']; ?>">
    <input type="hidden" name="totalPoin" value="<?= $totalPoin; ?>">
    <button type="submit" class="btn btn-primary">Konfirmasi Pembelian</button>
</form>
<?php
} else {
    echo "<div class='alert alert-danger'>Data checkout tidak lengkap.</div>";
}

include '../../templates/footer.php';
?>