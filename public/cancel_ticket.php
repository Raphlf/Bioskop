<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

require_login();
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my_ticket.php");
    exit;
}

$booking_id = (int)$_POST['booking_id'];

$stmt = $pdo->prepare("SELECT id, user_id FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking || $booking['user_id'] != $user['id']) {
    header("Location: my_ticket.php?msg=notfound");
    exit;
}

try {
    $pdo->beginTransaction();

    $pdo->prepare("DELETE FROM booking_seats WHERE booking_id = ?")
        ->execute([$booking_id]);

    $pdo->prepare("UPDATE bookings SET status='canceled', total_price=0 WHERE id = ?")
        ->execute([$booking_id]);

    $pdo->commit();

    header("Location: my_ticket.php?msg=canceled");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: my_ticket.php?msg=error");
    exit;
}
