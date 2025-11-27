<?php
require_once __DIR__ . '/../../src/db.php';

if (!isset($_GET['id'])) {
    header("Location: jadwal_manage.php?error=ID jadwal tidak ditemukan");
    exit;
}

$id = $_GET['id'];

// Hapus jadwal
$stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
$stmt->execute([$id]);

header("Location: jadwal_manage.php?success=Jadwal berhasil dihapus");
exit;
