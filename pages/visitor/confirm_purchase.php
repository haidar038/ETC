<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya visitor yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_token = $_POST['qr_token'];
    $user_id = $_SESSION['user_id'];
    // Cari transaksi dengan token tersebut yang statusnya verified
    $sql = "SELECT * FROM transactions WHERE qr_token='$input_token' AND transaction_type='purchase' AND status='verified'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        // Cek apakah visitor memiliki poin yang cukup
        $points_result = $conn->query("SELECT points FROM users WHERE id = $user_id");
        $user = $points_result->fetch_assoc();
        if ($user['points'] >= $transaction['nominal']) {
            // Deduct poin dan update transaksi menjadi completed
            $conn->query("UPDATE users SET points = points - " . $transaction['nominal'] . " WHERE id = $user_id");
            $conn->query("UPDATE transactions SET status='completed' WHERE id = " . $transaction['id']);
            echo "<div class='alert alert-success'>Pembayaran dan pembelian selesai!</div>";
        } else {
            echo "<div class='alert alert-danger'>Poin tidak mencukupi.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Token QR tidak valid atau transaksi belum dikonfirmasi oleh tenant.</div>";
    }
}
?>

<h2>Konfirmasi Pembelian</h2>
<form method="POST">
    <div class="mb-3">
        <label>Masukkan Token QR</label>
        <input type="text" name="qr_token" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Konfirmasi Pembelian</button>
</form>
<?php include '../../templates/footer.php'; ?>