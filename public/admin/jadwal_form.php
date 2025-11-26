<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form Jadwal</title>
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
    padding: 8px 12px;
    background: #4e5cff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
}

.btn-danger {
    background: #ff6b6b;
}

.btn-warning {
    background: #f4a742;
}

.form-box {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    width: 450px;
    margin-top: 20px;
}

.form-box input,
.form-box select,
.form-box textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #aaa;
    margin-bottom: 15px;
}

.form-box button {
    width: 100%;
    padding: 10px;
    background: #4e5cff;
    color: #fff;
    border: none;
    border-radius: 8px;
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
        <h1>Form Jadwal</h1>

        <div class="form-box">
            <form method="post">

                <label>Film</label>
                <select>
                    <option value="">-- Pilih Film --</option>
                    <option>Avatar</option>
                </select>

                <label>Tanggal</label>
                <input type="date" required>

                <label>Jam Tayang</label>
                <input type="time" required>

                <button type="submit">Simpan</button>
            </form>
        </div>

    </main>

</div>

</body>
</html>
