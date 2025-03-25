<?php
require_once __DIR__ . '/../vendor/autoload.php';
// config/config.php

// Memuat file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Definisikan konstanta dari variabel env
define('BASE_URL', $_ENV['BASE_URL']);

// Pengaturan database
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);

// Pengaturan lain, misalnya secret key
define('SECRET_KEY', $_ENV['SECRET_KEY']);

ini_set('session.cookie_http_only', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
