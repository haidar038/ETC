<?php
include_once __DIR__ . '/../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETC - Event Territory Chip</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="h-100">
    <?php
    // Get current page
    $current_page = $_SERVER['PHP_SELF'];
    $is_admin_login = strpos($current_page, 'pages/admin/admin_login.php') !== false;
    $navbar_class = $is_admin_login ? 'navbar-dark bg-dark' : 'navbar-dark bg-primary';
    ?>

    <nav class="navbar navbar-expand-lg <?= $navbar_class ?>">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL; ?>">
                <i class="fas fa-qrcode me-2"></i>ETC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>">Home</a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL; ?>pages/admin/dashboard.php">Dashboard</a>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'visitor'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL; ?>pages/visitor/dashboard.php">Dashboard</a>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'tenant'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL; ?>pages/tenant/dashboard.php">Dashboard</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/how_it_works.php">How It Works</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/about.php">About</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL; ?>pages/contact.php">Contact</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> <?= $_SESSION['username'] ?? 'User'; ?>
                                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'visitor'): ?>
                                    <span class="badge bg-light text-dark ms-1"><?= $_SESSION['balance'] ?? '0'; ?> pts</span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL; ?>pages/admin/dashboard.php">Dashboard</a></li>
                                <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'visitor'): ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL; ?>pages/visitor/profile.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL; ?>pages/visitor/balance.php">Balance</a></li>
                                <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'tenant'): ?>
                                    <li><a class="dropdown-item" href="<?= BASE_URL; ?>pages/tenant/profile.php">Profile</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL; ?>pages/change_password.php">Change Password</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= BASE_URL; ?>logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <div class="d-flex gap-2">
                            <li class="nav-item"></li>
                            <a class="nav-link" href="<?= BASE_URL; ?>pages/login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-light" href="<?= BASE_URL; ?>pages/register.php">Register</a>
                            </li>
                        </div>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container mt-5 h-100">