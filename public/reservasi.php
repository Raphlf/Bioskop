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

$stmt = $pdo->prepare("SELECT r.*, s.show_date, s.show_time, f.title, f.poster
    FROM reservations r
    JOIN schedules s ON r.schedule_id = s.id
    JOIN films f ON s.film_id = f.id
    WHERE r.user_id = ? ORDER BY r.booking_time DESC");
$stmt->execute([$user['id']]);
$reservations = $stmt->fetchAll();
$jadwals = $pdo->query("SELECT s.*, f.title FROM schedules s JOIN films f ON s.film_id = f.id ORDER BY s.show_date, s.show_time")->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
body {
    background: #0c0c0c;
    color: #eee;
    font-family: "Poppins", sans-serif;
}

h2, h3 {
    color: #ff4444;
    letter-spacing: 1px;
}

.form-card {
    background: #111;
    padding: 25px;
    border-radius: 16px;
    max-width: 480px;
    margin: 20px auto;
    box-shadow: 0 0 20px rgba(255, 40, 40, .25);
    border: 1px solid #222;
}

.form-card label {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    color: #eee;
}

.form-card input,
.form-card select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #333;
    background: #111;
    color: #eee;
}

.form-card button {
    width: 100%;
    padding: 12px;
    background: #ff4444;
    border: none;
    border-radius: 10px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.form-card button:hover {
    background: #cc0000;
}

.error {
    color: #ff8888;
    margin-bottom: 10px;
}

.table {
    width: 95%;
    margin: auto;
    border-collapse: collapse;  
    background: #111;
    border-radius: 12px;
    overflow: hidden;            
    box-shadow: 0 0 15px rgba(255, 40, 40, .2);
    border: none !important;     
    outline: none !important;
}

.table th {
    background: #ff4444;
    padding: 12px;
    font-size: 14px;
    border: none;               
    color: #fff;
    text-align: center;
}

.table td {
    padding: 10px;
    text-align: center;
    border: none;                
    border-bottom: 1px solid #222; 
    color: #ddd;
}

.table tr:last-child td {
    border-bottom: none;         
}

.table tr:hover {
    background: rgba(255, 50, 50, 0.1);
}

.table img {
    border-radius: 6px;
    box-shadow: 0 0 5px rgba(255, 50, 50, .5);
}
</style>

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

<?php if(count($reservations) == 0): ?>
    <p style="text-align:center;">Belum ada transaksi.</p>
<?php else: ?>
<table class="table">
    <tr>
        <th>Poster</th>
        <th>Film</th>
        <th>Tanggal</th>
        <th>Jam</th>
        <th>Kursi</th>
        <th>Total Harga</th>
        <th>Status</th>
        <th>Waktu Pesan</th>
    </tr>
    <?php foreach($reservations as $r): ?>
    <tr>
        <td>
            <?php if($r['poster']): ?>
            <img src="<?= BASE_URL . '/' . esc($r['poster']) ?>" style="width:60px; height:90px; object-fit:cover;">
            <?php endif; ?>
        </td>
        <td><?= esc($r['title']) ?></td>
        <td><?= esc($r['show_date']) ?></td>
        <td><?= esc($r['show_time']) ?></td>
        <td><?= esc($r['seats']) ?></td>
        <td>Rp<?= number_format($r['total_price'],0,',','.') ?></td>
        <td><?= esc($r['status']) ?></td>
        <td><?= esc($r['booking_time']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
