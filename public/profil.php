<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$resv = $pdo->prepare("
    SELECT COUNT(*) AS total_reservasi, 
           COALESCE(SUM(seats),0) AS total_kursi
    FROM reservations 
    WHERE user_id = ?");
$resv->execute([$user_id]);
$stat = $resv->fetch();

$q = $pdo->prepare("
    SELECT r.*, s.show_date, s.show_time, 
           f.title, f.poster
    FROM reservations r
    JOIN schedules s ON r.schedule_id = s.id
    JOIN films f ON s.film_id = f.id
    WHERE r.user_id = ?
    ORDER BY r.booking_time DESC");
$q->execute([$user_id]);
$riwayat_list = $q->fetchAll();
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

.profil-card {
    background: #111;
    padding: 25px;
    border-radius: 16px;
    max-width: 480px;
    margin: 20px auto;
    box-shadow: 0 0 20px rgba(255, 40, 40, .25);
    border: 1px solid #222;
}

.profil-header {
    display: flex;
    align-items: center;
    gap: 20px;
}

.profil-foto {
    width: 90px;
    height: 90px;
    background: #ff4444;
    color: white;
    font-size: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 15px rgba(255, 50, 50, .6);
}

.badge {
    background: #ff4444;
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    display: inline-block;
}

.btn {
    display: inline-block;
    background: #ff4444;
    color: white !important;
    padding: 10px 18px;
    margin-top: 15px;
    border-radius: 10px;
    font-weight: bold;
    transition: 0.25s;
}

.btn:hover {
    background: #cc0000;
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

.table td:nth-child(7) {
    font-weight: bold;
    color: #ff4444;
}
</style>

<h2>My Profile</h2>

<div class="profil-card">
    <div class="profil-header">
        <div class="profil-foto">👤</div>
        <div>
            <h3><?= esc($user['name']) ?></h3>
            <p><?= esc($user['email']) ?></p>
            <span class="badge"><?= strtoupper($user['role']) ?></span>
        </div>
    </div>

    <hr>

    <p><strong>ID Pengguna:</strong> <?= $user['id'] ?></p>
    <p><strong>Tanggal Buat Akun:</strong> <?= $user['created_at'] ?></p>
    <p><strong>Total Reservasi:</strong> <?= $stat['total_reservasi'] ?></p>
    <p><strong>Total Kursi Dipesan:</strong> <?= $stat['total_kursi'] ?></p>

    <a href="edit_profil.php" class="btn">Edit Profil</a>
</div>

<br><br>

<h3>Riwayat Transaksi</h3>

<?php if (count($riwayat_list) == 0): ?>
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

    <?php foreach ($riwayat_list as $r): ?>
    <tr>
        <td>
            <img src="<?= BASE_URL . '/' . esc($r['poster']) ?>"
                 style="width:60px; height:90px; object-fit:cover;">
        </td>

        <td><?= esc($r['title']) ?></td>
        <td><?= esc($r['show_date']) ?></td>
        <td><?= esc($r['show_time']) ?></td>
        <td><?= esc($r['seats']) ?></td>
        <td>Rp<?= number_format($r['total_price'], 0, ',', '.') ?></td>
        <td><?= esc($r['status']) ?></td>
        <td><?= esc($r['booking_time']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php endif; ?>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
