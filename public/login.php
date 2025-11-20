<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/helpers.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        header("Location: " . BASE_URL . "/index.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<h2>Login</h2>

<form method="POST" class="form-card">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <?php if($error): ?>
        <p class="error"><?= esc($error) ?></p>
    <?php endif; ?>

    <button type="submit">Login</button>

    <p style="margin-top: 10px;">
        Belum punya akun? 
        <a href="<?= BASE_URL ?>/register.php">Register</a>
    </p>

</form>


<?php include __DIR__ . '/../src/templates/footer.php'; ?>
