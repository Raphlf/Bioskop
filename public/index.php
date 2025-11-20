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

<style>
body {
    background: #0e0a1f;
    color: #e6d6ff;
    font-family: Arial, sans-serif;
}

h2 {
    text-align: center;
    margin-top: 25px;
    margin-bottom: 25px;
    font-size: 28px;
    color: #d4b9ff;
}

.jadwal-list {
    margin: 0 auto;
    margin-top: 10px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
    width: 92%;
}

.jadwal-card {
    background: #1b1133;
    padding: 18px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(110, 70, 170, 0.35);
    transition: 0.25s;
    border: 1px solid #3b236b;
}

.jadwal-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 16px rgba(140, 90, 220, 0.45);
}

.poster-img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #4a2b75;
}

.jadwal-card h3 {
    margin-top: 12px;
    color: #e6d6ff;
    font-size: 20px;
}

.jadwal-card p {
    margin: 6px 0;
    color: #c3abff;
    font-size: 15px;
}

.btn {
    display: block;
    width: 90%;
    margin: 12px auto 0;
    padding: 10px 0;
    background: #7b41f5;
    color: #fff !important;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.25s;
}

.btn:hover {
    background: #9c6bff;
    transform: scale(1.05);
}
</style>

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
