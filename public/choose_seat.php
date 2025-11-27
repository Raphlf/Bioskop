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

    // kursi yang sudah diambil booking lain
    $stmt = $pdo->prepare("
        SELECT bs.seat_id
        FROM booking_seats bs
        JOIN bookings b2 ON bs.booking_id = b2.id
        WHERE b2.schedule_id = ? AND b2.id != ?
    ");
    $stmt->execute([$booking['schedule_id'], $booking_id]);
    $takenSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// ===================== 3. HANDLE POST =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {

    $seat_ids_str = isset($_POST['seat_ids']) ? trim($_POST['seat_ids']) : '';
    $seat_ids = $seat_ids_str ? explode(',', $seat_ids_str) : [];

    $MAX_SEATS = 5;

    if (empty($seat_ids)) {
        $errors[] = "Pilih setidaknya satu kursi.";
    } elseif (count($seat_ids) > $MAX_SEATS) {
        $errors[] = "Maksimal pilih {$MAX_SEATS} kursi.";
    } else {

        $validSeats = [];

        foreach ($seat_ids as $seat_id) {
            $seat_id = (int)$seat_id;

            // cek seat valid & milik studio ini
            $stmt = $pdo->prepare("SELECT 1 FROM seats WHERE id = ? AND studio_id = ? LIMIT 1");
            $stmt->execute([$seat_id, $booking['studio_id']]);
            $seatValid = $stmt->fetchColumn();

            if (!$seatValid) {
                $errors[] = "Ada kursi yang tidak valid.";
                break;
            }

            // cek apakah kursi sudah diambil
            $stmt = $pdo->prepare("
                SELECT 1
                FROM booking_seats bs
                JOIN bookings b2 ON bs.booking_id = b2.id
                WHERE b2.schedule_id = ? 
                  AND bs.seat_id = ? 
                  AND b2.id != ?
                LIMIT 1
            ");
            $stmt->execute([$booking['schedule_id'], $seat_id, $booking_id]);

            if ($stmt->fetchColumn()) {
                $errors[] = "Salah satu kursi sudah diambil orang lain.";
                break;
            }

            $validSeats[] = $seat_id;
        }

        if (empty($errors)) {

            $pdo->beginTransaction();

            try {

                // Hapus kursi lama dulu (kalau ada)
                $pdo->prepare("DELETE FROM booking_seats WHERE booking_id = ?")
                    ->execute([$booking_id]);

                // Simpan yang baru
                foreach ($validSeats as $seat_id) {
                    $pdo->prepare("
                        INSERT INTO booking_seats (booking_id, seat_id)
                        VALUES (?, ?)
                    ")->execute([$booking_id, $seat_id]);
                }

                // Update total harga
                $totalPrice = count($validSeats) * $booking['price'];

                $pdo->prepare("
                    UPDATE bookings 
                    SET total_price = ? 
                    WHERE id = ?
                ")->execute([$totalPrice, $booking_id]);

                $pdo->commit();

                header("Location: my_ticket.php");
                exit;

            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Terjadi kesalahan saat menyimpan kursi.";
            }
        }
    }
}

include __DIR__ . '/../src/templates/header.php';
?>

<style>
html {
    /* 1. Pastikan HTML mengambil seluruh tinggi viewport */
    height: 100%;
}
body {
    display: flex;
    flex-direction: column; /* Susun elemen ke bawah */
    min-height: 100vh;      /* Minimal setinggi jendela browser */
    margin: 0;
    padding: 0;
    background:#f1f5f9;
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
}

.seat-wrapper {
    max-width:900px;
    margin:110px auto 50px;
    text-align:center;
    flex: 1;
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
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
}

.screen-bar {
    width:60%;
    margin:0 auto 20px;
    padding:10px;
    background:#111827;
    color:white;
    border-radius:10px;
    font-weight:600;
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
    padding: 10px 0;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: white;
    cursor: pointer;
    font-size: 13px;
    transition: .2s ease;
    /* Tambahkan ini */
    min-width: 55px; /* Sesuaikan nilai ini sesuai keinginan Anda */
    height: 35px; /* Opsional: buat tinggi dan lebarnya sama untuk tombol persegi */
    display: flex; /* Untuk menengahkan teks jika ada padding horizontal */
    align-items: center;
    justify-content: center;
    transition: ease .2s;
}

.seat-btn:hover {
    transform: scale(1.05);
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

.summary-box {
    margin-top:10px;
    font-size:14px;
    color:#334155;
}

.submit-btn {
    margin-top:20px;
    padding:12px 25px;
    border:none;
    border-radius:999px;
    background:#2563eb;
    color:white;
    font-weight:600;
    cursor:pointer;
    font-size:15px;
}

.submit-btn:hover {
    background:#1e40af;
}

</style>

<div class="seat-wrapper">

<?php if (!empty($errors)): ?>
    <div class="seat-errors"><?= implode('<br>', array_map('esc', $errors)); ?></div>
<?php endif; ?>

<?php if ($booking): ?>

    <div class="seat-title">Pilih Kursi</div>
    <div class="seat-sub">
        <?= esc($booking['title']) ?> —
        <?= date('d M Y H:i', strtotime($booking['show_time'])) ?> —
        <?= esc($booking['studio_name']) ?>
    </div>

    <div class="screen-bar">LAYAR</div>

    <form method="POST" onsubmit="return validateSeats()">

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

        <div class="summary-box">
            <strong>Kursi dipilih:</strong>
            <span id="seatCount">0</span> |
            <strong>Total:</strong> Rp <span id="totalPrice">0</span>
        </div>

        <input type="hidden" name="seat_ids" id="seat_ids">

        <button class="submit-btn">Simpan Kursi</button>

    </form>

<?php else: ?>
    <p>Booking tidak ditemukan.</p>
<?php endif; ?>

</div>

<script>

const MAX_SEAT = 5;
const price = <?= (int)$booking['price']; ?>;

let selectedSeats = [];

const seatCount = document.getElementById('seatCount');
const totalPrice = document.getElementById('totalPrice');

function updateSummary() {
    seatCount.innerText = selectedSeats.length;
    totalPrice.innerText = selectedSeats.length * price;
    document.getElementById('seat_ids').value = selectedSeats.join(',');
}

document.querySelectorAll('.seat-btn').forEach(btn => {

    if (btn.classList.contains('taken')) return;

    btn.addEventListener('click', function () {

        const seatId = this.dataset.seatId;
        const index  = selectedSeats.indexOf(seatId);

        if (index > -1) {
            selectedSeats.splice(index, 1);
            this.classList.remove('selected');
        } else {

            if (selectedSeats.length >= MAX_SEAT) {
                alert('Maksimal pilih ' + MAX_SEAT + ' kursi');
                return;
            }

            selectedSeats.push(seatId);
            this.classList.add('selected');
        }

        updateSummary();

    });
});

function validateSeats() {
    if (selectedSeats.length === 0) {
        alert('Pilih setidaknya 1 kursi dulu.');
        return false;
    }
    return true;
}

</script>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
