<?php
// public/admin/studio_form.php
include __DIR__ . '/../../config.php';

$mode = $_GET['mode'] ?? ($_POST['mode'] ?? 'add');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $mode === 'add' && isset($_GET['nama'])) {
    // quick-add dari studio_manage form (GET)
    $nama = $db->real_escape_string($_GET['nama']);
    $baris = (int)$_GET['baris'];
    $kolom = (int)$_GET['kolom'];

    $db->query("INSERT INTO studios (nama_studio, baris, kolom) VALUES ('$nama', $baris, $kolom)");
    $studio_id = $db->insert_id;
    // redirect to kursi_manage untuk generate seat grid
    header("Location: kursi_manage.php?studio_id=$studio_id&action=generate");
    exit;
}

// POST handling for edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'edit') {
    $id = (int)$_POST['id'];
    $nama = $db->real_escape_string($_POST['nama']);
    $baris = (int)$_POST['baris'];
    $kolom = (int)$_POST['kolom'];

    $db->query("UPDATE studios SET nama_studio='$nama', baris=$baris, kolom=$kolom WHERE id=$id");
    header("Location: studio_manage.php");
    exit;
}

// show edit form
if ($mode === 'edit') {
    $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
    $studio = $db->query("SELECT * FROM studios WHERE id=$id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Studio Form</title>
<style>
body{font-family:Arial;background:#f7f8fb;padding:20px}
.container{max-width:680px;margin:0 auto;background:#fff;padding:18px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06)}
.input{width:100%;padding:10px;margin:8px 0;border-radius:6px;border:1px solid #ccc}
.btn{background:#4e5cff;color:#fff;padding:10px 16px;border-radius:6px;border:none;cursor:pointer}
</style>
</head>
<body>
<div class="container">
<a href="studio_manage.php">⬅ Kembali</a>
<h2><?= $mode === 'edit' ? 'Edit Studio' : 'Tambah Studio' ?></h2>

<form method="post">
    <input type="hidden" name="mode" value="<?= $mode ?>">
    <?php if ($mode === 'edit'): ?>
        <input type="hidden" name="id" value="<?= $studio['id'] ?>">
    <?php endif; ?>

    <label>Nama Studio</label>
    <input class="input" name="nama" required value="<?= $studio['nama_studio'] ?? '' ?>">

    <label>Baris</label>
    <input class="input" name="baris" type="number" min="1" required value="<?= $studio['baris'] ?? '8' ?>">

    <label>Kolom</label>
    <input class="input" name="kolom" type="number" min="1" required value="<?= $studio['kolom'] ?? '10' ?>">

    <button class="btn" type="submit"><?= $mode === 'edit' ? 'Simpan Perubahan' : 'Tambah Studio' ?></button>
</form>
</div>
</body>
</html>
