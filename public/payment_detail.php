<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($booking_id <= 0) {
    die("Booking tidak valid.");
}

// Ambil data booking + schedule + film
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
    WHERE b.id = ? AND b.user_id = ?
    LIMIT 1
");
$stmt->execute([$booking_id, $user['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Booking tidak ditemukan.");
}

// Ambil kursi
$stmtSeats = $pdo->prepare("
    SELECT seat_number
    FROM booking_seats bs
    JOIN seats s ON s.id = bs.seat_id
    WHERE bs.booking_id = ?
");
$stmtSeats->execute([$booking_id]);
$seatList = $stmtSeats->fetchAll(PDO::FETCH_COLUMN);

$total_price = count($seatList) * $booking['price'];

include __DIR__ . '/../src/templates/header.php';
?>

<style>
body {
    background: #fafafa;
    font-family: system-ui, "Segoe UI", sans-serif;
    margin: 0;
    padding: 0;
}
.pay-wrapper {
    max-width: 720px;
    margin: 120px auto;
    background: #fff;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    border: 1px solid #e5e7eb;
}
.pay-title {
    font-size: 28px;
    font-weight: 800;
    color: #b8962f;
    margin-bottom: 10px;
}
.pay-sub {
    color: #6b7280;
    margin-bottom: 20px;
}
.pay-row {
    margin: 10px 0;
    font-size: 16px;
}
.pay-label {
    font-weight: 600;
}
.pay-input {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    margin-top: 8px;
    font-size: 16px;
}
.btn-pay {
    width: 100%;
    padding: 12px;
    background: #16a34a;
    border: none;
    color: white;
    font-size: 17px;
    border-radius: 12px;
    cursor: pointer;
    margin-top: 18px;
    font-weight: 700;
}
.btn-pay:hover {
    background: #15803d;
}
.btn-cancel {
    width: 100%;
    padding: 12px;
    background: #dc2626;
    border: none;
    color: white;
    font-size: 17px;
    border-radius: 12px;
    cursor: pointer;
    margin-top: 10px;
    font-weight: 700;
}
.btn-cancel:hover {
    background: #b91c1c;
}
</style>

<div class="pay-wrapper">

    <div class="pay-title">Pembayaran</div>
    <div class="pay-sub"><?= esc($booking['title']) ?> — <?= esc($booking['studio_name']) ?></div>

    <div class="pay-row"><span class="pay-label">Tanggal:</span>
        <?= date("d M Y H:i", strtotime($booking['show_time'])) ?>
    </div>

    <div class="pay-row"><span class="pay-label">Kursi:</span>
        <?= implode(", ", $seatList) ?>
    </div>

    <div class="pay-row"><span class="pay-label">Total Harga:</span>
        Rp <?= number_format($total_price, 0, ',', '.') ?>
    </div>

    <!-- FORM PEMBAYARAN -->
    <form id="payForm" action="process_payment.php" method="POST">
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
        <input type="hidden" id="amount_due" name="amount_due" value="<?= $total_price ?>">

        <div class="pay-row">
            <label class="pay-label">Masukkan Jumlah Uang:</label>
            <input type="number" id="amount_paid" class="pay-input" name="amount_paid" required min="0">
        </div>

        <button class="btn-pay">Bayar Sekarang</button>
    </form>

    <!-- FORM CANCEL -->
    <form id="cancelForm" action="cancel_payment.php" method="POST">
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
        <button class="btn-cancel">Cancel</button>
    </form>

</div>

<!-- POPUP VALIDASI & PROTEKSI -->
<script>
document.getElementById("payForm").addEventListener("submit", function(e) {
    let due = parseInt(document.getElementById("amount_due").value);
    let paid = parseInt(document.getElementById("amount_paid").value);

    if (paid < due) {
        alert("Uang yang Anda masukkan kurang!\nTotal: Rp " + due.toLocaleString() + "\nDibayar: Rp " + paid.toLocaleString());
        e.preventDefault();
        return;
    }

    let confirmPay = confirm("Yakin ingin melakukan pembayaran?");
    if (!confirmPay) {
        e.preventDefault();
    }
});

document.getElementById("cancelForm").addEventListener("submit", function(e) {
    let confirmCancel = confirm("Yakin ingin membatalkan booking ini?");
    if (!confirmCancel) {
        e.preventDefault();
    }
});
</script>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
