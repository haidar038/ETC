<?php
require_once '../config/config.php';
require_once '../templates/header.php';
?>

<div class="row">
    <div class="col-12 mx-auto">
        <h1 class="text-center fw-bold mb-4">
            <i class="fas fa-envelope text-primary me-2"></i>Contact Us
        </h1>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title fw-bold">Hubungi Kami</h3>
                        <address class="mt-4">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Alamat</h5>
                                    <p class="mb-0">
                                        Jakarta Convention Center<br>
                                        Jl. Jenderal Gatot Subroto<br>
                                        Jakarta 10270, Indonesia
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-phone-alt fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Telepon</h5>
                                    <p class="mb-0">(021) 123-4567</p>
                                </div>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-envelope fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Email</h5>
                                    <p class="mb-0">info@event-transaction.com</p>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>Jam Operasional</h5>
                                    <p class="mb-0">
                                        Senin - Jumat: 09:00 - 17:00<br>
                                        Sabtu: 10:00 - 15:00<br>
                                        Minggu: Tutup
                                    </p>
                                </div>
                            </div>
                        </address>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h3 class="card-title fw-bold">Kirim Pesan</h3>
                        <p class="text-muted">Silakan isi formulir di bawah ini untuk mengirim pesan kepada kami.
                        </p>

                        <form action="#" method="POST" class="mt-4">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subjek</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Pesan</label>
                                <textarea class="form-control" id="message" name="message" rows="4"
                                    required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="card shadow mt-4">
            <div class="card-body">
                <h3 class="card-title">Lokasi</h3>
                <div class="mt-3 embed-responsive embed-responsive-16by9">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2904357208824!2d106.7993944!3d-6.2258699!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f14d30079f01%3A0x2e74f2341fff266d!2sJakarta%20Convention%20Center!5e0!3m2!1sen!2sid!4v1658997000000!5m2!1sen!2sid"
                        width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div> -->
    </div>

    <div class="mt-5 text-center">
        <h4 class="fw-bold">Ikuti Kami</h4>
        <div class="mt-3">
            <a href="#" class="btn btn-outline-primary me-2">
                <i class="fab fa-facebook fa-lg"></i>
            </a>
            <a href="#" class="btn btn-outline-info me-2">
                <i class="fab fa-twitter fa-lg"></i>
            </a>
            <a href="#" class="btn btn-outline-danger me-2">
                <i class="fab fa-instagram fa-lg"></i>
            </a>
            <a href="#" class="btn btn-outline-success">
                <i class="fab fa-whatsapp fa-lg"></i>
            </a>
        </div>
    </div>
</div>

<?php
require_once '../templates/footer.php';
?>