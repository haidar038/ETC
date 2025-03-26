<?php
// config/config.php

// Muat autoload Composer (pastikan file vendor/autoload.php ada)
require_once __DIR__ . '/../vendor/autoload.php';

// Muat file .env dari root project
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Definisikan BASE_URL dari environment, fallback jika tidak diset
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost:8085/');

// Pengaturan session
ini_set('session.cookie_http_only', 1);
ini_set('session.cookie_secure', 1);
session_start();
