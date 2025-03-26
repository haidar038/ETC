<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';
include '../../includes/functions.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'] ?? '';

    if ($method == 'qr_scan') {
        // Proses dari scan QR otomatis
        $qr_token = trim($_POST['qr_token'] ?? '');
        if (empty($qr_token)) {
            $message = "<div class='alert alert-danger'>Gagal membaca token dari QR.</div>";
        } else {
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
        // Metode Input Manual
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
                    $message = "<div class='alert alert-success'>Transaksi topup berhasil dibuat untuk visitor <strong>{$user['name']}</strong>.<br>
                                QR Code untuk verifikasi:<br>
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

<div class="row">
    <!-- Kolom Kiri: Kamera untuk Scan QR -->
    <div class="col-md-4">
        <h4>Scan QR (Verifikasi Topup)</h4>
        <!-- Container kamera dengan aspect ratio 4:3 -->
        <div id="reader" style="width:100%; aspect-ratio: 4/3; border:1px solid #ddd;"></div>
        <div class="mt-2">
            <button id="startCamera" class="btn btn-secondary">Start Camera</button>
            <button id="stopCamera" class="btn btn-secondary" style="display:none;">Stop Camera</button>
        </div>
        <!-- Form tersembunyi untuk mengirim token hasil scan -->
        <form id="qrForm" method="POST" style="display:none;">
            <input type="hidden" name="method" value="qr_scan">
            <input type="hidden" name="qr_token" id="qr_token">
        </form>
    </div>

    <!-- Kolom Kanan: Input Manual dengan Username Lookup -->
    <div class="col-md-8">
        <h4>Input Manual Topup</h4>
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

<!-- Sertakan library Html5Qrcode -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // Variabel untuk mengelola scanner
    let html5QrcodeScanner;
    let scannerActive = false;

    // Fungsi untuk memulai kamera
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

    // Fungsi untuk menghentikan kamera
    document.getElementById('stopCamera').addEventListener('click', function() {
        if (scannerActive && html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                console.log("Scanner stopped.");
                scannerActive = false;
                document.getElementById('startCamera').disabled = false;
                this.style.display = "none";
            }).catch(err => {
                console.error("Gagal menghentikan scanner: " + err);
            });
        }
    });

    // Callback ketika QR berhasil di-scan
    function onScanSuccess(decodedText, decodedResult) {
        // Hentikan scanner secara otomatis
        html5QrcodeScanner.stop().then(() => {
            console.log("Scanner stopped after success.");
            scannerActive = false;
            document.getElementById('startCamera').disabled = false;
            document.getElementById('stopCamera').style.display = "none";
            // Simpan token ke input tersembunyi
            document.getElementById('qr_token').value = decodedText;
            // Langsung submit form tanpa tombol trigger manual
            document.getElementById('qrForm').submit();
        }).catch(err => {
            console.error("Gagal menghentikan scanner: " + err);
        });
    }

    // Callback jika scan gagal (tidak perlu menampilkan error terus-menerus)
    function onScanFailure(error) {
        // Dapat diabaikan atau digunakan untuk debugging
        // console.warn("Scan failure: " + error);
    }

    // AJAX Username Lookup (Metode Manual)
    document.getElementById('username').addEventListener('keyup', function() {
        const username = this.value.trim();
        const lookupDiv = document.getElementById('usernameLookup');

        if (username.length === 0) {
            lookupDiv.innerHTML = "";
            return;
        }

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