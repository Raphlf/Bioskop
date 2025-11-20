<?php require_once __DIR__ . '/../auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bioskop App</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <a href="<?= BASE_URL ?>/index.php">Bioskop App</a>
    </div>

    <div class="nav-right">
        <?php if(is_logged_in()): ?>
            <a href="<?= BASE_URL ?>/profil.php">Profil</a>
            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard Admin</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
