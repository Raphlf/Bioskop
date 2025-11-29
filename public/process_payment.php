<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

require_login();
$user = $_SESSION["user"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}

$booking_id  = intval($_POST["booking_id"]);
$amount_due  = intval($_POST["amount_due"]);
$amount_paid = intval($_POST["amount_paid"]);

if ($amount_paid < $amount_due) {
    die("Uang kurang!");
}

$change = $amount_paid - $amount_due;

// UPDATE BOOKING
$stmt = $pdo->prepare("
    UPDATE bookings 
    SET total_price = ?, status = 'confirmed'
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$amount_due, $booking_id, $user["id"]]);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Pembayaran Berhasil</title>

<style>
body {
    background: #fafafa;
    font-family: system-ui, "Segoe UI", sans-serif;
    margin: 0;
    padding: 0;
}

.success-card {
    max-width: 500px;
    margin: 130px auto;
    background: white;
    border-radius: 20px;
    padding: 26px;
    text-align: center;
    box-shadow: 0 12px 28px rgba(0,0,0,0.09);
    border: 1px solid #e5e7eb;
}

.success-title {
    font-size: 26px;
    font-weight: 800;
    color: #16a34a;
    margin-bottom: 10px;
}

.row-label {
    font-size: 14px;
    color: #6b7280;
}

.row-value {
    font-size: 18px;
    font-weight: 700;
    color: #b8962f;
    margin-bottom: 12px;
}

.btn-home {
    display: block;
    margin-top: 20px;
    padding: 12px;
    background: #b8962f;
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
}
.btn-home:hover {
    background: #a18429;
}
</style>

</head>
<body>

<div class="success-card">

    <div class="success-title">Pembayaran Berhasil!</div>

    <div class="row-label">Total Harga</div>
    <div class="row-value">Rp <?= number_format($amount_due, 0, ',', '.') ?></div>

    <div class="row-label">Uang Dibayar</div>
    <div class="row-value">Rp <?= number_format($amount_paid, 0, ',', '.') ?></div>

    <div class="row-label">Kembalian</div>
    <div class="row-value">Rp <?= number_format($change, 0, ',', '.') ?></div>

    <a href="my_ticket.php" class="btn-home">Lihat Tiket Saya</a>

</div>

</body>
</html>
