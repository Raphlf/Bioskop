<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/helpers.php';
require_admin();

$films = $pdo->query("SELECT * FROM films ORDER BY created_at DESC")->fetchAll();
?>

<?php include __DIR__ . '/../../src/templates/header.php'; ?>

<h2>Manage Films</h2>
<p><a href="<?= BASE_URL ?>/admin/film_form.php" class="btn">Tambah Film</a></p>

<table class="table">
    <thead><tr><th>ID</th><th>Title</th><th>Genre</th><th>Durasi</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach($films as $f): ?>
            <tr>
                <td><?= $f['id'] ?></td>
                <td><?= esc($f['title']) ?></td>
                <td><?= esc($f['genre']) ?></td>
                <td><?= esc($f['duration']) ?> menit</td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/film_form.php?id=<?= $f['id'] ?>">Edit</a> |
                    <a href="<?= BASE_URL ?>/admin/films_manage.php?delete=<?= $f['id'] ?>" onclick="return confirm('Hapus film?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM films WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . BASE_URL . '/admin/films_manage.php');
    exit;
}
?>

<?php include __DIR__ . '/../../src/templates/footer.php'; ?>
