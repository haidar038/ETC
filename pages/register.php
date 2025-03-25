<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';
include '../includes/functions.php'; // Pastikan fungsi generateQRToken() tersedia

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

        // Generate email verification token
        $email_verification_token = bin2hex(random_bytes(16));
        // confirmed_status = 0 berarti belum terverifikasi
        $confirmed_status = 0;

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type, catalog_token, confirmed_status, email_verification_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssds", $name, $email, $passwordHash, $user_type, $catalog_token, $confirmed_status, $email_verification_token);
        // Catatan: Tipe binding "d" dipakai untuk confirmed_status jika kita pakai tipe numerik; 
        // jika seharusnya string, gunakan "s". Di sini gunakan "d" untuk angka 0.
        if ($stmt->execute()) {
            // Buat link verifikasi (ganti yourdomain.com dengan domain Anda)
            $verificationLink = "http://yourdomain.com/pages/verify_email.php?token=" . $email_verification_token;

            // Pada implementasi nyata, kirim email verifikasi ke pengguna
            echo "<div class='alert alert-success mb-3'>
                    Registrasi berhasil. Silakan cek email Anda untuk konfirmasi (contoh link: <a href='$verificationLink'>$verificationLink</a>).
                  </div>";
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