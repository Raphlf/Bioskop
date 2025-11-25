<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

require_login();

// Pastikan reservation_id ada
if (!isset($_GET['reservation_id'])) {
    die("Reservation ID tidak ditemukan.");
}

$reservation_id = intval($_GET['reservation_id']);

// Ambil data reservation + harga dari schedules
$stmt = $pdo->prepare("SELECT r.*, s.price 
    FROM reservations r 
    JOIN schedules s ON r.schedule_id = s.id
    WHERE r.id = ? LIMIT 1");
$stmt->execute([$reservation_id]);
$res = $stmt->fetch();

if (!$res) {
    die("Reservasi tidak valid.");
}

// Generate seat list (A1 sampai A30)
$seats = [];
for ($i = 1; $i <= 30; $i++) {
    $seats[] = "A" . $i;
}

// Ambil kursi yang sudah dipesan orang lain
$stmt = $pdo->prepare("SELECT seat_code FROM reservation_seats 
                        WHERE reservation_id != ?");
$stmt->execute([$reservation_id]);
$takenSeats = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Jika user submit kursi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['seat'])) die("Tidak ada kursi yang dipilih.");

    $seat = $_POST['seat'];

    // Cek apakah kursi sudah diambil
    if (in_array($seat, $takenSeats)) {
        die("Kursi sudah diambil orang lain.");
    }

    // Simpan kursi pilihan
    $pdo->prepare("INSERT INTO reservation_seats (reservation_id, seat_code) VALUES (?, ?)")
        ->execute([$reservation_id, $seat]);

    // Update reservation (pakai harga dari schedules)
    $pdo->prepare("UPDATE reservations 
                   SET seats = 1, total_price = ? 
                   WHERE id = ?")
        ->execute([$res['price'], $reservation_id]);

    header("Location: reservasi.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pilih Kursi</title>
    <style>
        body {
            background: #3b0d0d;
            font-family: Arial, sans-serif;
            color: #f1d6d0;
            text-align: center;
            padding: 20px;
        }

        h2 {
            color: #d4af97;
            margin-bottom: 20px;
        }

        .screen {
            width: 60%;
            margin: 20px auto;
            padding: 12px;
            background: #800000;
            color: white;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 10px;
            justify-content: center;
            margin: 20px auto;
            width: 60%;
        }

        .seat {
            padding: 12px;
            background: #2a1a1a;
            color: #f1d6d0;
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
            border: 1px solid #5e3b1a;
        }

        .seat:hover {
            background: #5e3b1a;
        }

        .taken {
            background: #4d0000;
            color: #888;
            cursor: not-allowed;
        }

        .selected {
            background: #a52a2a;
            color: white;
            border: 1px solid #fff;
        }

        button {
            margin-top: 20px;
            padding: 14px 20px;
            font-size: 16px;
            background: #800000;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            width: 220px;
            font-weight: bold;
        }

        button:hover {
            background: #a52a2a;
        }

        input[type="hidden"] {
            display: none;
        }
    </style>

    <script>
        let selectedSeat = null;

        function chooseSeat(seatCode) {
            if (document.getElementById(seatCode).classList.contains("taken")) {
                return;
            }

            if (selectedSeat) {
                document.getElementById(selectedSeat).classList.remove("selected");
            }

            selectedSeat = seatCode;
            document.getElementById(seatCode).classList.add("selected");
            document.getElementById("seat_input").value = seatCode;
        }
    </script>
</head>

<body>

<h2>Pilih Kursi</h2>

<div class="screen">LAYAR</div>

<form method="POST">

    <div class="seat-grid">

        <?php foreach ($seats as $s): ?>
            <?php
                $isTaken = in_array($s, $takenSeats);
                $class = $isTaken ? "seat taken" : "seat";
            ?>

            <div 
                id="<?= $s ?>" 
                class="<?= $class ?>"
                onclick="chooseSeat('<?= $s ?>')"
            >
                <?= $s ?>
            </div>

        <?php endforeach; ?>

    </div>

    <input type="hidden" name="seat" id="seat_input">

    <button type="submit">Simpan Pilihan Kursi</button>

</form>

</body>
</html>
