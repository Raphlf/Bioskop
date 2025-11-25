<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = intval($_POST['schedule_id']);

    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? LIMIT 1");
    $stmt->execute([$schedule_id]);
    $sch = $stmt->fetch();

    if (!$sch) {
        $errors[] = 'Jadwal tidak ditemukan';
    } elseif ($sch['seats_available'] < 1) {
        $errors[] = 'Maaf, kursi tidak tersedia untuk jadwal ini.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, schedule_id, seats, total_price, status) VALUES (?, ?, 0, 0, 'pending')");
        $stmt->execute([$user['id'], $schedule_id]);
        $reservation_id = $pdo->lastInsertId();

        header('Location: choose_seat.php?reservation_id=' . $reservation_id);
        exit;
    }
}

$stmt = $pdo->prepare("SELECT r.*, s.show_date, s.show_time, f.title, f.poster
    FROM reservations r
    JOIN schedules s ON r.schedule_id = s.id
    JOIN films f ON s.film_id = f.id
    WHERE r.user_id = ?
    ORDER BY r.booking_time DESC");
$stmt->execute([$user['id']]);
$reservations = $stmt->fetchAll();

$jadwals = $pdo->query("SELECT s.*, f.title FROM schedules s JOIN films f ON s.film_id = f.id ORDER BY s.show_date, s.show_time")->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
body {
    background: #3b0d0d;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #f1d6d0;
}
h2, h3 { color: #ffdddd; text-align: center; }

.form-card {
    background: #2a1a1a;
    padding: 30px;
    border-radius: 12px;
    width: 480px;
    margin: 20px auto;
    box-shadow: 0 0 20px #000;
}
.form-card label { color: #d4af97; font-weight: bold; }
.form-card select, .form-card button {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
}
.form-card button {
    background: #800000;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: bold;
}

.table {
    width: 95%;
    margin: auto;
    border-collapse: collapse;
    color: #f1d6d0;
    background: #2a1a1a;
}
.table th {
    background: #800000;
    padding: 12px;
}
.table td {
    padding: 10px;
    border-bottom: 1px solid #5e3b1a;
}
</style>

<h2>Reservasi Tiket Bioskop</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red; text-align:center;"><?= implode('<br>', $errors) ?></div>
<?php endif; ?>

<div class="form-card">
    <form method="POST">
        <label>Pilih Jadwal Film</label>
        <select name="schedule_id" required>
            <option value="">-- Pilih Jadwal --</option>
            <?php foreach ($jadwals as $j): ?>
                <option value="<?= $j['id'] ?>">
                    <?= $j['title'] ?> - <?= $j['show_date'] ?> <?= substr($j['show_time'],0,5) ?> - Rp<?= number_format($j['price']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Pesan Tiket</button>
    </form>
</div>

<h3>Daftar Reservasi</h3>

<?php if (count($reservations) == 0): ?>
    <p style="text-align:center;">Belum ada reservasi.</p>
<?php else: ?>
<table class="table">
    <tr>
        <th>Poster</th>
        <th>Film</th>
        <th>Tanggal</th>
        <th>Jam</th>
        <th>Kursi</th>
        <th>Total</th>
        <th>Status</th>
        <th>Waktu</th>
    </tr>
    <?php foreach ($reservations as $r): ?>
    <tr>
        <td><img src="<?= BASE_URL . '/' . $r['poster'] ?>" width="60"></td>
        <td><?= $r['title'] ?></td>
        <td><?= $r['show_date'] ?></td>
        <td><?= $r['show_time'] ?></td>
        <td><?= $r['seats'] ?></td>
        <td>Rp<?= number_format($r['total_price']) ?></td>
        <td><?= $r['status'] ?></td>
        <td><?= $r['booking_time'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
