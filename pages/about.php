<?php
require_once '../config/config.php';
require_once '../templates/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <h1 class="text-center fw-bold mb-4">
            <i class="fas fa-info-circle text-primary me-2"></i>About ETC
        </h1>

        <div class="card mb-4 shadow">
            <div class="card-body">
                <h3 class="card-title">Tentang Kami</h3>
                <p>
                    <strong>Event Territory Chip</strong> adalah platform transaksi digital yang dirancang khusus untuk acara,
                    expo, dan festival.
                </p>
                <p>
                    Sistem kami menawarkan solusi transaksi tanpa uang tunai (cashless) yang aman dan efisien,
                    memungkinkan pengunjung untuk melakukan top-up saldo dan melakukan pembelian dari berbagai
                    tenant menggunakan poin digital. Setiap Rp1.000 bernilai 1 poin dalam sistem kami.
                </p>
                <p>
                    Dengan menggunakan teknologi QR code untuk verifikasi transaksi, sistem ini memastikan keamanan
                    dan transparansi dalam setiap pembelian dan pembayaran yang dilakukan selama acara.
                </p>
            </div>
        </div>

        <div class="card mb-4 shadow">
            <div class="card-body">
                <h3 class="card-title">Fitur Utama</h3>
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-wallet fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Top-Up Poin</h5>
                                <p>Konversi uang tunai menjadi poin digital untuk bertransaksi dalam acara.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Belanja Produk</h5>
                                <p>Temukan dan beli produk dari berbagai tenant menggunakan poin.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-qrcode fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Verifikasi QR Code</h5>
                                <p>Transaksi yang aman dengan verifikasi QR code untuk setiap pembelian.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-history fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Riwayat Transaksi</h5>
                                <p>Pantau semua aktivitas pembelian dan top-up dalam satu tempat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow">
            <div class="card-body">
                <h3 class="card-title">Peran Pengguna</h3>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3 text-center">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                                <h5>Admin</h5>
                                <p>Mengelola top-up, memantau transaksi, dan mengawasi keseluruhan sistem.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-user fa-3x text-success mb-3"></i>
                                <h5>Pengunjung</h5>
                                <p>Melakukan top-up, membeli produk, dan menikmati pengalaman transaksi digital.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-store fa-3x text-info mb-3"></i>
                                <h5>Tenant</h5>
                                <p>Menawarkan produk, memproses pesanan, dan mengelola inventaris.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="how_it_works.php" class="btn btn-primary btn-lg me-2">
                <i class="fas fa-info-circle me-2"></i>Cara Kerja
            </a>
            <a href="contact.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-envelope me-2"></i>Hubungi Kami
            </a>
        </div>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>