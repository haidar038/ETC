<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';
include '../../includes/functions.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'] ?? '';

    if ($method == 'qr_scan') {
        // Metode: Scan QR menggunakan kamera (token diambil dari scanner)
        $qr_token = trim($_POST['qr_token'] ?? '');
        if (empty($qr_token)) {
            $message = "<div class='alert alert-danger'>Gagal membaca token dari QR.</div>";
        } else {
            // Cari transaksi topup yang pending dengan token tersebut
            $sql = "SELECT * FROM transactions WHERE qr_token = '$qr_token' AND (transaction_type='topup_mandiri' OR transaction_type='topup_admin') AND status='pending'";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $transaction = $result->fetch_assoc();
                // Konversi: Rp1000 = 1 poin
                $points = $transaction['nominal'] / 1000;
                $conn->query("UPDATE users SET points = points + $points WHERE id = " . $transaction['user_id']);
                $conn->query("UPDATE transactions SET status='completed' WHERE id = " . $transaction['id']);
                $message = "<div class='alert alert-success'>Topup berhasil diverifikasi dan poin telah ditambahkan.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Token QR tidak valid atau transaksi sudah diverifikasi.</div>";
            }
        }
    } elseif ($method == 'manual_input') {
        // Metode: Input Manual dengan lookup username
        $username = trim($_POST['username'] ?? '');
        $nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);
        if (empty($username) || $nominal === false || $nominal <= 0) {
            $message = "<div class='alert alert-danger'>Data tidak valid. Pastikan username dan nominal sudah diisi dengan benar.</div>";
        } else {
            // Cari visitor berdasarkan username
            $stmt = $conn->prepare("SELECT id, name FROM users WHERE username = ? AND user_type = 'visitor'");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $resultUser = $stmt->get_result();
            if ($resultUser->num_rows > 0) {
                $user = $resultUser->fetch_assoc();
                $user_id = $user['id'];
                // Generate token QR untuk transaksi manual
                $qr_token = generateQRToken();
                $transaction_type = 'topup_admin';
                $stmtInsert = $conn->prepare("INSERT INTO transactions (user_id, nominal, transaction_type, status, qr_token) VALUES (?, ?, ?, 'pending', ?)");
                $stmtInsert->bind_param("iiss", $user_id, $nominal, $transaction_type, $qr_token);
                if ($stmtInsert->execute()) {
                    // Tampilkan QR Code untuk verifikasi oleh visitor
                    $qr_url = "https://quickchart.io/qr?text=" . urlencode($qr_token) . "&size=200";
                    $message = "<div class='alert alert-success'>Transaksi topup berhasil dibuat untuk visitor <strong>{$user['name']}</strong>. QR Code untuk verifikasi:<br>
                                <img src='$qr_url' alt='QR Code' class='img-fluid'></div>";
                } else {
                    $message = "<div class='alert alert-danger'>Gagal membuat transaksi: " . $stmtInsert->error . "</div>";
                }
                $stmtInsert->close();
            } else {
                $message = "<div class='alert alert-danger'>Visitor dengan username '$username' tidak ditemukan.</div>";
            }
            $stmt->close();
        }
    }
}
?>

<h2 class="mb-4 text-primary fw-bold">Topup oleh Admin</h2>

<?php if (!empty($message)) echo $message; ?>

<!-- Gunakan Bootstrap Tabs untuk memilih metode topup -->
<ul class="nav nav-tabs" id="topupTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="qr-tab" data-bs-toggle="tab" data-bs-target="#qr" type="button" role="tab" aria-controls="qr" aria-selected="true">
            Verifikasi Scan QR
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false">
            Input Manual
        </button>
    </li>
</ul>
<div class="tab-content" id="topupTabContent">
    <!-- Tab 1: Verifikasi Scan QR -->
    <div class="tab-pane fade show active p-3" id="qr" role="tabpanel" aria-labelledby="qr-tab">
        <!-- Elemen untuk scan QR menggunakan kamera -->
        <div id="reader" style="width:300px;"></div>
        <!-- Form tersembunyi untuk mengirim token hasil scan -->
        <form id="qrForm" method="POST" class="mt-3">
            <input type="hidden" name="method" value="qr_scan">
            <input type="hidden" name="qr_token" id="qr_token">
            <button type="submit" class="btn btn-primary">Verifikasi Hasil Scan</button>
        </form>
    </div>
    <!-- Tab 2: Input Manual dengan AJAX Username Lookup -->
    <div class="tab-pane fade p-3" id="manual" role="tabpanel" aria-labelledby="manual-tab">
        <form method="POST" id="manualForm">
            <input type="hidden" name="method" value="manual_input">
            <div class="mb-3">
                <label>Username Visitor</label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="Masukkan username visitor">
                <div id="usernameLookup" class="mt-1"></div>
            </div>
            <div class="mb-3">
                <label>Nominal Topup (Rp)</label>
                <input type="number" name="nominal" class="form-control" required placeholder="Masukkan nominal topup">
            </div>
            <button type="submit" class="btn btn-primary">Buat Transaksi dan Tampilkan QR</button>
        </form>
    </div>
</div>

<!-- Sertakan library Html5Qrcode untuk scan QR -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // ===================
    // Metode Scan QR
    // ===================
    function onScanSuccess(decodedText, decodedResult) {
        // Isi input tersembunyi dengan token yang di-scan
        document.getElementById('qr_token').value = decodedText;
        // Berhenti scan setelah token ditemukan
        html5QrcodeScanner.clear().then(_ => {
            console.log("Scanner cleared.");
        }).catch(error => {
            console.error("Gagal menghentikan scanner.", error);
        });
    }

    function onScanFailure(error) {
        // Tidak perlu menampilkan error setiap saat
    }

    let html5QrcodeScanner = new Html5Qrcode("reader");
    const config = {
        fps: 10,
        qrbox: 250
    };

    html5QrcodeScanner.start({
        facingMode: "environment"
    }, config, onScanSuccess, onScanFailure);

    // ===================
    // AJAX Username Lookup (Metode Manual)
    // ===================

    document.getElementById('username').addEventListener('keyup', function() {
        const username = this.value.trim();
        const lookupDiv = document.getElementById('usernameLookup');

        if (username.length === 0) {
            lookupDiv.innerHTML = "";
            return;
        }

        // Menggunakan Fetch API untuk AJAX lookup
        fetch('lookup_username.php?username=' + encodeURIComponent(username))
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    lookupDiv.innerHTML = "<small class='text-success'>Ditemukan: " + data.name + " (ID: " + data.id + ")</small>";
                } else {
                    lookupDiv.innerHTML = "<small class='text-danger'>Username tidak ditemukan.</small>";
                }
            })
            .catch(error => {
                console.error("Error lookup:", error);
                lookupDiv.innerHTML = "";
            });
    });
</script>

<?php include '../../templates/footer.php'; ?>