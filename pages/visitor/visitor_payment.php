<?php
include '../../config/config.php';
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qr_token'])) {
    $qr_token = $conn->real_escape_string($_POST['qr_token']);

    // Cek transaksi berdasarkan QR token
    $sqlCheck = "SELECT * FROM transactions WHERE qr_token = '$qr_token' AND status = 'waiting_payment'";
    $resultCheck = $conn->query($sqlCheck);

    if ($resultCheck->num_rows > 0) {
        $transaction = $resultCheck->fetch_assoc();
        $visitor_id = $transaction['user_id'];
        $totalPoin = $transaction['nominal'];

        // Cek saldo visitor
        $sqlBalance = "SELECT points FROM users WHERE id = $visitor_id";
        $resultBalance = $conn->query($sqlBalance);
        $user = $resultBalance->fetch_assoc();

        if ($user['points'] >= $totalPoin) {
            // Kurangi poin visitor dan update transaksi
            $newBalance = $user['points'] - $totalPoin;
            $conn->query("UPDATE users SET points = $newBalance WHERE id = $visitor_id");
            $conn->query("UPDATE transactions SET status = 'completed' WHERE id = {$transaction['id']}");

            echo "<div class='alert alert-success'>Pembayaran berhasil!</div>";
        } else {
            echo "<div class='alert alert-danger'>Saldo tidak mencukupi.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>QR tidak valid atau transaksi sudah diproses.</div>";
    }
}
