<?php
require_once __DIR__ . "/../../src/db.php";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=jadwal.csv");

$output = fopen("php://output", "w");

// Header CSV
fputcsv($output, ["ID", "Film", "Studio", "Tanggal", "Jam"]);

$stmt = $pdo->query("
    SELECT j.id, f.title AS film, s.name AS studio, j.tanggal, j.jam
    FROM schedule j
    JOIN films f ON f.id = j.film_id
    JOIN studio s ON s.id = j.studio_id
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
