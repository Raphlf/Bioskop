<?php
session_start();
require_once __DIR__ . '/config.php';

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function require_admin() {
    if (!is_logged_in() || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo "Akses ditolak.";
        exit;
    }
}
?>