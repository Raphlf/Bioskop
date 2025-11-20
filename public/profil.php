<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/helpers.php';
require_login();
$user = $_SESSION['user'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name=='') $message = 'Nama tidak boleh kosong';
    else {
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute([$name, $user['id']]);
        $_SESSION['user']['name'] = $name;
        $message = 'Profil diperbarui';
    }
}
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<h2>Profil</h2>
<?php if($message) echo '<p class="info">'.esc($message).'</p>'; ?>
<form method="POST" class="form-card">
    <label>Nama</label>
    <input type="text" name="name" value="<?= esc($user['name']) ?>" required>
    <label>Email</label>
    <input type="email" value="<?= esc($user['email']) ?>" disabled>
    <button type="submit">Simpan</button>
</form>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
