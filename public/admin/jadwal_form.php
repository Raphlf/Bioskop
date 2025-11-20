<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/helpers.php';
require_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$jadwal = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $jadwal = $stmt->fetch();
}

$films = $pdo->query("SELECT * FROM films ORDER BY title")->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $film_id = intval($_POST['film_id']);
    $date = $_POST['show_date'];
    $time = $_POST['show_time'];
    $price = floatval($_POST['price']);
    $seats = intval($_POST['seats_total']);

    if ($film_id <= 0) $errors[] = 'Pilih film';
    if ($date == '' || $time == '') $errors[] = 'Tanggal/waktu wajib diisi';

    if (empty($errors)) {
        if ($jadwal) {
            $stmt = $pdo->prepare("UPDATE schedules SET film_id=?, show_date=?, show_time=?, price=?, seats_total=?, seats_available=? WHERE id=?");
            $stmt->execute([$film_id, $date, $time, $price, $seats, $seats, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO schedules (film_id,show_date,show_time,price,seats_total,seats_available) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$film_id, $date, $time, $price, $seats, $seats]);
        }
        header('Location: ' . BASE_URL . '/admin/jadwal_manage.php');
        exit;
    }
}
?>

<?php include __DIR__ . '/../../src/templates/header.php'; ?>

<h2><?= $jadwal ? 'Edit' : 'Tambah' ?> Jadwal</h2>

<?php if(!empty($errors)): ?>
    <ul class="error">
        <?php foreach($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" class="form-card">
    <label>Film</label>
    <select name="film_id" required>
        <option value="">-- Pilih --</option>
        <?php foreach($films as $f): ?>
            <option value="<?= $f['id'] ?>" <?= $jadwal && $jadwal['film_id']==$f['id'] ? 'selected' : '' ?>><?= esc($f['title']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Tanggal</label>
    <input type="date" name="show_date" value="<?= $jadwal ? esc($jadwal['show_date']) : '' ?>" required>

    <label>Waktu</label>
    <input type="time" name="show_time" value="<?= $jadwal ? esc($jadwal['show_time']) : '' ?>" required>

    <label>Harga</label>
    <input type="number" step="0.01" name="price" value="<?= $jadwal ? esc($jadwal['price']) : '' ?>" required>

    <label>Jumlah Kursi</label>
    <input type="number" name="seats_total" value="<?= $jadwal ? esc($jadwal['seats_total']) : 100 ?>" required>

    <button type="submit">Simpan</button>
</form>

<?php include __DIR__ . '/../../src/templates/footer.php'; ?>
