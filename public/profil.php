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
/* ==================== Profile Light Mode ==================== */
body {
    background: #f4f6f8;
    color: #1f2937;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding-top: 110px;   /* FIX ketutupan navbar */
}

h2, h3 {
    color: #0f172a;
    letter-spacing: 0.5px;
    text-align: center;
    margin-bottom: 20px;
}

/* CARD PROFIL */
.profil-card {
    background: #ffffff;
    padding: 32px;
    border-radius: 18px;
    max-width: 700px;
    margin: 30px auto;
    box-shadow: 0 8px 30px rgba(0,0,0,0.09);
    border: 1px solid #e5e7eb;
    animation: fadein 0.4s ease;
}

@keyframes fadein {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* FOTO PROFIL */
.profil-header {
    display: flex;
    align-items: center;
    gap: 28px;
    margin-bottom: 20px;
}

.profil-foto {
    width: 95px;
    height: 95px;
    background: #6366f1;
    color: white;
    font-size: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 18px rgba(99,102,241,0.3);
}

/* LABEL ROLE */
.badge {
    background: #e0e7ff;
    color: #4338ca;
    padding: 6px 14px;
    border-radius: 22px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

/* BUTTON */
.btn {
    display: inline-block;
    background: #6366f1;
    color: white !important;
    padding: 12px 24px;
    margin-top: 18px;
    border-radius: 12px;
    font-weight: 700;
    transition: 0.25s ease;
    box-shadow: 0 5px 16px rgba(99,102,241,0.3);
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}
.btn:hover {
    background: #4f46e5;
    box-shadow: 0 6px 20px rgba(79,70,229,0.45);
}

/* TABEL RIWAYAT */
.table {
    width: 94%;
    margin: 15px auto 40px;
    border-collapse: collapse;  
    background: #ffffff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
}

.table th {
    background: #f3f4f6;
    padding: 16px;
    font-size: 15px;
    font-weight: 700;
    color: #374151;
    text-align: center;
}

.table td {
    padding: 13px;
    text-align: center;
    border-bottom: 1px solid #e5e7eb;
    color: #4b5563;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tr:hover {
    background: #f9fafb;
}

.table img {
    border-radius: 9px;
    box-shadow: 0 0 6px rgba(0,0,0,0.05);
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
