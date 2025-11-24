<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
.navbar {
    background: #0a1d33;
    padding: 12px 25px;
    font-family: "Poppins", sans-serif;
}
.navbar .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.nav-brand {
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    text-decoration: none;
}
.nav-menu {
    display: flex;
    gap: 18px;
    align-items: center;
}
.nav-menu a {
    color: #cfd7e3;
    text-decoration: none;
    font-size: 14px;
    padding: 6px 10px;
    border-radius: 6px;
    transition: 0.2s;
}
.nav-menu a:hover,
.nav-menu a.active {
    background: #132f57;
    color: #ffffff;
}

.nav-user {
    color: #86a3c6;
    font-size: 13px;
    margin-right: 10px;
}

/* Responsive */
.menu-toggle {
    display: none;
    font-size: 22px;
    color: white;
    cursor: pointer;
}

@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }

    .nav-menu {
        display: none;
        position: absolute;
        background: #0a1d33;
        top: 60px;
        right: 0;
        width: 100%;
        flex-direction: column;
        padding: 15px 0;
    }

    .nav-menu.show {
        display: flex;
    }
}
</style>

<nav class="navbar">
    <div class="container">
        <a href="<?= BASE_URL ?>/index.php" class="nav-brand">
            🎬 Bioskop App
        </a>

        <div class="menu-toggle" id="menuToggle">
            ☰
        </div>

        <div class="nav-menu" id="navMenu">

            <a href="<?= BASE_URL ?>/index.php"
               class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
               Home
            </a>

            <a href="<?= BASE_URL ?>/film.php"
               class="<?= $currentPage == 'film.php' ? 'active' : '' ?>">
               Film
            </a>

            <a href="<?= BASE_URL ?>/reservasi.php"
               class="<?= $currentPage == 'reservasi.php' ? 'active' : '' ?>">
               Reservasi
            </a>

            <?php if (isset($_SESSION['user'])): ?>

                <a href="<?= BASE_URL ?>/profil.php"
                   class="<?= $currentPage == 'profil.php' ? 'active' : '' ?>">
                   Profil
                </a>

                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <a href="<?= BASE_URL ?>/admin/index.php"
                       class="<?= str_contains($_SERVER['PHP_SELF'], 'admin') ? 'active' : '' ?>">
                       Admin
                    </a>
                <?php endif; ?>

                <span class="nav-user">
                    👤 <?= $_SESSION['user']['name']; ?>
                </span>

                <a href="<?= BASE_URL ?>/logout.php" id="logoutBtn" onclick="return confirmLogout()">
                    Logout
                </a>

                <script>
                    function confirmLogout() {
                       const confirmation = confirm("Apakah Anda yakin ingin keluar (Logout)?");
                        return confirmation;
                    }
                </script>

            <?php else: ?>

                <a href="<?= BASE_URL ?>/login.php"
                   class="<?= $currentPage == 'login.php' ? 'active' : '' ?>">
                   Login
                </a>

                <a href="<?= BASE_URL ?>/register.php"
                   class="<?= $currentPage == 'register.php' ? 'active' : '' ?>">
                   Daftar
                </a>

            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.getElementById("menuToggle").addEventListener("click", function() {
    document.getElementById("navMenu").classList.toggle("show");
});
</script>
