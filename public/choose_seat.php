<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user   = $_SESSION['user'];
$errors = [];

// ===================== 1. Ambil booking =====================
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($booking_id <= 0) {
    $errors[] = "Booking ID tidak ditemukan.";
    $booking  = null;
} else {
    $stmt = $pdo->prepare("
        SELECT b.*, s.show_time, s.price, s.studio_id,
               f.title, st.name AS studio_name
        FROM bookings b
        JOIN schedules s ON b.schedule_id = s.id
        JOIN films f     ON s.film_id     = f.id
        JOIN studios st  ON s.studio_id   = st.id
        WHERE b.id = ? AND b.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$booking_id, $user['id']]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $errors[] = "Booking tidak ditemukan atau bukan milikmu.";
    }
}

// ===================== 2. Ambil kursi & kursi terpakai =====================
$seats       = [];
$takenSeats  = [];

if (empty($errors)) {
    // semua kursi di studio ini
    $stmt = $pdo->prepare("SELECT * FROM seats WHERE studio_id = ? ORDER BY id");
    $stmt->execute([$booking['studio_id']]);
    $seats = $stmt->fetchAll();

    // kursi yang sudah diambil untuk jadwal ini oleh booking lain
    $stmt = $pdo->prepare("
        SELECT bs.seat_id
        FROM booking_seats bs
        JOIN bookings b2 ON bs.booking_id = b2.id
        WHERE b2.schedule_id = ? AND b2.id != ?
    ");
    $stmt->execute([$booking['schedule_id'], $booking_id]);
    $takenSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// ===================== 3. HANDLE POST (pilih kursi) =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {

    $seat_id = isset($_POST['seat_id']) ? (int)$_POST['seat_id'] : 0;

    // cek seat valid & milik studio ini
    $stmt = $pdo->prepare("SELECT * FROM seats WHERE id = ? AND studio_id = ? LIMIT 1");
    $stmt->execute([$seat_id, $booking['studio_id']]);
    $seatRow = $stmt->fetch();

    if (!$seatRow) {
        $errors[] = "Kursi yang dipilih tidak valid.";
    } else {
        // cek apakah kursi sudah diambil booking lain
        $stmt = $pdo->prepare("
            SELECT 1
            FROM booking_seats bs
            JOIN bookings b2 ON bs.booking_id = b2.id
            WHERE b2.schedule_id = ? AND bs.seat_id = ? AND b2.id != ?
            LIMIT 1
        ");
        $stmt->execute([$booking['schedule_id'], $seat_id, $booking_id]);
        $already = $stmt->fetchColumn();

        if ($already) {
            $errors[] = "Kursi sudah diambil orang lain.";
        }
    }

    if (empty($errors)) {
        // simpan kursi ke booking_seats
        $pdo->prepare("INSERT INTO booking_seats (booking_id, seat_id) VALUES (?, ?)")
            ->execute([$booking_id, $seat_id]);

        // update total_price di bookings (1 tiket)
        $pdo->prepare("UPDATE bookings SET total_price = ? WHERE id = ?")
            ->execute([$booking['price'], $booking_id]);

        header("Location: my_ticket.php");
        exit;
    }
}

include __DIR__ . '/../src/templates/header.php';
?>

<style>
body {
    background:#f1f5f9;
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
}
.seat-wrapper {
    max-width:900px;
    margin:110px auto 50px;
    text-align:center;
}
.seat-title {
    font-size:24px;
    font-weight:800;
    margin-bottom:6px;
}
.seat-sub {
    color:#64748b;
    margin-bottom:20px;
}
.seat-errors {
    color:#b91c1c;
    margin-bottom:15px;
}
.screen-bar {
    width:60%;
    margin:0 auto 20px;
    padding:10px;
    background:#111827;
    color:white;
    border-radius:10px;
}
.seat-grid {
    display:grid;
    grid-template-columns: repeat(10, 1fr);
    gap:8px;
    justify-content:center;
    width:60%;
    margin:0 auto 20px;
}
.seat-btn {
    padding:10px 0;
    border-radius:8px;
    border:1px solid #d1d5db;
    background:white;
    cursor:pointer;
    font-size:13px;
}
.seat-btn.taken {
    background:#e5e7eb;
    color:#9ca3af;
    cursor:not-allowed;
}
.seat-btn.selected {
    background:#2563eb;
    color:white;
    border-color:#1d4ed8;
}
.submit-btn {
    margin-top:15px;
    padding:12px 20px;
    border:none;
    border-radius:999px;
    background:#2563eb;
    color:white;
    font-weight:600;
    cursor:pointer;
}
.empty-text {
    color:#64748b;
}
</style>

<div class="seat-wrapper">

    <?php if (!empty($errors) && !$booking): ?>
        <div class="seat-errors"><?= implode('<br>', array_map('esc', $errors)); ?></div>
    <?php endif; ?>

    <?php if ($booking): ?>
        <div class="seat-title">Pilih Kursi</div>
        <div class="seat-sub">
            <?= esc($booking['title']) ?> —
            <?= date('d M Y H:i', strtotime($booking['show_time'])) ?> —
            <?= esc($booking['studio_name']) ?>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="seat-errors"><?= implode('<br>', array_map('esc', $errors)); ?></div>
        <?php endif; ?>

        <div class="screen-bar">LAYAR</div>

        <form method="POST">
            <div class="seat-grid">
                <?php foreach ($seats as $seat): ?>
                    <?php
                        $isTaken = in_array($seat['id'], $takenSeats);
                        $classes = "seat-btn" . ($isTaken ? " taken" : "");
                    ?>
                    <button type="button"
                            class="<?= $classes ?>"
                            data-seat-id="<?= $seat['id'] ?>"
                            <?= $isTaken ? "disabled" : "" ?>>
                        <?= esc($seat['seat_number']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <input type="hidden" name="seat_id" id="seat_id">
            <button type="submit" class="submit-btn">Simpan Pilihan Kursi</button>
        </form>
    <?php else: ?>
        <p class="empty-text">Booking tidak dapat ditampilkan.</p>
    <?php endif; ?>

</div>

<script>
let selectedButton = null;
document.querySelectorAll('.seat-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        if (btn.classList.contains('taken')) return;

        if (selectedButton) {
            selectedButton.classList.remove('selected');
        }
        selectedButton = btn;
        btn.classList.add('selected');
        document.getElementById('seat_id').value = btn.dataset.seatId;
    });
});
</script>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
