<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = intval($_POST['schedule_id']);
    $seats = intval($_POST['seats']);

    if ($seats <= 0) $errors[] = 'Jumlah kursi tidak valid';

    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? LIMIT 1");
    $stmt->execute([$schedule_id]);
    $sch = $stmt->fetch();

    if (!$sch) $errors[] = 'Jadwal tidak ditemukan';
    if ($seats > $sch['seats_available']) $errors[] = 'Kursi tidak cukup tersedia';

    if (empty($errors)) {
        $total = $seats * $sch['price'];
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, schedule_id, seats, total_price) VALUES (?,?,?,?)");
        $stmt->execute([$user['id'], $schedule_id, $seats, $total]);

        $stmt = $pdo->prepare("UPDATE schedules SET seats_available = seats_available - ? WHERE id = ?");
        $stmt->execute([$seats, $schedule_id]);

        header('Location: reservasi.php');
        exit;
    }
}

$stmt = $pdo->prepare("SELECT r.*, s.show_date, s.show_time, f.title FROM reservations r
    JOIN schedules s ON r.schedule_id = s.id
    JOIN films f ON s.film_id = f.id
    WHERE r.user_id = ? ORDER BY r.booking_time DESC");
$stmt->execute([$user['id']]);
$reservations = $stmt->fetchAll();

$jadwals = $pdo->query("SELECT s.*, f.title FROM schedules s JOIN films f ON s.film_id = f.id ORDER BY s.show_date, s.show_time")->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<h2>Reservasi</h2>

<form method="POST" class="form-card">
    <label>Pilih Jadwal</label>
    <select name="schedule_id" required>
        <?php foreach($jadwals as $j): ?>
            <option value="<?= $j['id'] ?>"><?= esc($j['title']) ?> | <?= esc($j['show_date']) ?> <?= esc($j['show_time']) ?> | Sisa: <?= $j['seats_available'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Jumlah Kursi</label>
    <input type="number" name="seats" min="1" required>

    <?php if(!empty($errors)): ?>
        <ul class="error">
            <?php foreach($errors as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <button type="submit">Pesan</button>
</form>

<h3>Daftar Reservasiku</h3>
<table class="table">
    <thead>
        <tr><th>Film</th><th>Tanggal</th><th>Jam</th><th>Kursi</th><th>Total</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach($reservations as $r): ?>
            <tr>
                <td><?= esc($r['title']) ?></td>
                <td><?= esc($r['show_date']) ?></td>
                <td><?= esc($r['show_time']) ?></td>
                <td><?= esc($r['seats']) ?></td>
                <td>Rp<?= number_format($r['total_price']) ?></td>
                <td><?= esc($r['status']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
