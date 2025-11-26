<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;
?>

<style>
/* ===================== NAVBAR TIX ID STYLE ===================== */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
    padding: 18px 26px;
    background: rgba(255,255,255,1);
    border-bottom: 1px solid #e5e7eb;
    backdrop-filter: none;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-family: "Poppins", sans-serif;
}

/* scroll blur effect */
.navbar.scrolled {
    background: rgba(255,255,255,0.65);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.nav-brand {
    color: #0f172a;
    font-size: 22px;
    font-weight: 700;
    text-decoration: none;
}

.nav-menu {
    display: flex;
    gap: 22px;
    align-items: center;
}

.nav-menu a {
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    color: #475569;
    transition: 0.20s ease;
}

.nav-menu a:hover,
.nav-menu a.active {
    color: #0f172a;
    background: #f1f5f9;
}

/* ========== PROFILE DROPDOWN ========== */
.profile-box {
    position: relative;
}

.profile-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
    color: #334155;
    font-weight: 600;
    user-select: none;
}

.profile-btn:hover {
    background: #f1f5f9;
}

/* dropdown menu */
.profile-dropdown {
    position: absolute;
    top: 48px;
    right: 0;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(12px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    border-radius: 14px;
    padding: 8px 0;
    width: 180px;
    display: none;
}

.profile-dropdown.show {
    display: block;
}

.profile-dropdown a {
    display: block;
    padding: 10px 16px;
    font-size: 14px;
    color: #334155;
    text-decoration: none;
    transition: 0.2s ease;
}

.profile-dropdown a:hover {
    background: #f1f5f9;
}

/* logout warna merah */
.profile-dropdown a.logout {
    color: #dc2626;
}
.profile-dropdown a.logout:hover {
    background: #fee2e2;
}

/* ========== MOBILE ========== */
.menu-toggle {
    display: none;
    font-size: 24px;
    cursor: pointer;
    color: #0f172a;
}

@media (max-width: 768px) {
    .menu-toggle { display: block; }

    .nav-menu {
        position: absolute;
        top: 70px;
        right: 15px;
        display: none;
        flex-direction: column;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(12px);
        border-radius: 14px;
        width: 80%;
        padding: 14px 0;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .nav-menu.show { display: flex; }

    .profile-dropdown {
        position: relative;
        top: 0;
        right: 0;
        width: 100%;
        box-shadow: none;
        backdrop-filter: none;
        background: transparent;
    }

    .profile-dropdown a {
        padding-left: 22px;
    }

    body {
    padding-top: 90px !important;
    }

}
</style>

<nav class="navbar" id="mainNavbar">
    <a href="<?= BASE_URL ?>/index.php" class="nav-brand">🎬 My Cinema</a>

    <div class="menu-toggle" id="menuToggle">☰</div>

    <div class="nav-menu" id="navMenu">

        <a href="<?= BASE_URL ?>/index.php"
           class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
            Home
        </a>

        <a href="<?= BASE_URL ?>/film.php"
           class="<?= $currentPage == 'film.php' ? 'active' : '' ?>">
            Film
        </a>

        <?php if ($isLoggedIn): ?>
        <a href="<?= BASE_URL ?>/my_ticket.php"
           class="<?= $currentPage == 'my_ticket.php' ? 'active' : '' ?>">
           My Ticket
        </a>
        <?php endif; ?>

        <?php if ($isLoggedIn && $user['role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/admin/dashboard.php"
           class="<?= str_contains($_SERVER['PHP_SELF'], 'admin') ? 'active' : '' ?>">
           Admin
        </a>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>

        <div class="profile-box">
            <div class="profile-btn" id="profileBtn">
                👤 <?= htmlspecialchars($user['name']) ?>
            </div>

            <div class="profile-dropdown" id="profileDropdown">
                <a href="<?= BASE_URL ?>/profil.php">My Profile</a>
                <a href="<?= BASE_URL ?>/logout.php" class="logout"
                   onclick="return confirm('Yakin ingin logout?')">
                   Logout
                </a>
            </div>
        </div>

        <?php else: ?>
        <a href="<?= BASE_URL ?>/login.php"
           class="<?= $currentPage == 'login.php' ? 'active' : '' ?>">
           Login
        </a>
        <?php endif; ?>
    </div>
</nav>

<script>
// toggle menu mobile
document.getElementById("menuToggle").addEventListener("click", function () {
    document.getElementById("navMenu").classList.toggle("show");
});

// blur on scroll
window.addEventListener("scroll", function () {
    const navbar = document.getElementById("mainNavbar");
    navbar.classList.toggle("scrolled", window.scrollY > 10);
});

// dropdown profil
const profileBtn = document.getElementById("profileBtn");
const dropdown = document.getElementById("profileDropdown");

profileBtn?.addEventListener("click", function () {
    dropdown.classList.toggle("show");
});

// tutup dropdown jika klik di luar
document.addEventListener("click", function (e) {
    if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
    }
});
</script>
