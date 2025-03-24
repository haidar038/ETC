<?php
include '../config/config.php';
include '../config/database.php';
include '../templates/header.php';

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
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $passwordHash, $user_type);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Registrasi berhasil. <a href='login.php'>Login sekarang</a></div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<h2>Register</h2>
<form method="POST">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Jenis User</label>
        <select name="user_type" class="form-control" required>
            <option value="visitor">Visitor</option>
            <option value="tenant">Tenant</option>
            <!-- Pastikan admin hanya dibuat secara manual atau melalui proses yang berbeda -->
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>