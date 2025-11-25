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
    SELECT COUNT(*) AS total_reservasi
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
    background: #121212;
    color: #e0e0e0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
}

h2, h3 {
    color: #33aa77;
    letter-spacing: 1px;
    text-align: center;
    margin-bottom: 20px;
}

.profil-card {
    background: #1f1f1f;
    padding: 30px;
    border-radius: 14px;
    max-width: 520px;
    margin: 20px auto;
    box-shadow: 0 0 30px rgba(50, 180, 150, 0.5);
    border: none;
}

.profil-header {
    display: flex;
    align-items: center;
    gap: 25px;
}

.profil-foto {
    width: 90px;
    height: 90px;
    background: #33aa77;
    color: white;
    font-size: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 18px #33aa77;
}

.badge {
    background: #33aa77;
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-block;
}

.btn {
    display: inline-block;
    background: #33aa77;
    color: white !important;
    padding: 12px 22px;
    margin-top: 18px;
    border-radius: 12px;
    font-weight: 700;
    transition: background-color 0.25s ease;
    box-shadow: 0 0 12px #33aa77;
    cursor: pointer;
}

.btn:hover {
    background: #2b8c63;
    box-shadow: 0 0 18px #2b8c63;
}

.table {
    width: 95%;
    margin: auto;
    border-collapse: collapse;  
    background: #1f1f1f;
    border-radius: 14px;
    overflow: hidden;            
    box-shadow: 0 0 25px rgba(50, 180, 150, 0.4);
    border: none !important;     
    outline: none !important;
}

.table th {
    background: #33aa77;
    padding: 16px;
    font-size: 15px;
    border: none;               
    color: #e0e0e0;
    text-align: center;
}

.table td {
    padding: 13px;
    text-align: center;
    border: none;                
    border-bottom: 1px solid #264d39; 
    color: #cce9d6;
}

.table tr:last-child td {
    border-bottom: none;         
}

.table tr:hover {
    background: rgba(50, 180, 150, 0.15);
}

.table img {
    border-radius: 9px;
    box-shadow: 0 0 8px rgba(50, 180, 150, 0.7);
}

.table td:nth-child(7) {
    font-weight: bold;
    color: #33aa77;
}
</style>

<h2>My Profile</h2>

<div class="profil-card">
    <div class="profil-header">
        <div class="profil-foto">👤</div>
        <div>
            <h3 style="margin: 0;"><?= esc($user['name']) ?></h3>
            <p><?= esc($user['email']) ?></p>
            <span class="badge"><?= strtoupper($user['role']) ?></span>
        </div>
    </div>

    <hr>

    <p><strong>ID Pengguna:</strong> <?= $user['id'] ?></p>
    <p><strong>Tanggal Buat Akun:</strong> <?= $user['created_at'] ?></p>
    <p><strong>Total Reservasi:</strong> <?= $stat['total_reservasi'] ?></p>
    <p><strong>Total Kursi Dipesan:</strong> 
        <?php 
            // Calculate total seats ordered by user from reservation_seat table
            $total_kursi_stmt = $pdo->prepare("SELECT COUNT(*) AS total_kursi FROM reservation_seats rs JOIN reservations r ON rs.reservation_id = r.id WHERE r.user_id = ?");
            $total_kursi_stmt->execute([$user_id]);
            $total_kursi_row = $total_kursi_stmt->fetch();
            echo $total_kursi_row ? $total_kursi_row['total_kursi'] : 0;
        ?>
    </p>

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
