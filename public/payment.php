<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak valid.");
}

$schedule_id = (int)$_POST['schedule_id'];
$seats = $_POST['seats'] ?? [];

if (empty($seats)) {
    die("Tidak ada kursi dipilih.");
}

//AMBIL DATA SCHEDULE
$stmt = $pdo->prepare("
    SELECT s.*, f.title, st.name AS studio_name 
    FROM schedules s
    JOIN films f ON s.film_id = f.id
    JOIN studios st ON s.studio_id = st.id
    WHERE s.id = ?
");
$stmt->execute([$schedule_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    die("Jadwal tidak ditemukan.");
}

$total_price = count($seats) * $schedule['price'];

// INSERT BOOKINGS
$stmt = $pdo->prepare("
    INSERT INTO bookings (user_id, schedule_id, total_price, status) 
    VALUES (?, ?, ?, 'pending')
");
$stmt->execute([$user['id'], $schedule_id, $total_price]);

$booking_id = $pdo->lastInsertId();

// INSERT BOOKING_SEATS
$bs = $pdo->prepare("INSERT INTO booking_seats (booking_id, seat_id) VALUES (?, ?)");

foreach ($seats as $s) {
    $bs->execute([$booking_id, $s]);
}

header("Location: payment_detail.php?booking_id=" . $booking_id);
exit;
