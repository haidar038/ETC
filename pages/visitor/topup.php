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

$user_id = $_SESSION['user_id'];

// Proses topup berdasarkan metode
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Topup Mandiri
    if (isset($_POST['nominal'])) {
        $nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);
        if ($nominal === false || $nominal <= 0) {
            echo "<div class='alert alert-danger'>Nominal topup tidak valid.</div>";
            exit();
        }
        $transaction_type = 'topup_mandiri';
        $qr_token = generateQRToken();
        $sql = "INSERT INTO transactions (user_id, nominal, transaction_type, status, qr_token) 
                VALUES ($user_id, $nominal, '$transaction_type', 'pending', '$qr_token')";
        if ($conn->query($sql)) {
            $_SESSION['topup_success'] = [
                'qr_token' => $qr_token,
                'nominal' => $nominal
            ];
            header("Location: topup_confirmation.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
    // Topup by Admin (verifikasi via scan QR oleh visitor)
    elseif (isset($_POST['method']) && $_POST['method'] == 'topup_by_admin') {
        $qr_token = trim($_POST['qr_token'] ?? '');
        if (empty($qr_token)) {
            echo "<div class='alert alert-danger'>Token QR tidak valid.</div>";
            exit();
        }
        $sql = "SELECT * FROM transactions 
                WHERE qr_token = '$qr_token' AND transaction_type = 'topup_admin' AND status = 'pending'";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $transaction = $result->fetch_assoc();
            // Konversi: Rp1000 = 1 poin
            $points = $transaction['nominal'] / 1000;
            $conn->query("UPDATE users SET points = points + $points WHERE id = " . $transaction['user_id']);
            $conn->query("UPDATE transactions SET status = 'completed' WHERE id = " . $transaction['id']);
            $_SESSION['topup_success'] = [
                'qr_token' => $qr_token,
                'nominal' => $transaction['nominal']
            ];
            header("Location: topup_confirmation.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Token QR tidak valid atau transaksi sudah diverifikasi.</div>";
        }
    }
}
?>

<h2 class="fw-bold text-primary mb-4">Top Up Poin</h2>

<div class="row mb-4">
    <div class="col-12 col-md-8">
        <!-- Form untuk Topup Mandiri -->
        <div>
            <h4>Topup Mandiri</h4>
            <form method="POST">
                <div class="mb-3">
                    <label>Nominal Topup (Rp)</label>
                    <input type="number" name="nominal" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Proses Topup Mandiri</button>
            </form>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <!-- Bagian untuk Verifikasi Topup by Admin -->
        <div>
            <h4>Verifikasi Topup oleh Admin</h4>
            <p>Jika admin telah menginput topup untuk Anda, lakukan scan QR verifikasi di bawah ini:</p>
            <!-- Container kamera dengan aspect ratio 4:3 -->
            <div id="reader" style="width:100%; max-width:400px; aspect-ratio: 4/3; border:1px solid #ddd;"></div>
            <div class="mt-2">
                <button id="startCamera" class="btn btn-secondary">Start Camera</button>
                <button id="stopCamera" class="btn btn-secondary" style="display:none;">Stop Camera</button>
            </div>
            <!-- Form tersembunyi untuk mengirim token hasil scan -->
            <form id="qrForm" method="POST" style="display:none;">
                <input type="hidden" name="method" value="topup_by_admin">
                <input type="hidden" name="qr_token" id="qr_token">
            </form>
        </div>
    </div>
</div>

<!-- Riwayat Topup -->
<h4 class="fw-bold text-primary mb-3">Riwayat Topup</h4>
<?php
$sqlHistory = "SELECT * FROM transactions 
               WHERE user_id = $user_id AND transaction_type='topup_mandiri' 
               ORDER BY transaction_date DESC";
$resultHistory = $conn->query($sqlHistory);
if ($resultHistory->num_rows > 0) :
?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nominal (Rp)</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultHistory->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                        <td>Mandiri</td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <span class="badge text-bg-warning"><?= ucfirst($row['status']); ?></span>
                            <?php elseif ($row['status'] == 'verified'): ?>
                                <span class="badge text-bg-info"><?= ucfirst($row['status']); ?></span>
                            <?php elseif ($row['status'] == 'completed'): ?>
                                <span class="badge text-bg-success"><?= ucfirst($row['status']); ?></span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary"><?= ucfirst($row['status']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['transaction_date']; ?></td>
                        <td>
                            <!-- Button Detail (tetap sama) -->
                            <button type="button" class="btn btn-info btn-sm detailBtn"
                                data-id="<?= $row['id']; ?>"
                                data-nominal="<?= number_format($row['nominal'], 0, ',', '.'); ?>"
                                data-method="Mandiri"
                                data-status="<?= ucfirst($row['status']); ?>"
                                data-token="<?= $row['qr_token']; ?>"
                                data-date="<?= $row['transaction_date']; ?>">
                                Detail
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="alert alert-info">Belum ada riwayat topup.</div>
<?php endif; ?>

<!-- Modal Detail Topup -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Topup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>ID Transaksi:</strong> <span id="modal-id"></span></p>
                <p><strong>Nominal:</strong> Rp <span id="modal-nominal"></span></p>
                <p><strong>Metode:</strong> <span id="modal-method"></span></p>
                <p><strong>Status:</strong> <span id="modal-status"></span></p>
                <p><strong>QR Token:</strong> <span id="modal-token"></span></p>
                <p><strong>Tanggal:</strong> <span id="modal-date"></span></p>
                <p><strong>Gambar QR:</strong></p>
                <img id="modal-qr" src="" alt="QR Code" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk mengisi modal dengan data dari tombol detail -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var detailButtons = document.querySelectorAll('.detailBtn');
        detailButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var token = button.getAttribute('data-token');
                document.getElementById('modal-id').innerText = button.getAttribute('data-id');
                document.getElementById('modal-nominal').innerText = button.getAttribute('data-nominal');
                document.getElementById('modal-method').innerText = button.getAttribute('data-method');
                document.getElementById('modal-status').innerText = button.getAttribute('data-status');
                document.getElementById('modal-token').innerText = token;
                document.getElementById('modal-date').innerText = button.getAttribute('data-date');
                document.getElementById('modal-qr').src = "https://quickchart.io/qr?text=" + token + "&size=200";
                var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                detailModal.show();
            });
        });
    });

    // ===================
    // Konfigurasi QR Scanner (untuk verifikasi topup by admin)
    // ===================
    let html5QrcodeScanner;
    let scannerActive = false;

    document.getElementById('startCamera').addEventListener('click', function() {
        if (!scannerActive) {
            // Tampilkan form tersembunyi jika belum muncul
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
        // Hentikan scanner otomatis
        html5QrcodeScanner.stop().then(() => {
            scannerActive = false;
            document.getElementById('startCamera').disabled = false;
            document.getElementById('stopCamera').style.display = "none";
            // Simpan token ke input tersembunyi
            document.getElementById('qr_token').value = decodedText;
            // Tampilkan modal konfirmasi (jika diinginkan, atau langsung submit)
            var confirmModal = new bootstrap.Modal(document.getElementById('detailModal'));
            // Kita bisa menggunakan modal detail yang sama untuk konfirmasi scan
            document.getElementById('modal-id').innerText = "-";
            document.getElementById('modal-nominal').innerText = "-";
            document.getElementById('modal-method').innerText = "Topup by Admin";
            document.getElementById('modal-status').innerText = "Pending";
            document.getElementById('modal-token').innerText = decodedText;
            document.getElementById('modal-date').innerText = "-";
            document.getElementById('modal-qr').src = "https://quickchart.io/qr?text=" + decodedText + "&size=200";
            confirmModal.show();
            // Submit form otomatis
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