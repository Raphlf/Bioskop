<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header("Location: film.php");
    exit;
}

$schedule_id = intval($_GET['schedule_id']);

// Ambil data jadwal
$stmt = $pdo->prepare("SELECT s.*, f.title, st.name AS studio_name, s.price
    FROM schedules s
    JOIN films f ON s.film_id = f.id
    JOIN studios st ON s.studio_id = st.id
    WHERE s.id = ? LIMIT 1");
$stmt->execute([$schedule_id]);
$sch = $stmt->fetch();

if (!$sch) {
    die("Jadwal tidak ditemukan.");
}

// Buat booking
$stmt = $pdo->prepare("INSERT INTO bookings (user_id, schedule_id, total_price)
                       VALUES (?, ?, 0)");
$stmt->execute([$user['id'], $schedule_id]);
$booking_id = $pdo->lastInsertId();

header("Location: choose_seat.php?booking_id=" . $booking_id);
exit;
?>
