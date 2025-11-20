<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/helpers.php';
require_admin();

$jadwals = $pdo->query("SELECT s.*, f.title FROM schedules s JOIN films f ON s.film_id = f.id ORDER BY s.show_date, s.show_time")->fetchAll();
?>

<?php include __DIR__ . '/../../src/templates/header.php'; ?>

<h2>Manage Jadwal</h2>
<p><a href="<?= BASE_URL ?>/admin/jadwal_form.php" class="btn">Tambah Jadwal</a></p>

<table class="table">
    <thead><tr><th>ID</th><th>Film</th><th>Tanggal</th><th>Jam</th><th>Harga</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach($jadwals as $j): ?>
            <tr>
                <td><?= $j['id'] ?></td>
                <td><?= esc($j['title']) ?></td>
                <td><?= esc($j['show_date']) ?></td>
                <td><?= esc($j['show_time']) ?></td>
                <td>Rp<?= number_format($j['price']) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/jadwal_form.php?id=<?= $j['id'] ?>">Edit</a> |
                    <a href="<?= BASE_URL ?>/admin/jadwal_manage.php?delete=<?= $j['id'] ?>" onclick="return confirm('Hapus jadwal?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/jadwal_manage.php');
    exit;
}
?>

<?php include __DIR__ . '/../../src/templates/footer.php'; ?>
