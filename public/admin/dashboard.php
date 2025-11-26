<?php
require_once __DIR__ . '/../../src/db.php';

// Ambil data dari database
$totalFilms = $pdo->query("SELECT COUNT(*) FROM films")->fetchColumn();
$totalRooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$totalSchedules = $pdo->query("SELECT COUNT(*) FROM schedules")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// Data chart “Penonton per Film”
$filmChart = $pdo->query("
    SELECT f.title, COUNT(b.id) AS total
    FROM films f
    LEFT JOIN schedules s ON s.film_id = f.id
    LEFT JOIN bookings b ON b.schedule_id = s.id
    GROUP BY f.id
")->fetchAll(PDO::FETCH_ASSOC);

// Data grafik 7 hari terakhir
$daily = $pdo->query("
    SELECT DATE(created_at) AS tanggal, COUNT(id) AS jumlah
    FROM bookings
    WHERE DATE(created_at) >= DATE(NOW() - INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
")->fetchAll(PDO::FETCH_ASSOC);

$dailyLabels = [];
$dailyValues = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date("Y-m-d", strtotime("-$i day"));
    $dailyLabels[] = $day;

    $found = 0;
    foreach ($daily as $d) {
        if ($d['tanggal'] == $day) $found = $d['jumlah'];
    }
    $dailyValues[] = $found;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* Layout */
.container {
    display: flex;
    height: 100vh;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background: #1d1f27;
    padding: 20px;
    color: #fff;
}

.logo {
    text-align: center;
    margin-bottom: 30px;
    font-size: 24px;
}

.menu {
    list-style: none;
}

.menu li {
    margin-bottom: 15px;
}

.menu a {
    display: block;
    padding: 10px;
    color: #cfcfcf;
    text-decoration: none;
    border-radius: 6px;
    transition: 0.2s;
}

.menu a:hover,
.menu a.active {
    background: #4e5cff;
    color: #fff;
}

.logout {
    color: #ff6b6b !important;
}

/* Content */
.content {
    flex: 1;
    padding: 30px;
    background: #f3f4f7;
    overflow-y: auto;
}

.content h1 {
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    font-size: 16px;
    color: #555;
    margin-bottom: 30px;
}

/* Cards */
.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
}

.card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.card h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #444;
}

.card p {
    font-size: 40px;
    margin-top: 5px;
    font-weight: bold;
    color: #4e5cff;
}

/* Chart box */
.chart-box {
    background: #fff;
    padding: 25px;
    margin-top: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>

</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">🎬 Admin</h2>

        <ul class="menu">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="films_manage.php">Kelola Film</a></li>
            <li><a href="jadwal_manage.php">Kelola Jadwal</a></li>
            <li><a href="users_manage.php">Kelola User</a></li>
            <li><a href="exports.php">Export Data</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </aside>

    <!-- CONTENT -->
    <main class="content">

        <h1>Dashboard</h1>
        <p class="subtitle">Ringkasan data sistem bioskop</p>

        <!-- Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Film</h3>
                <p><?= $totalFilms ?></p>
            </div>

            <div class="card">
                <h3>Total Ruangan</h3>
                <p><?= $totalRooms ?></p>
            </div>

            <div class="card">
                <h3>Total Jadwal</h3>
                <p><?= $totalSchedules ?></p>
            </div>

            <div class="card">
                <h3>Total Booking</h3>
                <p><?= $totalBookings ?></p>
            </div>
        </div>

        <!-- Chart: Booking 7 Hari -->
        <div class="chart-box">
            <h3>Pemesanan 7 Hari Terakhir</h3>
            <canvas id="dailyChart"></canvas>
        </div>

        <!-- Chart: Penonton per Film -->
        <div class="chart-box">
            <h3>Penonton per Film</h3>
            <canvas id="filmChart"></canvas>
        </div>

        <!-- Chart: Genre Pie -->
        <div class="chart-box">
            <h3>Pembagian Genre Film</h3>
            <canvas id="genreChart"></canvas>
        </div>

    </main>
</div>

<script>
// ---------- DATA PHP KE JAVASCRIPT ----------
const filmLabels = <?= json_encode(array_column($filmChart, "title")) ?>;
const filmValues = <?= json_encode(array_column($filmChart, "total")) ?>;

const dailyLabels = <?= json_encode($dailyLabels) ?>;
const dailyValues = <?= json_encode($dailyValues) ?>;

// 1. Line Chart: Booking 7 Hari
new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Booking',
            data: dailyValues,
            borderColor: '#4e5cff',
            borderWidth: 3,
            fill: false,
            tension: 0.3
        }]
    }
});

// 2. Bar Chart: Penonton per Film
new Chart(document.getElementById('filmChart'), {
    type: 'bar',
    data: {
        labels: filmLabels,
        datasets: [{
            label: 'Penonton',
            data: filmValues,
            backgroundColor: '#4e5cff'
        }]
    }
});

// 3. Pie Chart: Genre Film
new Chart(document.getElementById('genreChart'), {
    type: 'pie',
    data: {
        labels: ['Action', 'Drama', 'Horror', 'Comedy'],
        datasets: [{
            data: [30, 25, 20, 25],
            backgroundColor: ['#4e5cff','#1abc9c','#e74c3c','#f1c40f']
        }]
    }
});
</script>

</body>
</html>
