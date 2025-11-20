<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

$stmt = $pdo->query("SELECT * FROM films ORDER BY created_at DESC");
$films = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<h2>Film</h2>

<div class="film-list">
<?php foreach($films as $f): ?>
    <div class="film-card">
        <?php if($f['poster']): ?>
            <img src="<?= BASE_URL ?>/<?= esc($f['poster']) ?>" alt="<?= esc($f['title']) ?>" class="poster">
        <?php endif; ?>
        <h3><?= esc($f['title']) ?></h3>
        <p><?= esc($f['genre']) ?> - <?= esc($f['duration']) ?> menit</p>
        <p><?= esc($f['description']) ?></p>
    </div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
