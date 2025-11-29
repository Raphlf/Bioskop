<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

require_login();
$user = $_SESSION['user'];

if (!isset($_POST['booking_id'])) {
    die("Invalid request.");
}

$booking_id = (int)$_POST['booking_id'];

// Hapus dulu seatnya
$pdo->prepare("DELETE FROM booking_seats WHERE booking_id = ?")
    ->execute([$booking_id]);

// Hapus booking
$pdo->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?")
    ->execute([$booking_id, $user['id']]);

header("Location: index.php?msg=cancelled");
exit;
