<?php
require_once "../../src/db.php";

// DELETE USER
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    header("Location: users_manage.php");
    exit;
}

// UPDATE ROLE
if (isset($_POST['update_role'])) {
    $id   = $_POST['id'];
    $role = $_POST['role'];

    $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role, $id]);

    header("Location: users_manage.php");
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kelola User</title>

    <style>
* {
    margin: 0; padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* Layout */
.container { display: flex; height: 100vh; }

/* Sidebar */
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
    transition: 0.2s;
}

.menu a.active,
.menu a:hover {
    background: #4e5cff;
    color: white;
}

.logout { color: #ff6b6b !important; }

/* Content */
.content {
    flex: 1;
    padding: 30px;
    background: #f3f4f7;
    overflow-y: auto;
}

.content h1 { font-size: 32px; margin-bottom: 10px; }

/* Table */
.table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    background: #4e5cff;
    color: white;
    padding: 12px;
    text-align: left;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.btn {
    padding: 7px 12px;
    text-decoration: none;
    color: white;
    border-radius: 6px;
    font-size: 14px;
}

.btn-danger { background: #ff6b6b; }
.btn-warning { background: #f4a742; }

/* Role Edit Box */
.role-box {
    background: white;
    padding: 20px;
    margin-top: 20px;
    border-radius: 12px;
    width: 400px;
}

.role-box select, .role-box button {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #aaa;
}

.role-box button {
    background: #4e5cff;
    color: white;
    border: none;
}
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
            <li><a class="active" href="users_manage.php">Kelola User</a></li>
            <li><a href="exports.php">Export Data</a></li>
            <li><a href="../logout.php" class="logout">Logout</a></li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="content">

        <h1>Kelola User</h1>

        <!-- Table -->
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['role'] ?></td>
                <td>
                    <a class="btn-warning btn" href="users_manage.php?edit=<?= $u['id'] ?>">Edit Role</a>
                    <a class="btn-danger btn" href="users_manage.php?delete=<?= $u['id'] ?>" onclick="return confirm('Yakin hapus user?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Edit Role Only -->
        <?php if (isset($_GET['edit'])):
            $id = $_GET['edit'];
            $st = $pdo->prepare("SELECT * FROM users WHERE id=?");
            $st->execute([$id]);
            $user = $st->fetch();
        ?>
        <div class="role-box">
            <h3>Edit Role User</h3>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                <select name="role">
                    <option value="user" <?= $user['role']=="user"?"selected":"" ?>>User</option>
                    <option value="admin" <?= $user['role']=="admin"?"selected":"" ?>>Admin</option>
                </select>

                <button type="submit" name="update_role">Simpan Perubahan</button>
            </form>
        </div>
        <?php endif; ?>

    </main>

</div>

</body>
</html>
