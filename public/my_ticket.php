<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

$stmt = $pdo->prepare("SELECT r.*, s.show_date, s.show_time, f.title, f.poster
    FROM reservations r
    JOIN schedules s ON r.schedule_id = s.id
    JOIN films f ON s.film_id = f.id
    WHERE r.user_id = ?
    ORDER BY r.booking_time DESC");
$stmt->execute([$user['id']]);
$reservations = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
/* ============ FIX FOOTER NGANGKAT ============ */
html, body {
    height: 100%;
    margin: 0;
}

.page-wrapper {
    min-height: calc(100vh - 80px); /* navbar + footer fix */
    display: flex;
    flex-direction: column;
}

.content-area {
    flex: 1;
    padding-bottom: 40px;
}

/* ============ THEME PUTIH EMAS ============ */
body {
    background: #fafafa;
    color: #1f2937;
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
}

/* Title */
.page-title {
    text-align: center;
    margin: 110px 0 26px;
    font-size: 30px;
    font-weight: 800;
    color: #b8962f;
}

/* List container */
.ticket-list {
    max-width: 820px;
    margin: 0 auto 80px;
    display: flex;
    flex-direction: column;
    gap: 22px;
}

/* ============ TICKET CARD ============ */
.ticket-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.1);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

/* Top */
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
    color: #1f2937;
}
.ticket-meta {
    font-size: 14px;
    color: #6b7280;
}

/* Poster */
.ticket-poster img {
    width: 80px;
    border-radius: 8px;
}

/* Bottom */
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
    color: #b8962f; /* GOLD */
}

/* Empty text */
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

<?php if (count($reservations) === 0): ?>
    <p class="empty-text">Belum ada tiket yang kamu beli.</p>
<?php else: ?>

<div class="ticket-list">

<?php foreach ($reservations as $r): ?>

    <?php
        $seatText = $r["seats"];
        $seatCount = ($seatText && $seatText !== "0")
            ? count(array_filter(array_map("trim", explode(",", $seatText))))
            : 0;
    ?>

    <div class="ticket-card">

        <div class="ticket-top">
            <div class="ticket-info">
                <div class="ticket-title"><?= esc($r['title']) ?></div>
                <div class="ticket-meta"><?= esc($r['show_date']) ?> – <?= substr($r['show_time'],0,5) ?></div>
                <div class="ticket-meta">Status: <?= esc($r['status']) ?></div>
            </div>

            <div class="ticket-poster">
                <img src="<?= BASE_URL . '/' . esc($r['poster']) ?>">
            </div>
        </div>

        <div class="ticket-bottom">
            <div>
                <div class="tb-label">Jumlah Tiket</div>
                <div class="tb-value"><?= $seatCount ?> Orang</div>
            </div>

            <div>
                <div class="tb-label">Kursi</div>
                <div class="tb-value"><?= esc($seatText) ?></div>
            </div>

            <div>
                <div class="tb-label">Kode</div>
                <div class="tb-value">#<?= $r['id'] ?></div>
            </div>
        </div>

    </div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div> <!-- end content -->
</div> <!-- end wrapper -->

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
