<?php
require_once '../config/config.php';
require_once '../templates/header.php';
?>

<div class="row">
    <div class="col-12 col-lg-10 mx-auto">
        <h1 class="text-center fw-bold mb-4">
            <i class="fas fa-question-circle text-primary me-2"></i>How It Works
        </h1>

        <!-- Overview Section -->
        <div class="card mb-5 shadow">
            <div class="card-body">
                <h3 class="card-title fw-bold mb-3">Deskripsi Sistem</h3>
                <hr>
                <p>
                    <strong>Event Territoty Chip</strong> menawarkan solusi transaksi tanpa uang tunai (cashless) yang
                    aman dan efisien untuk acara dan expo.
                </p>
                <p>
                    Sistem ini mengkonversi uang tunai menjadi poin digital (1 poin = Rp1.000), yang dapat
                    digunakan oleh pengunjung untuk bertransaksi dengan tenant yang berpartisipasi. Semua
                    transaksi diverifikasi dengan kode QR untuk keamanan dan transparansi.
                </p>
            </div>
        </div>

        <!-- Step by Step Guide -->
        <h3 class="text-center fw-bold text-success mb-4">Panduan Langkah demi Langkah</h3>

        <!-- For Visitors -->
        <div class="card mb-5 shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-user me-2"></i>Untuk Pengunjung</h4>
            </div>
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-success">1</span>
                        </div>
                        <h5>Registrasi</h5>
                        <p>Daftar sebagai pengunjung di aplikasi atau booth pendaftaran acara.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-success">2</span>
                        </div>
                        <h5>Top-Up Poin</h5>
                        <p>Setor uang tunai ke admin untuk mendapatkan poin digital.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-success">3</span>
                        </div>
                        <h5>Belanja Produk</h5>
                        <p>Pilih produk dari tenant dan checkout menggunakan poin.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-success">4</span>
                        </div>
                        <h5>Scan QR Code</h5>
                        <p>Tunjukkan QR code pesanan Anda untuk diverifikasi oleh tenant.</p>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ url_for('auth.register') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sebagai Pengunjung
                    </a>
                </div>
            </div>
        </div>

        <!-- For Tenants -->
        <div class="card mb-5 shadow">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="fas fa-store me-2"></i>Untuk Tenant</h4>
            </div>
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-info">1</span>
                        </div>
                        <h5>Registrasi</h5>
                        <p>Daftar sebagai tenant di aplikasi atau booth pendaftaran acara.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-info">2</span>
                        </div>
                        <h5>Tambah Produk</h5>
                        <p>Tambahkan produk yang ingin Anda jual dengan detail dan harga.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-info">3</span>
                        </div>
                        <h5>Verifikasi Pesanan</h5>
                        <p>Scan QR code dari pengunjung untuk memverifikasi pesanan.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-info">4</span>
                        </div>
                        <h5>Kelola Penjualan</h5>
                        <p>Pantau penjualan dan kelola inventaris produk Anda.</p>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ url_for('auth.register') }}" class="btn btn-info btn-lg text-white">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sebagai Tenant
                    </a>
                </div>
            </div>
        </div>

        <!-- For Admins -->
        <div class="card mb-5 shadow">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="fas fa-user-tie me-2"></i>Untuk Admin</h4>
            </div>
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-danger">1</span>
                        </div>
                        <h5>Proses Top-Up</h5>
                        <p>Terima uang tunai dari pengunjung dan proses top-up poin.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-danger">2</span>
                        </div>
                        <h5>Kelola Pengguna</h5>
                        <p>Tambah, edit, atau nonaktifkan akun pengguna jika diperlukan.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-danger">3</span>
                        </div>
                        <h5>Pantau Transaksi</h5>
                        <p>Pantau semua transaksi top-up dan pembelian dalam sistem.</p>
                    </div>
                    <div class="col-md-3 mb-4 text-center">
                        <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-3"
                            style="width: 80px; height: 80px;">
                            <span class="display-4 text-danger">4</span>
                        </div>
                        <h5>Lihat Laporan</h5>
                        <p>Akses laporan dan statistik dari semua aktivitas acara.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Verification -->
        <div class="card mb-5 shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-qrcode me-2"></i>Verifikasi QR Code</h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h5>Bagaimana Verifikasi QR Code Bekerja?</h5>
                        <ol class="mt-3">
                            <li class="mb-2">Setiap transaksi (top-up atau pembelian) menghasilkan QR code unik.
                            </li>
                            <li class="mb-2">QR code berisi data transaksi terenkripsi yang hanya dapat diverifikasi
                                oleh sistem.</li>
                            <li class="mb-2">Untuk pembelian produk, pengunjung menunjukkan QR code kepada tenant.
                            </li>
                            <li class="mb-2">Tenant memindai QR code untuk memverifikasi pembayaran.</li>
                            <li class="mb-2">Transaksi dicatat dalam sistem dan inventaris produk diperbarui.</li>
                        </ol>
                        <p class="mt-3">
                            Sistem QR code ini memastikan transaksi yang aman, mengurangi kesalahan manual, dan
                            memberikan catatan digital yang dapat dilacak untuk setiap pembelian.
                        </p>
                    </div>
                    <div class="col-md-5 text-center">
                        <i class="fas fa-qrcode fa-10x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQs -->
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Pertanyaan Umum</h4>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Bagaimana cara melakukan top-up poin?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="faqOne"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Untuk melakukan top-up, datangi booth admin acara, setor uang tunai, dan admin akan
                                memproses top-up poin ke akun Anda. Setiap Rp1.000 setara dengan 1 poin.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Apakah saya bisa menarik kembali poin menjadi uang tunai?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, Anda dapat menarik kembali poin yang tidak terpakai menjadi uang tunai di booth
                                admin
                                setelah acara berakhir atau sesuai dengan kebijakan acara.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Bagaimana jika QR code tidak dapat dipindai?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Jika terjadi masalah saat memindai QR code, pastikan layar ponsel Anda cukup terang
                                dan
                                bersih. Jika masalah berlanjut, Anda dapat meminta bantuan admin acara untuk
                                verifikasi manual.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Bisakah saya menggunakan satu akun untuk beberapa pengunjung?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="faqFour"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Kami menyarankan setiap pengunjung memiliki akun sendiri untuk keamanan dan
                                kemudahan
                                pelacakan transaksi. Namun, Anda dapat berbagi akun dengan anggota keluarga atau
                                teman jika diperlukan.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Bagaimana cara menjadi tenant di acara?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="faqFive"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Untuk menjadi tenant, hubungi penyelenggara acara melalui kontak yang tersedia di
                                halaman "Hubungi Kami". Panitia akan memberikan informasi tentang persyaratan dan
                                biaya partisipasi.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ url_for('main.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>