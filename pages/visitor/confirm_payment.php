<?php
// pages/visitor/confirm_payment.php

include '../../config/config.php';
include '../../config/database.php';
include '../../includes/functions.php';
include '../../templates/header.php';

// Pastikan hanya visitor yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses POST: Ketika visitor mengirim token hasil scan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qr_token'])) {
    $qr_token = trim($_POST['qr_token']);
    if (empty($qr_token)) {
        echo "<div class='alert alert-danger'>Token QR tidak valid.</div>";
    } else {
        // Cari transaksi dengan tipe 'purchase', status 'verified', dan sesuai user
        $sql = "SELECT * FROM transactions 
                WHERE qr_token = '$qr_token' AND transaction_type = 'purchase' AND status = 'verified' AND user_id = $user_id";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $transaction = $result->fetch_assoc();
            // Hitung required points (konversi: 1 poin = Rp1000)
            $requiredPoints = $transaction['nominal'] / 1000;
            $resultUser = $conn->query("SELECT points FROM users WHERE id = $user_id");
            $user = $resultUser->fetch_assoc();
            if ($user['points'] >= $requiredPoints) {
                // Deduct points dan update transaksi menjadi completed
                $conn->query("UPDATE users SET points = points - $requiredPoints WHERE id = $user_id");
                $conn->query("UPDATE transactions SET status = 'completed' WHERE id = " . $transaction['id']);
                echo "<div class='alert alert-success'>Pembelian berhasil. Poin telah dikurangi.</div>";
            } else {
                echo "<div class='alert alert-danger'>Poin tidak mencukupi.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Token QR tidak valid atau transaksi tidak ditemukan.</div>";
        }
    }
}
?>

<h2 class="fw-bold text-primary mb-4">Konfirmasi Pembayaran</h2>
<p>Silakan scan QR Payment yang diberikan oleh tenant untuk menyelesaikan pembelian.</p>

<div id="reader" style="width:100%; max-width:400px; aspect-ratio: 4/3; border:1px solid #ddd;"></div>
<div class="mt-2">
    <button id="startCamera" class="btn btn-secondary">Start Camera</button>
    <button id="stopCamera" class="btn btn-secondary" style="display:none;">Stop Camera</button>
</div>

<!-- Form tersembunyi untuk mengirim token hasil scan -->
<form id="qrForm" method="POST" style="display:none;">
    <input type="hidden" name="qr_token" id="qr_token">
</form>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrcodeScanner;
    let scannerActive = false;

    document.getElementById('startCamera').addEventListener('click', function() {
        if (!scannerActive) {
            document.getElementById('qrForm').style.display = "block";
            html5QrcodeScanner = new Html5Qrcode("reader");
            const config = {
                fps: 10,
                qrbox: 250
            };
            html5QrcodeScanner.start({
                facingMode: "environment"
            }, config, onScanSuccess, onScanFailure);
            scannerActive = true;
            document.getElementById('stopCamera').style.display = "inline-block";
            this.disabled = true;
        }
    });

    document.getElementById('stopCamera').addEventListener('click', function() {
        if (scannerActive && html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                scannerActive = false;
                document.getElementById('startCamera').disabled = false;
                this.style.display = "none";
            }).catch(err => {
                console.error("Gagal menghentikan scanner: " + err);
            });
        }
    });

    function onScanSuccess(decodedText, decodedResult) {
        html5QrcodeScanner.stop().then(() => {
            scannerActive = false;
            document.getElementById('startCamera').disabled = false;
            document.getElementById('stopCamera').style.display = "none";
            document.getElementById('qr_token').value = decodedText;
            document.getElementById('qrForm').submit();
        }).catch(err => {
            console.error("Gagal menghentikan scanner: " + err);
        });
    }

    function onScanFailure(error) {
        // Error scan bisa diabaikan
    }
</script>

<?php include '../../templates/footer.php'; ?>