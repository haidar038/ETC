<?php
// pages/visitor/checkout.php

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
    // Ambil data cart dan tenant_token
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

    // Hitung total nominal pembelian dalam rupiah
    // Misal: harga produk disimpan dalam rupiah di tabel products, namun pada cart kita menggunakan konversi ke poin.
    // Untuk checkout, kita simpan nominal asli (dalam rupiah).
    $totalNominal = 0;
    foreach ($cartData as $item) {
        // Pastikan 'price' disimpan dalam rupiah di cart. Jika 'price' di cart adalah harga dalam poin, maka:
        // $totalNominal += $item['price'] * $item['quantity'] * 1000;
        // Namun, jika harga sudah disimpan dalam rupiah di cart, cukup:
        $totalNominal += $item['price'] * $item['quantity'] * 1000;
    }

    // Buat transaksi checkout dengan status pending dan tipe 'purchase'
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, tenant_id, nominal, transaction_type, status) VALUES (?, ?, ?, 'purchase', 'pending')");
    $stmt->bind_param("iii", $user_id, $tenant['id'], $totalNominal);
    if ($stmt->execute()) {
        // Dapatkan ID transaksi yang baru dibuat
        $transaction_id = $stmt->insert_id;
        $stmt->close();

        // Untuk mencegah double submit, lakukan redirect ke halaman receipt dengan ID transaksi sebagai parameter
        header("Location: checkout_receipt.php?transaction_id=" . $transaction_id);
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal membuat transaksi: " . $stmt->error . "</div>";
        $stmt->close();
    }
} else {
    echo "<div class='alert alert-danger'>Data checkout tidak lengkap.</div>";
}

include '../../templates/footer.php';
