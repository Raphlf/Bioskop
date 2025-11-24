<?php require_once __DIR__ . '/../auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bioskop App</title>
    <style>
    body {
        margin: 0;
        padding: 0;
    }
    .navbar {
        width: 100%;
        background: linear-gradient(135deg, #0a1d33, #0f2a47);
        display:flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 40px;
        position: sticky;
        top: 0;
        z-index: 999;
        box-sizing: border-box;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.15);
        font-family: "Poppins", sans-serif;
        margin: 0;
    }

.nav-left a {
    font-size: 24px;
    font-weight: 600;
    text-decoration: none;
    color: #ffffff;
    letter-spacing: 0.5px;

    transition: 0.3s ease;
}

.nav-left a:hover {
    opacity: 0.85;
    transform: translateY(-1px);
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 28px;
}

.nav-right a {
    text-decoration: none;
    color: #e6edf5;
    font-size: 16px;
    font-weight: 500;

    transition: 0.3s ease;
}

.nav-right a:hover {
    color: #ffffff;
    transform: translateY(-2px);
}

@media (max-width: 700px) {
    .navbar {
        flex-direction: column;
        padding: 16px 22px;
        gap: 12px;
        text-align: center;
    }

    .nav-right {
        gap: 16px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .nav-left a {
        font-size: 22px;
    }

    .nav-right a {
        font-size: 15px;
    }
}

</style>
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
