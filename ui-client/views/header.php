<?php

require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'E-Commerce UI' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php">🛒 E-Commerce</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Produk</a></li>
                <li><a href="utilities.php">🛠️ Tools</a></li>
                <li><a href="cart.php">Keranjang <span id="cart-count" class="badge">0</span></a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (($_SESSION['user']['role'] ?? 'user') === 'admin'): ?>
                        <li><a href="admin.php">👑 Admin</a></li>
                    <?php endif; ?>
                    <li><a href="profile.php">Profil</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Global Maintenance Alert -->
    <div id="maintenance-banner" class="banner banner-warning" style="display:none;">
        <div class="container">
            ⚠️ <strong>Mode Maintenance:</strong> Beberapa kategori produk sedang dalam pemeliharaan.
        </div>
    </div>

    <main class="container">