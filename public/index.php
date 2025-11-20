<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

$stmt = $pdo->query("SELECT s.*, f.title, f.poster 
                     FROM schedules s 
                     JOIN films f ON s.film_id = f.id 
                     ORDER BY s.show_date, s.show_time");
$jadwal = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<h2>Jadwal Film</h2>

<div class="jadwal-list">

<?php foreach($jadwal as $j): ?>
    <div class="jadwal-card">

        <img src="<?= BASE_URL . '/' . esc($j['poster']) ?>" class="poster-img">

        <h3><?= esc($j['title']) ?></h3>
        <p>Tanggal: <?= esc($j['show_date']) ?></p>
        <p>Jam: <?= esc($j['show_time']) ?></p>
        <p>Harga: Rp<?= number_format($j['price']) ?></p>

        <?php if(is_logged_in()): ?>
            <a class="btn" href="<?= BASE_URL ?>/reservasi.php?id=<?= $j['id'] ?>">Pesan</a>
        <?php else: ?>
            <a class="btn" href="<?= BASE_URL ?>/login.php">Login untuk pesan</a>
        <?php endif; ?>

    </div>
<?php endforeach; ?>

</div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
