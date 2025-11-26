<?php
require_once __DIR__ . '/../../src/db.php';

/* ============================
   AMBIL FILM
============================ */
$film_id = $_GET['film_id'] ?? 0;
$film = $pdo->prepare("SELECT * FROM films WHERE id = ?");
$film->execute([$film_id]);
$film = $film->fetch();

if (!$film) {
    die("Film tidak ditemukan.");
}

/* ============================
   AMBIL SEMUA JADWAL FILM
============================ */
$schedules = $pdo->prepare("
    SELECT schedules.*, studios.name AS studio_name
    FROM schedules
    JOIN studios ON schedules.studio_id = studios.id
    WHERE film_id = ?
    ORDER BY show_time ASC
");
$schedules->execute([$film_id]);
$schedules = $schedules->fetchAll();

/* ============================
   JIKA ADMIN PILIH JADWAL
============================ */
$selected_schedule_id = $_GET['schedule_id'] ?? null;
$seats = [];

if ($selected_schedule_id) {
    // Cari studio jadwal tersebut
    $query = $pdo->prepare("SELECT studio_id FROM schedules WHERE id = ?");
    $query->execute([$selected_schedule_id]);
    $schedule = $query->fetch();

    if ($schedule) {
        $studio_id = $schedule['studio_id'];

        // Ambil kursi dari studio tersebut
        $seatQuery = $pdo->prepare("SELECT * FROM seats WHERE studio_id = ? ORDER BY id ASC");
        $seatQuery->execute([$studio_id]);
        $seats = $seatQuery->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lihat Kursi</title>
<style>
    body { font-family: Arial; background: #eef1f7; padding: 20px; }
    .container { width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 12px; }
    h2 { margin-bottom: 10px; }
    .schedule-box { padding: 10px; background: #f7f7f7; border-radius: 8px; margin: 10px 0; }
    .seat-grid { display: grid; grid-template-columns: repeat(10, 1fr); gap: 8px; margin-top: 20px; }
    .seat { background: #4e5cff; color: #fff; padding: 12px; border-radius: 6px; text-align: center; }
    .screen { background: #ddd; padding: 10px; text-align: center; border-radius: 6px; margin-top: 20px; }
    a.btn { background: #4e5cff; padding: 8px 12px; border-radius: 6px; color: #fff; text-decoration: none; }
</style>
</head>

<body>
<div class="container">

    <h2>🎫 Kursi untuk Film: <b><?= htmlspecialchars($film['title']) ?></b></h2>
    <a class="btn" href="films_manage.php">← Kembali</a>

    <h3>Pilih Jadwal</h3>

    <?php foreach ($schedules as $s): ?>
        <div class="schedule-box">
            <b><?= $s['studio_name'] ?></b><br>
            Jam: <?= $s['show_time'] ?><br>
            Harga: Rp<?= number_format($s['price']) ?><br><br>

            <a class="btn" 
               href="seats_manage.php?film_id=<?= $film_id ?>&schedule_id=<?= $s['id'] ?>">
               Lihat Kursi
            </a>
        </div>
    <?php endforeach; ?>


    <?php if ($selected_schedule_id && !empty($seats)): ?>
        <h3>Kursi Studio</h3>

        <div class="screen">LAYAR</div>

        <div class="seat-grid">
        <?php foreach ($seats as $seat): ?>
            <div class="seat"><?= $seat['seat_number'] ?></div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>
