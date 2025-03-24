<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../includes/functions.php';
include '../../templates/header.php';

// Pastikan hanya tenant yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'tenant') {
    header("Location: ../login.php");
    exit();
}

$tenant_id = $_SESSION['user_id'];

// Proses generate token jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_token'])) {
    // Menggunakan fungsi generateQRToken() untuk membuat token unik, misalnya dengan panjang 10 karakter
    $new_token = generateQRToken(10);

    // Update token di database
    $sql = "UPDATE users SET catalog_token = '$new_token' WHERE id = $tenant_id";
    if ($conn->query($sql)) {
        $_SESSION['catalog_token'] = $new_token;
        $message = "Token berhasil digenerate.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Ambil data profil tenant
$sql = "SELECT * FROM users WHERE id = $tenant_id";
$result = $conn->query($sql);
$tenant = $result->fetch_assoc();

$catalog_token = $tenant['catalog_token'];
// Jika token tersedia, buat URL QR dengan API quickchart.io
$qr_url = $catalog_token ? "https://quickchart.io/qr?text=" . urlencode($catalog_token) . "&size=200" : "";
?>

<h2>Profil Tenant</h2>

<?php if (isset($message)) : ?>
<div class="alert alert-info"><?= $message; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <p><strong>Nama:</strong> <?= $tenant['name']; ?></p>
        <p><strong>Email:</strong> <?= $tenant['email']; ?></p>
        <p>
            <strong>Tenant Token (Catalog Token):</strong>
            <?= $catalog_token ? $catalog_token : '<span class="text-danger">Belum ada token, silakan generate token</span>'; ?>
        </p>
        <?php if ($catalog_token) : ?>
        <p><strong>QR Code:</strong></p>
        <img src="<?= $qr_url; ?>" alt="QR Code" class="img-fluid">
        <?php endif; ?>
        <form method="POST" class="mt-3">
            <button type="submit" name="generate_token" class="btn btn-primary">
                <?= $catalog_token ? "Regenerate Token" : "Generate Token" ?>
            </button>
        </form>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>