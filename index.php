<?php
include 'config/config.php';
include 'templates/header.php';
?>
<div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold text-primary">Event Territory Chip</h1>
    <div class="col-lg-8 mx-auto">
        <p class="lead mb-4">
            Nikmati pengalaman transaksi mudah dan aman di acara kami.
            Deposit tunai Anda akan dikonversi menjadi poin (1 poin = Rp1.000)
            untuk bertransaksi dengan semua tenant acara.
        </p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="pages/register.php" class="btn btn-primary btn-lg px-4 gap-3">
                    <i class="fas fa-user-plus me-2"></i>Register
                </a>
                <a href="pages/login.php" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            <?php else: ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#qrModal" class="btn btn-primary btn-lg px-4 gap-3">
                    <i class="fas fa-shopping-bag me-2"></i>Browse Products
                </a>
                <?php if ($_SESSION['user_type'] == 'visitor'): ?>
                    <a href="pages/visitor/topup.php" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-wallet me-2"></i>Top-Up Points
                    </a>
                <?php endif; ?>
            <?php endif; ?>
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

<!-- How It Works Section -->
<div class="container px-4 py-5" id="how-it-works">
    <h2 class="pb-2 border-bottom">How It Works</h2>
    <div class="row g-4 py-4 row-cols-1 row-cols-lg-3">
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
                <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
            </div>
            <div>
                <h3>1. Top-Up Points</h3>
                <p>Deposit uang tunai kepada admin acara dan dapatkan poin transaksi. Setiap Rp1.000 bernilai 1 poin.
                </p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
                <i class="fas fa-shopping-cart fa-2x text-primary"></i>
            </div>
            <div>
                <h3>2. Shop with Tenants</h3>
                <p>Gunakan poin Anda untuk berbelanja produk dari tenant-tenant yang berpartisipasi dalam acara.</p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
                <i class="fas fa-qrcode fa-2x text-primary"></i>
            </div>
            <div>
                <h3>3. Verify with QR Code</h3>
                <p>Setiap transaksi diverifikasi dengan kode QR untuk keamanan dan kepastian transaksi Anda.</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="container px-4 py-5" id="statistics">
    <h2 class="pb-2 border-bottom">Event Statistics</h2>
    <div class="row text-center py-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 bg-primary text-white">
                <div class="card-body">
                    <i class="fas fa-store fa-3x mb-3"></i>
                    <h3><?php echo $tenant_count ?? 0; ?></h3>
                    <h5>Participating Tenants</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 bg-success text-white">
                <div class="card-body">
                    <i class="fas fa-shopping-bag fa-3x mb-3"></i>
                    <h3><?php echo count($featured_products ?? []); ?></h3>
                    <h5>Featured Products</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 bg-info text-white">
                <div class="card-body">
                    <i class="fas fa-credit-card fa-3x mb-3"></i>
                    <h3>Quick & Secure</h3>
                    <h5>Transaction Process</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>