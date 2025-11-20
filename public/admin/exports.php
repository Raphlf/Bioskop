<?php
// simple CSV export example for reservations
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/auth.php';
require_admin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reservations.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['ID','User','Film','Tanggal','Waktu','Seats','Total','Status','BookedAt']);

$stmt = $pdo->query("SELECT r.*, u.name AS user_name, f.title AS film_title, s.show_date, s.show_time
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN schedules s ON r.schedule_id = s.id
    JOIN films f ON s.film_id = f.id");
while ($row = $stmt->fetch()) {
    fputcsv($out, [
        $row['id'], $row['user_name'], $row['film_title'], $row['show_date'], $row['show_time'],
        $row['seats'], $row['total_price'], $row['status'], $row['booking_time']
    ]);
}
fclose($out);
exit;
?>