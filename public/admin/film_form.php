<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/helpers.php';
require_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$film = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM films WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $film = $stmt->fetch();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $duration = intval($_POST['duration']);
    $desc = trim($_POST['description']);

    if ($title === '') $errors[] = 'Title wajib diisi';

    $poster_path = $film['poster'] ?? '';
    if (!empty($_FILES['poster']['name'])) {
        $up = upload_image($_FILES['poster']);
        if ($up) $poster_path = $up;
        else $errors[] = 'Upload poster gagal (pastikan jpg/png & <2MB)';
    }

    if (empty($errors)) {
        if ($film) {
            $stmt = $pdo->prepare("UPDATE films SET title=?, genre=?, duration=?, description=?, poster=? WHERE id=?");
            $stmt->execute([$title, $genre, $duration, $desc, $poster_path, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO films (title,genre,duration,description,poster) VALUES (?,?,?,?,?)");
            $stmt->execute([$title, $genre, $duration, $desc, $poster_path]);
        }
        header('Location: ' . BASE_URL . '/admin/films_manage.php');
        exit;
    }
}
?>

<?php include __DIR__ . '/../../src/templates/header.php'; ?>

<h2><?= $film ? 'Edit' : 'Tambah' ?> Film</h2>

<?php if(!empty($errors)): ?>
    <ul class="error">
        <?php foreach($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="form-card">
    <label>Title</label>
    <input type="text" name="title" value="<?= $film ? esc($film['title']) : '' ?>" required>

    <label>Genre</label>
    <input type="text" name="genre" value="<?= $film ? esc($film['genre']) : '' ?>">

    <label>Duration (menit)</label>
    <input type="number" name="duration" value="<?= $film ? esc($film['duration']) : '' ?>">

    <label>Description</label>
    <textarea name="description"><?= $film ? esc($film['description']) : '' ?></textarea>

    <label>Poster (jpg/png, &lt;2MB)</label>
    <input type="file" name="poster" accept="image/*">

    <button type="submit">Simpan</button>
</form>

<?php include __DIR__ . '/../../src/templates/footer.php'; ?>
