<?php
require_once __DIR__ . "/../../src/db.php";

// Ambil semua film
$films = $pdo->query("SELECT id, title FROM films ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua studio
$studios = $pdo->query("SELECT id, name FROM studios ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Mode edit
$id = $_GET['id'] ?? null;
$edit = null;
$edit_date = "";
$edit_time = "";

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ?");
    $stmt->execute([$id]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($edit) {
        $dt = new DateTime($edit["show_time"]);
        $edit_date = $dt->format("Y-m-d");
        $edit_time = $dt->format("H:i");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form Jadwal</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial; }

        .container { display: flex; height: 100vh; }

        .sidebar {
            width: 250px;
            background: #1d1f27;
            padding: 20px;
            color: #fff;
        }

        .logo { text-align: center; margin-bottom: 30px; font-size: 24px; font-weight: bold; }

        .menu { list-style: none; }
        .menu li { margin-bottom: 15px; }
        .menu a {
            display: block; padding: 10px;
            text-decoration: none; color: #cfcfcf;
            border-radius: 6px; transition: 0.2s;
        }
        .menu a:hover, .menu a.active { background: #4e5cff; color: #fff; }
        .logout { color: #ff6b6b !important; }

        .content { flex: 1; padding: 30px; background: #f3f4f7; overflow-y: auto; }
        .content h1 { font-size: 32px; margin-bottom: 15px; }

        .form-box {
            background: #fff; padding: 20px; border-radius: 12px;
            width: 450px; margin-top: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .form-box label { font-size: 14px; margin-bottom: 6px; display: block; }
        .form-box input, .form-box select {
            width: 100%; padding: 10px; border-radius: 8px;
            border: 1px solid #aaa; margin-bottom: 15px;
        }
        .form-box button {
            width: 100%; padding: 10px;
            background: #4e5cff; border: none;
            color: #fff; font-size: 16px; border-radius: 8px;
        }
        .form-box button:hover { background: #3c49d9; }
    </style>
</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">🎬 Admin</h2>

        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="films_manage.php">Kelola Film</a></li>
            <li><a class="active" href="jadwal_manage.php">Kelola Jadwal</a></li>
            <li><a href="users_manage.php">Kelola User</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </aside>

    <main class="content">

        <h1>Form Jadwal</h1>

        <div class="form-box">

            <form method="post" action="jadwal_save.php">

                <?php if ($edit): ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                <?php endif; ?>

                <label>Film</label>
                <select name="film_id" required>
                    <option value="">-- Pilih Film --</option>
                    <?php foreach ($films as $film): ?>
                        <option value="<?= $film['id'] ?>"
                            <?= ($edit && $edit['film_id'] == $film['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($film['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Studio</label>
                <select name="studio_id" required>
                    <option value="">-- Pilih Studio --</option>
                    <?php foreach ($studios as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            <?= ($edit && $edit['studio_id'] == $s['id']) ? 'selected' : '' ?>>
                            Studio <?= $s['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Tanggal Tayang</label>
                <input type="date" name="date" value="<?= $edit_date ?>" required>

                <label>Jam Tayang</label>
                <input type="time" name="time" value="<?= $edit_time ?>" required>

                <label>Harga</label>
                <input type="number" name="price" value="<?= $edit ? $edit['price'] : '' ?>" required>

                <button type="submit">Simpan</button>

            </form>

        </div>

    </main>

</div>

</body>
</html>
