<?php
require_once __DIR__ . "/../../src/db.php";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=films.csv");

$output = fopen("php://output", "w");

// Header CSV
fputcsv($output, ["ID", "Title", "Genre", "Duration", "Created At"]);

$stmt = $pdo->query("SELECT id, title, genre, duration, created_at FROM films");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
