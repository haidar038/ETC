<?php
session_start();
include_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Web App</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL; ?>">Web App</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>">Home</a>
                    </li>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'visitor'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/visitor/dashboard.php">Dashboard</a>
                    </li>
                    <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'tenant'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/tenant/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/tenant/profile.php">Profil</a>
                    </li>
                    <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/admin/dashboard.php">Dashboard</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>logout.php">Logout</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/register.php">Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">