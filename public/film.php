<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

$stmt = $pdo->query("SELECT * FROM films ORDER BY created_at DESC");
$films = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
    h2 {
        text-align: center;
        margin: 30px 0;
        color: #7ecbff;
        font-size: 30px;
    }

    .film-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        width: 90%;
        max-width: 1300px;
        margin: auto;
    }

    .film-card {
        background: #1a1a1a;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.4);
        transition: 0.25s;
        color: #ddd;
    }

    .film-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 15px rgba(126,203,255,0.3);
    }

    .poster {
        width: 100%;
        height: 320px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 12px;
    }

    .film-card h3 {
        margin: 10px 0 5px;
        color: #7ecbff;
        font-size: 20px;
    }

    .film-card p {
        margin: 5px 0;
        font-size: 15px;
        line-height: 1.4;
        color: #ccc;
    }

    @media (max-width: 600px) {
        .film-list {
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        }

        .poster {
            height: 240px;
        }
    }
</style>

<h2>Film</h2>

<div class="film-list">
<?php foreach($films as $f): ?>
    <div class="film-card">

        <?php if($f['poster']): ?>
            <img src="<?= BASE_URL ?>/<?= esc($f['poster']) ?>"
                 alt="<?= esc($f['title']) ?>"
                 class="poster">
        <?php endif; ?>

        <h3><?= esc($f['title']) ?></h3>
        <p><?= esc($f['genre']) ?> • <?= esc($f['duration']) ?> menit</p>
        <p><?= esc($f['description']) ?></p>

    </div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
