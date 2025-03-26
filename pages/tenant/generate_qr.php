<?php
include '../../config/config.php';
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = intval($_POST['transaction_id']);

    // Generate QR token
    $qr_token = uniqid('pay_');

    // Update transaksi dengan QR token
    $sqlUpdate = "UPDATE transactions SET status='waiting_payment', qr_token='$qr_token' WHERE id=$transaction_id";

    if ($conn->query($sqlUpdate)) {
        echo "<h3>QR untuk Pembayaran</h3>";
        echo "<img src='https://quickchart.io/qr?text=$qr_token&size=200' />";
        echo "<p>Silakan tunjukkan QR ini ke visitor untuk pembayaran.</p>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupdate transaksi.</div>";
    }
}
