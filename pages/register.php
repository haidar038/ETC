<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';
include '../includes/functions.php'; // Pastikan fungsi generateQRToken() tersedia

// Sertakan autoload Composer untuk PHPMailer
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name      = $conn->real_escape_string($_POST['name']);
    $email     = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password  = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'];

    if (!$email || empty($password)) {
        echo "<div class='alert alert-danger'>Email atau password tidak valid.</div>";
    } else {
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Jika mendaftar sebagai tenant, generate catalog_token
        $catalog_token = null;
        if ($user_type === 'tenant') {
            $catalog_token = generateQRToken(10); // Misalnya, token sepanjang 10 karakter
        }

        // Generate token verifikasi email
        $email_verification_token = bin2hex(random_bytes(16));
        $confirmed_status = 0; // Belum terverifikasi

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type, catalog_token, confirmed_status, email_verification_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // Format binding: name (s), email (s), password (s), user_type (s), catalog_token (s), confirmed_status (i), email_verification_token (s)
        $stmt->bind_param("sssssis", $name, $email, $passwordHash, $user_type, $catalog_token, $confirmed_status, $email_verification_token);
        if ($stmt->execute()) {
            // Buat link verifikasi, ganti BASE_URL sesuai dengan lingkungan produksi Anda
            $verificationLink = BASE_URL . "pages/verify_email.php?token=" . $email_verification_token;

            // Konfigurasi PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Set pengaturan SMTP
                $mail->isSMTP();
                $mail->Host       = $_ENV['SMTP_HOST']; // Ganti dengan host SMTP dari Hostinger
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['MAIL_USER']; // Ganti dengan email Anda
                $mail->Password   = $_ENV['MAIL_PASS']; // Ganti dengan password email Anda
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // atau ENCRYPTION_SMTPS untuk SSL
                $mail->Port       = $_ENV['SMTP_PORT'];

                // Atur pengirim dan penerima
                $mail->setFrom($_ENV['MAIL_USER'], 'Event Territory Chip');
                $mail->addAddress($email, $name);

                // Konten email
                $mail->isHTML(true);
                $mail->Subject = 'Konfirmasi Email Anda';
                $mail->Body    = "<p>Halo $name,</p>
                                  <p>Terima kasih telah mendaftar. Silakan klik link berikut untuk mengkonfirmasi email Anda:</p>
                                  <p><a href='$verificationLink'>$verificationLink</a></p>
                                  <p>Jika link tidak bisa diklik, salin URL berikut ke browser Anda:</p>
                                  <p>$verificationLink</p>";
                $mail->AltBody = "Halo $name,\n\nTerima kasih telah mendaftar. Silakan buka link berikut untuk mengkonfirmasi email Anda: $verificationLink";
                $mail->send();

                echo "<div class='alert alert-success mb-3'>Registrasi berhasil. Silakan cek email Anda untuk konfirmasi.</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-warning mb-3'>Registrasi berhasil, tetapi pengiriman email verifikasi gagal. Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            echo "<div class='alert alert-danger mb-3'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-5">
        <h2 class="mb-4 fw-bold text-primary">Register</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label for="user_type" class="form-label">Daftar sebagai:</label>
                <select name="user_type" id="user_type" class="form-control" required>
                    <option value="visitor">Visitor</option>
                    <option value="tenant">Tenant</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Bergabung Sekarang!</button>
        </form>
        <p class="mt-3 text-center">Sudah punya akun? <a class="link-primary fw-bold" href="login.php">Masuk Disini</a></p>
    </div>
</div>

<?php
include '../templates/footer.php';
?>