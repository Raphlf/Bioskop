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
        color: #33aa77;
        font-size: 32px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .film-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 28px;
        width: 90%;
        max-width: 1320px;
        margin: auto;
    }

    .film-card {
        background: #1f1f1f;
        padding: 22px;
        border-radius: 14px;
        box-shadow: 0 0 22px rgba(50, 180, 150, 0.45);
        transition: 0.3s ease;
        color: #d0f8e8;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .film-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 0 24px #33aa77;
    }

    .poster {
        width: 100%;
        height: 320px;
        object-fit: cover;
        border-radius: 14px;
        margin-bottom: 16px;
        box-shadow: 0 0 12px #33aa77;
    }

    .film-card h3 {
        margin: 10px 0 8px;
        color: #33aa77;
        font-size: 22px;
        font-weight: 700;
    }

    .film-card p {
        margin: 5px 0;
        font-size: 16px;
        line-height: 1.5;
        color: #b2d8c6;
    }

    @media (max-width: 600px) {
        .film-list {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        }

        .poster {
            height: 260px;
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
