<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/auth.php';
require_admin();

$films_count = $pdo->query("SELECT COUNT(*) AS c FROM films")->fetch()['c'];
$schedules_count = $pdo->query("SELECT COUNT(*) AS c FROM schedules")->fetch()['c'];
$res_count = $pdo->query("SELECT COUNT(*) AS c FROM reservations")->fetch()['c'];
?>

<?php include __DIR__ . '/../../src/templates/header.php'; ?>

<h2>Dashboard Admin</h2>
<div class="admin-stats">
    <div class="stat">Films: <?= $films_count ?></div>
    <div class="stat">Schedules: <?= $schedules_count ?></div>
    <div class="stat">Reservations: <?= $res_count ?></div>
</div>

<p>
    <a href="<?= BASE_URL ?>/admin/films_manage.php" class="btn">Manage Films</a>
    <a href="<?= BASE_URL ?>/admin/jadwal_manage.php" class="btn">Manage Jadwal</a>
    <a href="<?= BASE_URL ?>/admin/users_manage.php" class="btn">Manage Users</a>
</p>

<?php include __DIR__ . '/../../src/templates/footer.php'; ?>
