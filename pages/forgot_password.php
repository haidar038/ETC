<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        echo "<div class='alert alert-danger'>Email tidak valid.</div>";
    } else {
        // Cari user berdasarkan email
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Generate token reset dan tentukan waktu kedaluwarsa (misal 1 jam dari sekarang)
            $reset_token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $stmtUpdate = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
            $stmtUpdate->bind_param("ssi", $reset_token, $expires, $user['id']);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            // Siapkan link reset password (gunakan BASE_URL)
            $resetLink = BASE_URL . "pages/reset_password.php?token=" . $reset_token;

            // Konfigurasi PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.hostinger.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'official@krsp.cloud'; // Ganti dengan email Anda
                $mail->Password   = 'Metaverse@2025'; // Ganti dengan password email Anda
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('official@krsp.cloud', 'Event Territory Chip');
                $mail->addAddress($email, $user['name']);
                $mail->isHTML(true);
                $mail->Subject = 'Reset Password Anda';
                $mail->Body    = "<p>Halo " . $user['name'] . ",</p>
                                  <p>Anda telah meminta reset password. Silakan klik link berikut untuk mengatur ulang password Anda:</p>
                                  <p><a href='$resetLink'>$resetLink</a></p>
                                  <p>Link ini akan berlaku selama 1 jam.</p>";
                $mail->AltBody = "Halo " . $user['name'] . ",\n\nAnda telah meminta reset password. Silakan buka link berikut untuk mengatur ulang password Anda: $resetLink\n\nLink ini akan berlaku selama 1 jam.";
                $mail->send();
                echo "<div class='alert alert-success'>Link reset password telah dikirim ke email Anda.</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-warning'>Gagal mengirim email reset password. Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Email tidak ditemukan.</div>";
        }
        $stmt->close();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-5">
        <h2 class="fw-bold text-primary">Lupa Password</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Masukkan Email Anda</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
        </form>
    </div>
</div>

<?php include '../templates/footer.php'; ?>