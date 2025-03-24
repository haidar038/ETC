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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $description  = $conn->real_escape_string($_POST['description']);
    $price        = intval($_POST['price']);

    // Proses upload file gambar
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            echo "<div class='alert alert-danger'>Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.</div>";
            exit();
        }

        // Validasi ukuran file (misalnya 2MB)
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            echo "<div class='alert alert-danger'>Ukuran file terlalu besar. Maksimal 2MB.</div>";
            exit();
        }

        // Tentukan direktori upload
        $upload_dir = '../../public/assets/uploads/';
        // Buat nama file unik
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;
        // Validasi file (opsional: cek tipe file, ukuran, dsb.)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                echo "<div class='alert alert-danger'>Gagal mengupload gambar.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.</div>";
            include '../../templates/footer.php';
            exit();
        }
    }

    $sql = "INSERT INTO products (tenant_id, product_name, description, price, image) 
            VALUES ($tenant_id, '$product_name', '$description', $price, '$image_name')";
    if ($conn->query($sql)) {
        header("Location: product_list.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<h2>Tambah Produk Baru</h2>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" name="product_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label>Harga (dalam rupiah)</label>
        <input type="number" name="price" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Upload Gambar</label>
        <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Simpan Produk</button>
    <a href="product_list.php" class="btn btn-secondary">Batal</a>
</form>

<?php include '../../templates/footer.php'; ?>