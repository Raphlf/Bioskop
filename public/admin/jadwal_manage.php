<?php
require_once __DIR__ . '/../../src/db.php';

$stmt = $pdo->query("
    SELECT s.id, f.title, s.show_time, s.price, st.name AS studio_nama
    FROM schedules s
    JOIN films f ON s.film_id = f.id
    JOIN studios st ON s.studio_id = st.id
    ORDER BY s.show_time ASC
");
$jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kelola Jadwal</title>
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
    margin-bottom: 20px;
}

/* === TABLE STYLE FIXED === */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

thead th {
    background: #4e5cff;
    color: white;
    padding: 14px;
    font-size: 15px;
}

tbody tr {
    transition: 0.2s;
}

tbody tr:hover {
    background: #f1f3ff;
}

tbody td {
    padding: 12px 14px;
    border-bottom: 1px solid #e5e5e5;
    font-size: 14px;
    text-align: center;
}

/* Buttons */
.btn {
    padding: 8px 12px;
    background: #4e5cff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
}

.btn-edit {
    padding: 7px 12px;
    background: #f4a742;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    margin-right: 6px;
}

.btn-delete {
    padding: 7px 12px;
    background: #ff6b6b;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
}
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

        <h1>Kelola Jadwal</h1>

        <a class="btn" href="jadwal_form.php">+ Tambah Jadwal</a>
        <br><br>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Film</th>
            <th>Studio</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($jadwal as $j): 
        $dt = new DateTime($j['show_time']);
    ?>
        <tr>
            <td><?= $j['id'] ?></td>
            <td><?= htmlspecialchars($j['title']) ?></td>
            <td>Studio <?= htmlspecialchars($j['studio_nama']) ?></td>

            <td><?= $dt->format("Y-m-d") ?></td>
            <td><?= $dt->format("H:i") ?></td>

            <td>Rp <?= number_format($j['price'], 0, ',', '.') ?></td>

            <td>
                <a href="jadwal_form.php?id=<?= $j['id'] ?>" class="btn-edit">Edit</a>
                <a onclick="return confirm('Yakin hapus?')" 
                   href="jadwal_delete.php?id=<?= $j['id'] ?>"
                   class="btn-delete">Hapus</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

    </main>

</div>

</body>
</html>
