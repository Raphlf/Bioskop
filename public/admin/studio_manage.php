<?php
// public/admin/studio_manage.php
include __DIR__ . '/../../config.php'; // sesuaikan path jika perlu

// Handle delete (opsional)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->query("DELETE FROM studios WHERE id = $id");
    header("Location: studio_manage.php");
    exit;
}

// Ambil semua studio
$res = $db->query("SELECT * FROM studios ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admin - Kelola Studio</title>
<style>
/* minimal style */
body{font-family:Arial;margin:20px;background:#f7f8fb}
.container{max-width:980px;margin:0 auto}
h2{margin-bottom:12px}
.table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.06)}
.table th{background:#222;color:#fff;padding:12px;text-align:left}
.table td{padding:12px;border-bottom:1px solid #eee}
.form-inline input{padding:8px;margin-right:8px;border-radius:6px;border:1px solid #ccc}
.btn{background:#4e5cff;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none}
.btn-danger{background:#e53935}
.small{font-size:13px;color:#666}
</style>
</head>
<body>
<div class="container">
<a href="dashboard.php" class="small">⬅ Kembali</a>
<h2>Kelola Studio</h2>

<!-- Form Tambah (simple) -->
<form class="form-inline" action="studio_form.php" method="get" style="margin-bottom:18px">
    <input name="mode" value="add" type="hidden">
    <input name="nama" placeholder="Nama studio (mis. Studio 1)" required>
    <input name="baris" type="number" placeholder="Baris (mis. 8)" min="1" required>
    <input name="kolom" type="number" placeholder="Kolom (mis. 10)" min="1" required>
    <button class="btn">Tambah Studio</button>
</form>

<table class="table">
<tr><th>ID</th><th>Nama Studio</th><th>Baris</th><th>Kolom</th><th>Aksi</th></tr>
<?php while($s = $res->fetch_assoc()): ?>
<tr>
    <td><?= $s['id'] ?></td>
    <td><?= htmlspecialchars($s['nama_studio']) ?></td>
    <td><?= $s['baris'] ?></td>
    <td><?= $s['kolom'] ?></td>
    <td>
        <a class="btn" href="kursi_manage.php?studio_id=<?= $s['id'] ?>">Kelola Kursi</a>
        <a class="btn" href="studio_form.php?mode=edit&id=<?= $s['id'] ?>">Edit</a>
        <a class="btn btn-danger" href="studio_manage.php?delete=<?= $s['id'] ?>" onclick="return confirm('Hapus studio? Semua kursi & referensi juga akan terhapus.')">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
