<?php
require_once __DIR__ . '/../../src/db.php';

if (!isset($_GET['id'])) {
    header("Location: films_manage.php?error=ID tidak ditemukan");
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM films WHERE id = ?");
$ok = $stmt->execute([$id]);

if ($ok) {
    header("Location: films_manage.php?success=Film berhasil dihapus");
} else {
    header("Location: films_manage.php?error=Gagal menghapus film");
}
exit;
