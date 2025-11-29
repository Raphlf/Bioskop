<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

// Ambil semua booking user yang sudah CONFIRMED (sudah bayar)
$stmt = $pdo->prepare("
    SELECT b.*, 
           s.show_time,
           s.price,
           f.title, 
           f.poster,
           st.name AS studio_name
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    JOIN films f ON s.film_id = f.id
    JOIN studios st ON s.studio_id = st.id
    WHERE b.user_id = ?
      AND b.status = 'confirmed'
    ORDER BY b.created_at DESC
");
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

include __DIR__ . '/../src/templates/header.php';
?>

<style>
html, body {
    height: 100%;
    margin: 0;
}
.page-wrapper {
    min-height: calc(100vh - 80px);
    display: flex;
    flex-direction: column;
}
.content-area {
    flex: 1;
}
body {
    background: #fafafa;
    font-family: system-ui, "Segoe UI", sans-serif;
}
.page-title {
    text-align: center;
    margin: 110px 0 26px;
    font-size: 30px;
    font-weight: 800;
    color: #b8962f;
}
.ticket-list {
    max-width: 820px;
    margin: 0 auto 80px;
    display: flex;
    flex-direction: column;
    gap: 22px;
}
.ticket-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.09);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}
.ticket-top {
    display: flex;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 2px dashed #d4af37;
}
.ticket-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.ticket-title {
    font-size: 20px;
    font-weight: 700;
}
.ticket-meta {
    font-size: 14px;
    color: #6b7280;
}
.ticket-poster img {
    width: 80px;
    border-radius: 8px;
}
.ticket-bottom {
    display: flex;
    justify-content: space-between;
    padding: 18px 22px;
    font-size: 15px;
}
.tb-label {
    font-size: 12px;
    color: #6b7280;
}
.tb-value {
    font-weight: 700;
    color: #b8962f;
}
.empty-text {
    margin-top: 70px;
    text-align: center;
    color: #6b7280;
    font-size: 17px;
}
</style>

<div class="page-wrapper">
<div class="content-area">

<h2 class="page-title">My Ticket</h2>

<?php if (count($bookings) === 0): ?>
    <p class="empty-text">Belum ada tiket yang kamu beli.</p>
<?php else: ?>

<div class="ticket-list">

<?php foreach ($bookings as $b): ?>

<?php
// Ambil kursi berdasarkan booking_id
$seatStmt = $pdo->prepare("
    SELECT seat_number 
    FROM booking_seats bs
    JOIN seats s ON s.id = bs.seat_id
    WHERE bs.booking_id = ?
");
$seatStmt->execute([$b['id']]);
$seats = $seatStmt->fetchAll(PDO::FETCH_COLUMN);

$seatCount = count($seats);
$seatText  = implode(", ", $seats);
?>

<div class="ticket-card">

    <div class="ticket-top">
        <div class="ticket-info">
            <div class="ticket-title"><?= esc($b['title']) ?></div>

            <div class="ticket-meta">
                <?= date("d M Y", strtotime($b['show_time'])) ?> – 
                <?= date("H:i", strtotime($b['show_time'])) ?>
            </div>

            <div class="ticket-meta">
                Studio: <?= esc($b['studio_name']) ?>
            </div>
        </div>

        <div class="ticket-poster">
            <img src="<?= BASE_URL . '/' . esc($b['poster']) ?>">
        </div>
    </div>

    <div class="ticket-bottom">
        <div>
            <div class="tb-label">Jumlah Tiket</div>
            <div class="tb-value"><?= $seatCount ?> Orang</div>
        </div>

        <div>
            <div class="tb-label">Kursi</div>
            <div class="tb-value"><?= $seatText ?></div>
        </div>

        <div>
            <div class="tb-label">Kode</div>
            <div class="tb-value">#<?= $b['id'] ?></div>
        </div>
    </div>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div>
</div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
