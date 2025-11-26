<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

$stmt = $pdo->query("SELECT * FROM films ORDER BY created_at DESC");
$films = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
/* ===================== GLOBAL ===================== */
body {
    background: #f8fafb;
    margin: 0;
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    color: #111827;
}

/* ===================== TITLE ===================== */
.page-title {
    text-align: center;
    margin: 110px 0 30px;
    font-size: 32px;
    font-weight: 800;
    color: #1f2937;
}

/* ===================== GRID ===================== */
.film-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 26px;
    max-width: 1180px;
    width: 90%;
    margin: 0 auto 80px;
}

/* ===================== CARD FIX CLICK ===================== */
.film-card {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    text-align: center;
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
    transition: transform .2s ease, box-shadow .2s ease;
    position: relative;
}
.film-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 30px rgba(0,0,0,0.12);
}

.film-card a {
    display: block;
    width: 100%;
    height: 100%;
    color: inherit;
    text-decoration: none;
}

/* Poster */
.poster {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
}

/* Body */
.film-body {
    padding: 14px 16px 20px;
}
.film-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 6px;
}
.film-sub {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 10px;
}

/* Badge */
.film-badge {
    background: #33aa77;
    color: white;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 10px;
}
</style>

<h2 class="page-title">Daftar Film</h2>

<div class="film-list">

<?php foreach($films as $f): ?>
    <div class="film-card">
        <a href="<?= BASE_URL ?>/film_detail.php?id=<?= $f['id'] ?>">

            <img src="<?= BASE_URL ?>/<?= esc($f['poster']) ?>" 
                 alt="<?= esc($f['title']) ?>" 
                 class="poster">

            <div class="film-body">

                <span class="film-badge">Get Ticket</span>

                <div class="film-title"><?= esc($f['title']) ?></div>

                <div class="film-sub">
                    <?= esc($f['genre']) ?> • <?= esc($f['duration']) ?> menit
                </div>
            </div>
        </a>
    </div>
<?php endforeach; ?>

</div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
