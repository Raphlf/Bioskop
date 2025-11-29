<?php
// Pastikan file-file esensial di-require
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';

// 1. KONTROL AKSES: Cek apakah pengguna sudah login
if (!is_logged_in()) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // Pengguna tidak ditemukan, alihkan ke logout
    header("Location: " . BASE_URL . "/logout.php");
    exit;
}

// 2. STATISTIK: Mengambil total bookings (menggunakan tabel 'bookings')
$stat = ['total_reservasi' => 0];
try {
    // KOREKSI: Menggunakan tabel 'bookings'
    $resv = $pdo->prepare("
        SELECT COUNT(*) AS total_reservasi
        FROM bookings 
        WHERE user_id = ?");
    $resv->execute([$user_id]);
    $stat = $resv->fetch();
} catch (Exception $e) {
    // Log error, jangan tampilkan di depan
    // error_log("DB Error fetching total reservations: " . $e->getMessage());
}


// 3. STATISTIK: Menghitung Total Kursi Dipesan (menggunakan tabel 'booking_seats' dan 'bookings')
$total_kursi = 0;
try {
    // KOREKSI: Menggunakan tabel 'booking_seats' JOIN 'bookings'
    $total_kursi_stmt = $pdo->prepare("
        SELECT COUNT(*) AS total_kursi 
        FROM booking_seats rs 
        JOIN bookings r ON rs.booking_id = r.id 
        WHERE r.user_id = ?
    ");
    $total_kursi_stmt->execute([$user_id]);
    $total_kursi_row = $total_kursi_stmt->fetch();
    $total_kursi = $total_kursi_row ? $total_kursi_row['total_kursi'] : 0;
} catch (Exception $e) {
    // Log error
    // error_log("DB Error fetching total seats: " . $e->getMessage());
}
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
/* ==================== Profile Light Mode ==================== */
body {
    background: #f4f6f8;
    color: #1f2937;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding-top: 110px;  /* FIX ketutupan navbar */
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
    <p><strong>Total Kursi Dipesan:</strong> <?= $total_kursi ?></p>

    <a href="edit_profil.php" class="btn">Edit Profil</a>
</div>

<br><br>
<?php include __DIR__ . '/../src/templates/footer.php'; ?>