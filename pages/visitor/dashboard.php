<?php
include '../../config/config.php';
include '../../config/database.php';
include '../../templates/header.php';

// Pastikan hanya visitor yang dapat mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'visitor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql    = "SELECT * FROM users WHERE id=$user_id";
$result = $conn->query($sql);
$user   = $result->fetch_assoc();
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="fw-bold text-primary mb-4">Visitor Dashboard</h2>
                    <p class="mb-0 lead">Selamat datang, <?= $user['name']; ?>!</p>
                    <p>Poin kamu saat ini: <span class="fw-bold"><?= $user['points'] ?? 0; ?></span></p>
                </div>

                <!-- List Group -->
                <div class="list-group mt-4">
                    <a href="topup.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-cash-coin me-2"></i> Top Up Poin
                    </a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#qrModal" class="list-group-item list-group-item-action">
                        <i class="bi bi-qr-code-scan me-2"></i> Scan QR Tenant
                    </a>
                    <a href="transactions.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
                    </a>
                    <a href="profile.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk QR Scan "Browse Products" yang akan mengarah ke halaman catalog -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">Scan Tenant QR Code <i class="fas fa-qrcode"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Scan QR Code tenant untuk melihat katalog produk mereka.</p>
                <div id="qr-reader" style="width: 100%"></div>
                <div id="qr-reader-results" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button id="startButton" class="btn btn-primary">Mulai Scan</button>
                <button id="stopButton" class="btn btn-danger" style="display:none">Berhenti</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const html5QrCode = new Html5Qrcode("qr-reader");
        const resultContainer = document.getElementById('qr-reader-results');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');

        let scanning = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText) {
                resultContainer.innerHTML = `<div class="alert alert-success">QR Code terdeteksi! Mengalihkan ke katalog tenant...</div>`;
                html5QrCode.stop();
                scanning = false;
                startButton.style.display = 'inline-block';
                stopButton.style.display = 'none';

                // Redirect to the tenant catalog URL after a brief delay
                setTimeout(() => {
                    window.location.href = decodedText;
                }, 1500);
            }
        }

        startButton.addEventListener('click', function() {
            html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: 250
                },
                onScanSuccess
            ).then(() => {
                scanning = true;
                startButton.style.display = 'none';
                stopButton.style.display = 'inline-block';
            }).catch((err) => {
                resultContainer.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`;
            });
        });

        stopButton.addEventListener('click', function() {
            if (scanning) {
                html5QrCode.stop().then(() => {
                    scanning = false;
                    startButton.style.display = 'inline-block';
                    stopButton.style.display = 'none';
                });
            }
        });

        $('#qrModal').on('hidden.bs.modal', function() {
            if (scanning) {
                html5QrCode.stop();
                scanning = false;
                startButton.style.display = 'inline-block';
                stopButton.style.display = 'none';
            }
        });
    });
</script>


<?php include '../../templates/footer.php'; ?>