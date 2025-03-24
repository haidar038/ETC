<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<h2>Verifikasi Topup oleh Admin</h2>
<form method="POST">
    <div class="mb-3">
        <label>Masukkan Token QR Topup</label>
        <input type="text" name="qr_token" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Verifikasi</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qr_token = $_POST['qr_token'];
    // Cari transaksi topup yang pending dengan token tersebut
    $sql = "SELECT * FROM transactions WHERE qr_token='$qr_token' AND (transaction_type='topup_mandiri' OR transaction_type='topup_admin') AND status='pending'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        // Konversi: Rp1000 = 1 poin
        $points = $transaction['nominal'] / 1000;
        $conn->query("UPDATE users SET points = points + $points WHERE id = " . $transaction['user_id']);
        $conn->query("UPDATE transactions SET status='completed' WHERE id = " . $transaction['id']);
        echo "<div class='alert alert-success'>Topup berhasil diverifikasi dan poin telah ditambahkan.</div>";
    } else {
        echo "<div class='alert alert-danger'>Token QR tidak valid atau transaksi sudah diverifikasi.</div>";
    }
}
include '../../templates/footer.php';
?>