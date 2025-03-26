<?php
include '../../config/config.php';
include '../../config/database.php';

// Pastikan hanya visitor yang dapat mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cartData']) && isset($_POST['tenant_id']) && isset($_POST['totalPoin'])) {
    $cartData = $_POST['cartData']; // JSON string
    $tenant_id = intval($_POST['tenant_id']);
    $totalPoin = intval($_POST['totalPoin']); // Total harga dalam poin (hasil konversi dari rupiah/1000)

    // Cek apakah visitor memiliki poin yang cukup
    $resultUser = $conn->query("SELECT points FROM users WHERE id = $user_id");
    $user = $resultUser->fetch_assoc();
    if ($user['points'] < $totalPoin) {
        echo "Poin tidak mencukupi.";
        exit();
    }

    // Hitung nilai nominal asli dalam rupiah (misalnya 1 poin = Rp1000)
    $nominalRupiah = $totalPoin * 1000;

    // Kurangi poin visitor (satuan poin)
    $conn->query("UPDATE users SET points = points - $totalPoin WHERE id = $user_id");

    // Simpan transaksi dengan nilai nominal asli (dalam rupiah)
    $sql = "INSERT INTO transactions (user_id, tenant_id, nominal, transaction_type, status, transaction_date) 
            VALUES ($user_id, $tenant_id, $nominalRupiah, 'purchase', 'completed', NOW())";
    if ($conn->query($sql)) {
        echo "Pembelian berhasil. Total nominal transaksi: Rp " . number_format($nominalRupiah, 0, ',', '.');
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Data pembelian tidak lengkap.";
}
