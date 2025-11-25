<?php
require_once __DIR__ . '/../src/db.php';
date_default_timezone_set('Asia/Jakarta');

// Cek apakah data dikirim
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$reservation_id = $_POST['reservation_id'] ?? null;
$seats = $_POST['seats'] ?? [];

if (!$reservation_id || empty($seats)) {
    die("Data kursi tidak lengkap.");
}

try {
    // Insert setiap seat yang dipilih ke tabel reservation_seats
    $stmt = $pdo->prepare("
        INSERT INTO reservation_seats (reservation_id, seat_code, created_at)
        VALUES (?, ?, NOW())
    ");

    foreach ($seats as $seat_code) {
        $stmt->execute([$reservation_id, $seat_code]);
    }

    echo "Kursi berhasil disimpan ke tabel reservation_seats.";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
