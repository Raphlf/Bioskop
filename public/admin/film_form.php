<?php
require_once __DIR__ . '/../../src/db.php';

// Jika edit
$edit = false;
$film = [
    "title" => "",
    "genre" => "",
    "duration" => "",
    "description" => "",
    "poster" => ""
];

if (isset($_GET["id"])) {
    $edit = true;
    $stmt = $pdo->prepare("SELECT * FROM films WHERE id=?");
    $stmt->execute([$_GET["id"]]);
    $film = $stmt->fetch();
}

// Jika submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $genre = $_POST["genre"];
    $duration = $_POST["duration"];
    $description = $_POST["description"];

    // Upload poster
    $posterName = $film["poster"];

    if (!empty($_FILES["poster"]["name"])) {
        $posterName = time() . "_" . $_FILES["poster"]["name"];
        move_uploaded_file($_FILES["poster"]["tmp_name"], __DIR__ . "/assets/uploads/" . $posterName);
    }

    if ($edit) {
        $stmt = $pdo->prepare("UPDATE films SET title=?, genre=?, duration=?, description=?, poster=? WHERE id=?");
        $stmt->execute([$title, $genre, $duration, $description, $posterName, $_GET["id"]]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO films (title, genre, duration, description, poster)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $genre, $duration, $description, $posterName]);
    }

    header("Location: films_manage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $edit ? "Edit Film" : "Tambah Film" ?></title>

<style>
/* COPY DARI DASHBOARD */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

.container { display: flex; height: 100vh; }

.sidebar {
    width: 250px;
    background: #1d1f27;
    padding: 20px;
    color: #fff;
}

.logo { text-align: center; margin-bottom: 30px; font-size: 24px; }

.menu { list-style: none; }

.menu li { margin-bottom: 15px; }

.menu a {
    display: block;
    padding: 10px;
    color: #cfcfcf;
    text-decoration: none;
    border-radius: 6px;
    transition: .2s;
}

.menu a:hover, .menu a.active { background: #4e5cff; color: #fff; }

.logout { color: #ff6b6b !important; }

.content {
    flex: 1;
    padding: 30px;
    background: #f3f4f7;
    overflow-y: auto;
}

/* Form box */
.form-box {
    max-width: 550px;
    margin: auto;
    padding: 25px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-box input, .form-box textarea {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    margin-bottom: 15px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

.button {
    width: 100%;
    padding: 14px;
    background: #4e5cff;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
}

.button:hover { background: #3b47d6; }

</style>
</head>
<body>

<div class="container">

    <!-- SIDEBAR COPY DARI DASHBOARD -->
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

        <h1><?= $edit ? "Edit Film" : "Tambah Film" ?></h1>

        <div class="form-box">

            <form method="POST" enctype="multipart/form-data">

                <label>Judul Film</label>
                <input type="text" name="title" required value="<?= $film['title'] ?>">

                <label>Genre</label>
                <input type="text" name="genre" required value="<?= $film['genre'] ?>">

                <label>Durasi (menit)</label>
                <input type="number" name="duration" required value="<?= $film['duration'] ?>">

                <label>Poster Film</label>
                <input type="file" name="poster">

                <?php if ($edit && $film['poster']): ?>
                    <img src="assets/uploads/<?= $film['poster'] ?>" width="140" style="border-radius:10px;margin-bottom:10px">
                <?php endif; ?>

                <label>Deskripsi</label>
                <textarea name="description" rows="4"><?= $film['description'] ?></textarea>

                <button class="button" type="submit">Simpan</button>
            </form>

        </div>
    </main>

</div>

</body>
</html>
