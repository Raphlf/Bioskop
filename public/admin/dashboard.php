<?php
// dashboard.php (full, safe version)

// Tampilkan error fatal di log tapi jangan tampilkan notice/warning di layar user
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Pastikan session hanya dimulai sekali
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Paths relatif (sesuaikan jika struktur folder berbeda)
$path_db     = __DIR__ . '/../../src/db.php';
$path_auth   = __DIR__ . '/../../src/auth.php';
$path_header = __DIR__ . '/../../src/templates/header.php';
$path_footer = __DIR__ . '/../../src/templates/footer.php';

// Cek keberadaan file penting sebelum include, supaya tidak menghasilkan HTTP 500 tanpa penjelasan
if (!file_exists($path_db) || !file_exists($path_auth)) {
    // Pesan singkat agar developer tahu file mana yg hilang
    echo "<h2>Konfigurasi hilang</h2>";
    echo "<p>Pastikan file <code>db.php</code> dan <code>auth.php</code> ada di <code>/src</code>.</p>";
    // Jelaaskan path yang dicek
    echo "<pre>Checked paths:\n$path_db\n$path_auth</pre>";
    exit;
}

// Include aman
require_once $path_db;
require_once $path_auth;

// Pastikan function require_admin ada di auth (jika tidak, tampilkan pesan)
if (!function_exists('require_admin')) {
    echo "<h2>Autentikasi bermasalah</h2>";
    echo "<p>File auth.php tidak menyediakan fungsi <code>require_admin()</code>. Periksa file auth.</p>";
    exit;
}

// Pastikan koneksi $pdo ada (db.php seharusnya membuat $pdo)
if (!isset($pdo) || !$pdo) {
    echo "<h2>Database connection error</h2>";
    echo "<p>Variabel <code>$pdo</code> tidak tersedia. Periksa file db.php.</p>";
    exit;
}

// Pastikan user admin (require_admin akan mengarahkan / menghentikan bila perlu)
require_admin();

// --- Ambil data dari database dengan fallback yang aman ---
$films_count = 0;
$schedules_count = 0;
$res_count = 0;
$tickets_sold = 0;

try {
    $films_count = (int) $pdo->query("SELECT COUNT(*) AS c FROM films")->fetchColumn();
} catch (Exception $e) {
    // log error otomatis, tetap lanjutkan
    $films_count = 0;
}

try {
    $schedules_count = (int) $pdo->query("SELECT COUNT(*) AS c FROM schedules")->fetchColumn();
} catch (Exception $e) {
    $schedules_count = 0;
}

try {
    $res_count = (int) $pdo->query("SELECT COUNT(*) AS c FROM reservations")->fetchColumn();
} catch (Exception $e) {
    $res_count = 0;
}

// Ambil tiket terjual: coba SUM(qty) dahulu, kalau gagal (kolom tidak ada) pakai COUNT(*)
try {
    $stmt = $pdo->query("SELECT COALESCE(SUM(qty), NULL) AS total_qty FROM reservations");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['total_qty']) && $row['total_qty'] !== null) {
        $tickets_sold = (int) $row['total_qty'];
    } else {
        // fallback: tiket = jumlah reservasi (satu reservasi = 1 tiket) jika tidak ada kolom qty
        $tickets_sold = $res_count;
    }
} catch (Exception $e) {
    // fallback aman
    $tickets_sold = $res_count;
}

// Hitung total untuk persentase; jangan biarkan 0 dibagi 0
$total_for_percent = max($tickets_sold + $films_count + $res_count, 1);

$percent_tickets = (int) round(($tickets_sold / $total_for_percent) * 100);
$percent_films   = (int) round(($films_count / $total_for_percent) * 100);
$percent_res     = (int) round(($res_count / $total_for_percent) * 100);

// Siapkan include header/footer (jika tidak ada, tampilkan konten tanpa header/footer)
$use_header = file_exists($path_header);
$use_footer = file_exists($path_footer);

// Jika header ada, include
if ($use_header) {
    include $path_header;
}
?>
<!-- STYLE & HTML -->
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f2e9f4;
    margin: 0; padding: 0;
}
.dashboard-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    width: 240px;
    background: #ffffff;
    padding: 25px;
    border-radius: 0 25px 25px 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    text-align: center;
}
.sidebar h2 { font-size: 22px; font-weight:600; color:#6e4c78; margin-bottom:18px; }
.chart-box { width:170px; margin:20px auto; }
.chart-label { font-weight:600; color:#6d5d70; margin-top:10px; }

/* Content */
.content { flex: 1; padding: 40px; }
.title { font-size: 30px; font-weight:600; color:#4a3b4f; margin-bottom:20px; }

/* Stats */
.stats-grid { display:flex; gap:20px; margin-bottom:25px; }
.stat-card { background:#fff; padding:25px; border-radius:20px; width:220px; box-shadow:0 3px 8px rgba(0,0,0,0.06); text-align:center; }
.stat-card p { color:#6f5e72; font-weight:500; }
.stat-card h2 { font-size:36px; color:#5d3a74; }

/* Buttons */
.btn { background:#c7ade8; padding:12px 20px; border-radius:14px; text-decoration:none; color:#fff; font-weight:600; margin:10px 10px 0 0; display:inline-block; transition:0.2s; }
.btn:hover { background:#a685d1; transform:translateY(-2px); }
</style>

<div class="dashboard-container">
    <aside class="sidebar">
        <h2>Statistik</h2>

        <div class="chart-box">
            <canvas id="chartTickets"></canvas>
            <p class="chart-label">Tiket Terjual</p>
        </div>

        <div class="chart-box">
            <canvas id="chartFilms"></canvas>
            <p class="chart-label">Film Ditayangkan</p>
        </div>

        <div class="chart-box">
            <canvas id="chartRes"></canvas>
            <p class="chart-label">Reservasi Dibuat</p>
        </div>
    </aside>

    <main class="content">
        <h1 class="title">Dashboard Admin BIOSKOP</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <p>Total Film</p>
                <h2><?= htmlspecialchars($films_count, ENT_QUOTES) ?></h2>
            </div>

            <div class="stat-card">
                <p>Total Jadwal</p>
                <h2><?= htmlspecialchars($schedules_count, ENT_QUOTES) ?></h2>
            </div>

            <div class="stat-card">
                <p>Total Reservasi</p>
                <h2><?= htmlspecialchars($res_count, ENT_QUOTES) ?></h2>
            </div>
        </div>

        <div style="margin-top:20px;">
            <a href="films_manage.php" class="btn">Kelola Film</a>
            <a href="jadwal_manage.php" class="btn">Kelola Jadwal</a>
            <a href="users_manage.php" class="btn">Kelola User</a>
            <a href="exports.php" class="btn">Exports Data</a>
            <a href="../profil.php" class="btn">Profil Admin</a>
        </div>
    </main>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pastikan PHP-variabel tersedia ke JS (safe)
const percentTickets = <?= json_encode($percent_tickets) ?>;
const percentFilms   = <?= json_encode($percent_films) ?>;
const percentRes     = <?= json_encode($percent_res) ?>;

function drawDonut(id, percent) {
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Terpenuhi', 'Sisa'],
            datasets: [{
                data: [percent, 100 - percent],
                backgroundColor: ['#c79ae8', '#eee1f5'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            responsive: true
        }
    });
}

drawDonut('chartTickets', percentTickets);
drawDonut('chartFilms', percentFilms);
drawDonut('chartRes', percentRes);
</script>

<?php
// Include footer jika ada
if ($use_footer) {
    include $path_footer;
}
?>
