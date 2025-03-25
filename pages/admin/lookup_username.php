<?php
header('Content-Type: application/json');

include '../config/config.php';
include '../config/database.php';

$username = isset($_GET['username']) ? trim($_GET['username']) : '';

if (empty($username)) {
    echo json_encode(["found" => false]);
    exit();
}

// Mencari visitor berdasarkan username
$stmt = $conn->prepare("SELECT id, name FROM users WHERE username = ? AND user_type = 'visitor'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "found" => true,
        "id"    => $user['id'],
        "name"  => $user['name']
    ]);
} else {
    echo json_encode(["found" => false]);
}

$stmt->close();
$conn->close();
