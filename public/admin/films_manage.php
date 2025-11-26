<?php
require_once __DIR__ . '/../../src/db.php';

// Ambil semua film
$films = $pdo->query("SELECT * FROM films ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Kelola Film</title>

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
        margin-bottom: 8px;
    }

    .subtitle {
        font-size: 16px;
        color: #555;
        margin-bottom: 25px;
    }

    /* Button */
    .btn {
        display: inline-block;
        padding: 8px 12px;
        font-size: 14px;
        background: #4e5cff;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn:hover {
        opacity: 0.9;
    }

    .btn-warning {
        background: #f4a742;
    }

    .btn-danger {
        background: #ff6b6b;
    }

    .btn-green {
        background: #27ae60;
    }

    /* Table */
    table.table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
    }

    table.table th {
        background: #4e5cff;
        color: #fff;
        padding: 12px;
        text-align: left;
    }

    table.table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
    }

    table.table tr:hover td {
        background: #f0f0f0;
    }

    .poster {
        border-radius: 6px;
    }
</style>

</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">🎬 Admin</h2>

        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="films_manage.php" class="active">Kelola Film</a></li>
            <li><a href="jadwal_manage.php">Kelola Jadwal</a></li>
            <li><a href="users_manage.php">Kelola User</a></li>
            <li><a href="exports.php">Export Data</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </aside>

    <!-- CONTENT -->
    <main class="content">

        <h1>Kelola Film</h1>
        <p class="subtitle">Daftar seluruh film</p>

        <a class="btn" href="film_form.php">+ Tambah Film</a>
        <br><br>

        <table class="table">
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Judul</th>
                <th>Aksi</th>
            </tr>

            <?php foreach ($films as $film): ?>
            <tr>
                <td><?= $film['id'] ?></td>

                <td>
                    <?php if (!empty($film['poster'])): ?>
                        <img src="../assets/uploads/<?= $film['poster'] ?>" width="70" class="poster">
                    <?php else: ?>
                        (no image)
                    <?php endif; ?>
                </td>

<td>
    <?php
        // auto fix
        $judul = $film['title']
                 ?? $film['nama_film']
                 ?? $film['judul']
                 ?? $film['name']
                 ?? '(judul tidak tersedia)';

        echo htmlspecialchars($judul);
    ?>
</td>

                <td>
                    <a class="btn btn-warning" href="film_form.php?id=<?= $film['id'] ?>">Edit</a>
                    <a class="btn btn-danger"
                       href="films_delete.php?id=<?= $film['id'] ?>"
                       onclick="return confirm('Hapus film ini?')">Hapus</a>
                    <a class="btn btn-green" 
                       href="seats_manage.php?film_id=<?= $film['id'] ?>">Lihat Kursi</a>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>

    </main>

</div>

</body>
</html>
