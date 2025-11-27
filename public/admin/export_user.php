<?php
require_once __DIR__ . "/../../src/db.php";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=users.csv");

$output = fopen("php://output", "w");

// Header CSV
fputcsv($output, ["ID", "Name", "Email", "Role", "Created At"]);

$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
