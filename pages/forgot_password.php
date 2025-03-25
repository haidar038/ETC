<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $error = "Email tidak valid.";
    } else {
        // Cari user berdasarkan email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
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

            // Siapkan link reset (sesuaikan dengan URL domain Anda)
            $resetLink = "http://localhost:8085/pages/reset_password.php?token=" . $reset_token;

            // (Dalam implementasi nyata, kirim email ke pengguna dengan link reset)
            echo "<div class='alert alert-success'>Link reset password telah dikirim ke email Anda (contoh link: <a href='$resetLink'>$resetLink</a>).</div>";
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