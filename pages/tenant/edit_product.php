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
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data produk
$sql = "SELECT * FROM products WHERE id = $product_id AND tenant_id = $tenant_id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
    include '../../templates/footer.php';
    exit();
}
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $description  = $conn->real_escape_string($_POST['description']);
    $price        = intval($_POST['price']);

    // Proses upload gambar jika ada file baru
    $image_name = $product['image']; // default menggunakan gambar lama
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../public/assets/uploads/';
        $new_image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $new_image_name;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_name = $new_image_name;
                // (Optional) Hapus file lama jika diperlukan
            } else {
                echo "<div class='alert alert-danger'>Gagal mengupload gambar.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.</div>";
            include '../../templates/footer.php';
            exit();
        }
    }

    $sqlUpdate = "UPDATE products 
                  SET product_name='$product_name', description='$description', price=$price, image='$image_name' 
                  WHERE id=$product_id AND tenant_id = $tenant_id";
    if ($conn->query($sqlUpdate)) {
        header("Location: product_list.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<h2>Edit Produk</h2>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" name="product_name" class="form-control" required value="<?= $product['product_name']; ?>">
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="description" class="form-control" rows="3"><?= $product['description']; ?></textarea>
    </div>
    <div class="mb-3">
        <label>Harga (dalam rupiah)</label>
        <input type="number" name="price" class="form-control" required value="<?= $product['price']; ?>">
    </div>
    <div class="mb-3">
        <label>Upload Gambar (biarkan kosong jika tidak ingin mengganti gambar)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        <?php if ($product['image']): ?>
        <p>Gambar saat ini:</p>
        <img src="../../public/assets/uploads/<?= $product['image']; ?>" alt="Gambar Produk" style="max-width:150px;">
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Update Produk</button>
    <a href="product_list.php" class="btn btn-secondary">Batal</a>
</form>

<?php include '../../templates/footer.php'; ?>