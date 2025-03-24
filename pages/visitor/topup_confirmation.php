<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../includes/functions.php';
include '../../templates/header.php';

// Pastikan hanya visitor yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

// Cek apakah data topup dari proses sebelumnya ada di session
if (!isset($_SESSION['topup_success'])) {
    echo "<div class='alert alert-info'>Tidak ada data topup yang dapat ditampilkan.</div>";
    include '../../templates/footer.php';
    exit();
}

// Ambil data topup dari session
$topupData = $_SESSION['topup_success'];
$qr_token = $topupData['qr_token'];
$nominal  = $topupData['nominal'];

// Hapus data topup dari session agar tidak terpakai lagi jika halaman di-refresh
unset($_SESSION['topup_success']);

// URL QR menggunakan API quickchart.io
$qr_url = "https://quickchart.io/qr?text=" . $qr_token . "&size=200";
?>

<h2>Konfirmasi Topup</h2>
<div class="alert alert-success">
    Topup berhasil diproses. Silakan tunjukkan QR berikut ke admin untuk verifikasi.
</div>

<div class="mb-3">
    <p><strong>Nominal Topup:</strong> Rp <?= number_format($nominal, 0, ',', '.'); ?></p>
    <p><strong>QR Token:</strong> <?= $qr_token; ?></p>
</div>
<div class="mb-3">
    <img src="<?= $qr_url; ?>" alt="QR Code" class="img-fluid">
</div>

<a href="topup.php" class="btn btn-primary">Kembali ke Halaman Topup</a>

<?php include '../../templates/footer.php'; ?>