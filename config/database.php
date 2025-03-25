<?php
// config/database.php

// Pastikan konfigurasi sudah dimuat dari config.php
require_once __DIR__ . '/config.php';

// Ambil konfigurasi database dari environment
$host    = $_ENV['DB_HOST'] ?? 'localhost';
$user    = $_ENV['DB_USER'] ?? 'root';
$pass    = $_ENV['DB_PASS'] ?? '';
$db      = $_ENV['DB_NAME'] ?? 'etchip';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset koneksi
$conn->set_charset($charset);
