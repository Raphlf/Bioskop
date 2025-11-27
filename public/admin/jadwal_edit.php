<?php
require_once __DIR__ . '/../../src/db.php';

// Ambil ID jadwal
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan.");
}

// Ambil data jadwal lama
$stmt = $pdo->prepare("
    SELECT s.*, f.title
    FROM schedules s
    JOIN films f ON s.film_id = f.id
    WHERE s.id = ?
");
$stmt->execute([$id]);
$jadwal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jadwal) {
    die("Data jadwal tidak ditemukan.");
}

// Ambil list film untuk select option
$filmStmt = $pdo->query("SELECT id, title FROM films ORDER BY title");
$films = $filmStmt->fetchAll(PDO::FETCH_ASSOC);

// Pecah datetime menjadi date + time
$datetime = explode(" ", $jadwal['show_time']);
$show_date = $datetime[0];
$show_time = substr($datetime[1], 0, 5);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Jadwal</title>
    <style>
        <?php include "style_base.css"; ?>
    </style>
</head>
<body>

<div class="container">

    <aside class="sidebar">
        <h2 class="logo">🎬 Admin</h2>

        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="films_manage.php">Kelola Film</a></li>
            <li><a class="active" href="jadwal_manage.php">Kelola Jadwal</a></li>
            <li><a href="users_manage.php">Kelola User</a></li>
            <li><a href="exports.php">Export Data</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </aside>

    <main class="content">
        <h1>Edit Jadwal</h1>

        <div class="form-box">
            <form method="post" action="jadwal_update.php">

                <input type="hidden" name="id" value="<?= $jadwal['id'] ?>">

                <label>Film</label>
                <select name="film_id" required>
                    <?php foreach ($films as $film): ?>
                        <option value="<?= $film['id'] ?>"
                            <?= ($film['id'] == $jadwal['film_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($film['title']) ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <label>Tanggal</label>
                <input type="date" name="date" value="<?= $show_date ?>" required>

                <label>Jam Tayang</label>
                <input type="time" name="time" value="<?= $show_time ?>" required>

                <button type="submit">Update</button>
            </form>
        </div>

    </main>

</div>

</body>
</html>
