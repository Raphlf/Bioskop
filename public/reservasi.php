<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

$errors = [];

function create_reservation($pdo, $userId, $schedule_id, &$errors) {
    // cek jadwal
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? LIMIT 1");
    $stmt->execute([$schedule_id]);
    $sch = $stmt->fetch();

    if (!$sch) {
        $errors[] = 'Jadwal tidak ditemukan';
        return null;
    } elseif ($sch['seats_available'] < 1) {
        $errors[] = 'Maaf, kursi tidak tersedia untuk jadwal ini.';
        return null;
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, schedule_id, seats, total_price, status) 
                               VALUES (?, ?, 0, 0, 'pending')");
        $stmt->execute([$userId, $schedule_id]);
        return $pdo->lastInsertId();
    }
    return null;
}

// dari tombol GET TICKET
if (isset($_GET['schedule_id'])) {
    $schedule_id = intval($_GET['schedule_id']);
    $reservation_id = create_reservation($pdo, $user['id'], $schedule_id, $errors);
    if ($reservation_id) {
        header('Location: choose_seat.php?reservation_id=' . $reservation_id);
        exit;
    }
}

// dari form manual
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = intval($_POST['schedule_id']);
    $reservation_id = create_reservation($pdo, $user['id'], $schedule_id, $errors);
    if ($reservation_id) {
        header('Location: choose_seat.php?reservation_id=' . $reservation_id);
        exit;
    }
}

// untuk dropdown jadwal
$jadwals = $pdo->query("SELECT s.*, f.title 
    FROM schedules s 
    JOIN films f ON s.film_id = f.id 
    ORDER BY s.show_date, s.show_time")->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
body {
    background: #f9fafb;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #111827;
}
h2 {
    text-align: center;
    margin-top: 26px;
}
.form-card {
    background: #ffffff;
    padding: 24px;
    border-radius: 14px;
    width: 480px;
    max-width: 95%;
    margin: 18px auto 40px;
    box-shadow: 0 12px 28px rgba(15,23,42,0.12);
}
.form-card label {
    font-weight: 600;
    font-size: 14px;
}
.form-card select,
.form-card button {
    width: 100%;
    padding: 10px 12px;
    margin-top: 10px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
}
.form-card button {
    background: #ef4444;
    color: white;
    border: none;
    font-weight: 700;
    margin-top: 14px;
    cursor: pointer;
}
.form-card button:hover {
    background: #dc2626;
}
.error-box {
    text-align:center;
    color:#b91c1c;
    margin-top:12px;
}
</style>

<h2>Pesan Tiket</h2>

<?php if (!empty($errors)): ?>
    <div class="error-box"><?= implode('<br>', array_map('esc', $errors)) ?></div>
<?php endif; ?>

<div class="form-card">
    <form method="POST">
        <label for="schedule_id">Pilih Jadwal Film</label>
        <select name="schedule_id" id="schedule_id" required>
            <option value="">-- Pilih Jadwal --</option>
            <?php foreach ($jadwals as $j): ?>
                <option value="<?= $j['id'] ?>">
                    <?= esc($j['title']) ?> - <?= esc($j['show_date']) ?> <?= substr($j['show_time'],0,5) ?> - Rp<?= number_format($j['price']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Lanjut Pilih Kursi</button>
    </form>
</div>
