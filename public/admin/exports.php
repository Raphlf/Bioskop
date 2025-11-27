<?php
require_once __DIR__ . "/../../src/db.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Data</title>
    <style>
        /* Reset & font */
        * { margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif; }

        /* Layout */
        .container { display: flex; height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #1d1f27;
            padding: 20px;
            color: #fff;
        }
        .logo {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .menu { list-style: none; }
        .menu li { margin-bottom: 15px; }
        .menu a {
            display: block;
            padding: 10px;
            color: #cfcfcf;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.2s;
        }
        .menu a:hover, .menu a.active { background: #4e5cff; color: #fff; }
        .logout { color: #ff6b6b !important; }

        /* Content */
        .content {
            flex: 1;
            padding: 30px;
            background: #f3f4f7;
            overflow-y: auto;
        }
        .content h1 { font-size: 32px; margin-bottom: 10px; }
        .subtitle { font-size: 16px; color: #555; margin-bottom: 30px; }

        /* Tombol */
        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin-bottom: 15px;
            background: #4e5cff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <h2 class="logo">🎬 Admin</h2>
        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="films_manage.php">Kelola Film</a></li>
            <li><a href="jadwal_manage.php">Kelola Jadwal</a></li>
            <li><a href="users_manage.php">Kelola User</a></li>
            <li><a class="active" href="exports.php">Export Data</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="content">
        <h1>Export Data</h1>
        <p class="subtitle">Export database ke format CSV dengan mudah.</p>

        <a href="export_film.php" class="btn">Export Film</a>
        <br>
        <a href="export_jadwal.php" class="btn">Export Jadwal</a>
        <br>
        <a href="export_user.php" class="btn">Export User</a>
    </main>

</div>

</body>
</html>
