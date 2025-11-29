<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

require_login();

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;

if ($booking_id <= 0) {
    header("Location: my_ticket.php?msg=invalid");
    exit;
}

// Pastikan booking milik user
$stmt = $pdo->prepare("SELECT id, status, user_id FROM bookings WHERE id = ? LIMIT 1");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking || $booking['user_id'] != $user['id']) {
    header("Location: my_ticket.php?msg=notfound");
    exit;
}

try {
    $pdo->beginTransaction();

    // Hapus kursi supaya kembali tersedia
    $stmt = $pdo->prepare("DELETE FROM booking_seats WHERE booking_id = ?");
    $stmt->execute([$booking_id]);

    // Ubah status menjadi canceled
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'canceled', total_price = 0 WHERE id = ?");
    $stmt->execute([$booking_id]);

    $pdo->commit();

    header("Location: my_ticket.php?msg=canceled");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: my_ticket.php?msg=error");
    exit;
}
