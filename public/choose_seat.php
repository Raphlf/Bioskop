<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

require_login();
$user = $_SESSION['user'];

if (!isset($_GET['schedule_id'])) {
    echo "Booking tidak valid.";
    exit;
}

$schedule_id = (int)$_GET['schedule_id'];

// AMBIL DATA JADWAL
$stmt = $pdo->prepare("
    SELECT s.*, f.title, f.poster, st.name AS studio_name
    FROM schedules s
    JOIN films f ON s.film_id = f.id
    JOIN studios st ON s.studio_id = st.id
    WHERE s.id = ?
");
$stmt->execute([$schedule_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    echo "Jadwal tidak ditemukan.";
    exit;
}

// AMBIL DAFTAR KURSI
$seatStmt = $pdo->prepare("
    SELECT id, seat_number 
    FROM seats 
    WHERE studio_id = ?
    ORDER BY seat_number ASC
");
$seatStmt->execute([$schedule['studio_id']]);
$seats = $seatStmt->fetchAll();

// KURSI TERSISIH (SUDAH DIBOOKING)
$bookedStmt = $pdo->prepare("
    SELECT seat_id 
    FROM booking_seats bs
    JOIN bookings b ON b.id = bs.booking_id
    WHERE b.schedule_id = ? 
      AND b.status = 'confirmed'
");
$bookedStmt->execute([$schedule_id]);
$bookedSeats = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);

include __DIR__ . '/../src/templates/header.php';
?>

<style>
/* desain kamu utuh */
body { background: #f3f4f6; margin: 0; padding: 0; }
.seat-wrapper {
    max-width: 750px;
    margin: 100px auto;
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}
.seat-title { font-size: 28px; font-weight: 800; text-align: center; margin-bottom: 5px; }
.seat-subtitle { text-align: center; color: #6b7280; margin-bottom: 25px; }
.screen-box {
    width: 60%; margin: 0 auto 30px; padding: 12px;
    background: #111827; color: white; font-weight: 700; border-radius: 8px; text-align: center;
}
.seat-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 10px;
    padding: 10px;
    justify-items: center;
}
.seat {
    width: 48px; height: 38px; background: #e5e7eb; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-weight: 600;
}
.seat.selected { background: #2563eb; color: white; }
.seat.booked { background: #9ca3af; cursor: not-allowed; color: white; }
.total-info { text-align: center; margin-top: 20px; font-weight: 600; }
.btn-primary {
    display: block; width: 200px; margin: 25px auto 10px; text-align: center;
    padding: 12px; background: #2563eb; border-radius: 12px;
    color: white; font-weight: 700; border: none; cursor: pointer;
}
.btn-primary:hover { background: #1d4ed8; }
.btn-cancel {
    display: block; width: 160px; margin: 10px auto;
    text-align: center; padding: 10px; background: #dc2626;
    border-radius: 12px; color: white; font-weight: 600; border: none; cursor: pointer;
}
.btn-cancel:hover { background: #b91c1c; }

/* POPUP */
.popup-bg {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.55);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99;
}
.popup-box {
    background: white;
    padding: 25px 30px;
    border-radius: 14px;
    text-align: center;
    width: 330px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}
.popup-box h3 { margin-bottom: 10px; font-size: 20px; font-weight: 700; }
.popup-box p { color: #555; margin-bottom: 20px; }
.popup-btn {
    padding: 10px 20px;
    border-radius: 10px;
    background: #2563eb;
    color: white;
    border: none;
    cursor: pointer;
    font-weight: 600;
}
.popup-btn:hover { background: #1d4ed8; }
</style>

<div class="seat-wrapper">

    <div class="seat-title">Pilih Kursi</div>
    <div class="seat-subtitle">
        <?= esc($schedule['title']) ?> — 
        <?= date("d M Y H:i", strtotime($schedule['show_time'])) ?> —
        <?= esc($schedule['studio_name']) ?>
    </div>

    <div class="screen-box">LAYAR</div>

    <div class="seat-grid">
        <?php foreach ($seats as $s):
            $booked = in_array($s['id'], $bookedSeats);
        ?>
            <div 
                class="seat <?= $booked ? 'booked' : '' ?>" 
                data-id="<?= $s['id'] ?>"
                data-number="<?= $s['seat_number'] ?>"
            >
                <?= $s['seat_number'] ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="total-info">
        Kursi dipilih: <span id="count">0</span> | Total: Rp <span id="total">0</span>
    </div>

    <form id="seatForm" method="POST" action="payment.php">
        <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">
        <div id="seatsContainer"></div>

        <button type="submit" class="btn-primary" id="payBtn">Lanjut Pembayaran</button>
    </form>

    <a href="index.php">
        <button class="btn-cancel">Cancel</button>
    </a>

</div>

<!-- POPUP -->
<div class="popup-bg" id="popup">
    <div class="popup-box">
        <h3>Pilih Kursi Dulu!</h3>
        <p>Kamu harus memilih minimal 1 kursi sebelum lanjut pembayaran.</p>
        <button class="popup-btn" id="closePopup">OK</button>
    </div>
</div>

<script>
let selectedSeats = [];
const seatEls = document.querySelectorAll(".seat:not(.booked)");
const seatsContainer = document.getElementById("seatsContainer");
const price = <?= $schedule['price'] ?>;

seatEls.forEach(seat => {
    seat.addEventListener("click", () => {
        const id = seat.dataset.id;
        const number = seat.dataset.number;

        seat.classList.toggle("selected");

        if (selectedSeats.find(s => s.id === id)) {
            selectedSeats = selectedSeats.filter(s => s.id !== id);
        } else {
            selectedSeats.push({ id, number });
        }

        renderSeats();
    });
});

function renderSeats() {
    seatsContainer.innerHTML = "";
    selectedSeats.forEach(s => {
        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "seats[]";
        input.value = s.id;
        seatsContainer.appendChild(input);
    });

    document.getElementById("count").innerText = selectedSeats.length;
    document.getElementById("total").innerText = (selectedSeats.length * price).toLocaleString();
}

// PROTEKSI: tidak boleh submit jika tidak pilih kursi
document.getElementById("seatForm").addEventListener("submit", function(e) {
    if (selectedSeats.length === 0) {
        e.preventDefault();
        document.getElementById("popup").style.display = "flex";
    }
});

// CLOSE POPUP
document.getElementById("closePopup").addEventListener("click", () => {
    document.getElementById("popup").style.display = "none";
});
</script>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
