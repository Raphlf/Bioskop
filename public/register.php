<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/helpers.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($name === '') $errors[] = 'Nama wajib diisi';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
    if ($password !== $password2) $errors[] = 'Password tidak sama';

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
        $stmt->execute([$name, $email, $hash]);
        header('Location: login.php');
        exit;
    }
}
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<h2>Daftar</h2>

<form method="POST" class="form-card">
    <label>Nama</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Ulangi Password</label>
    <input type="password" name="password2" required>

    <?php if(!empty($errors)): ?>
        <ul class="error">
            <?php foreach($errors as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <button type="submit">Daftar</button>
</form>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
