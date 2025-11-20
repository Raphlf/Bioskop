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
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>

<style>
    body {
        margin: 0;
        padding: 0;
        background: #0c1b2a;
        font-family: Arial, sans-serif;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
    }

    .card {
        background: #112233;
        padding: 40px;
        width: 400px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    h2 {
        margin-top: 0;
        margin-bottom: 25px;
        color: #ffb0b7;
        font-size: 28px;
        letter-spacing: 1px;
    }

    label {
        display: block;
        text-align: left;
        margin: 10px 0 5px;
        font-size: 15px;
    }

    input {
        width: 100%;
        padding: 12px;
        background: #0e1a27;
        border: none;
        border-radius: 25px;
        color: white;
        outline: none;
        font-size: 15px;
        margin-bottom: 12px;
        padding-left: 15px;
        border: 1px solid #1d2f45;
    }

    button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        background: linear-gradient(to right, #ff9a9e, #a1e0e5);
        color: #111;
        margin-top: 10px;
    }

    .error {
        background: rgba(255, 70, 70, 0.85);
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 15px;
        text-align: left;
        font-size: 14px;
    }

    .error li {
        margin-left: 20px;
    }

    .link {
        margin-top: 15px;
        font-size: 14px;
    }

    .link a {
        color: #7ecbff;
        text-decoration: none;
    }

    .link a:hover {
        text-decoration: underline;
    }

    @media(max-width: 450px) {
        .card {
            width: 90%;
            padding: 30px;
        }
    }
</style>

</head>
<body>

<div class="card">
    <h2>Register</h2>

    <?php if(!empty($errors)): ?>
        <ul class="error">
            <?php foreach($errors as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">

        <label>Nama</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Ulangi Password</label>
        <input type="password" name="password2" required>

        <button type="submit">Daftar</button>

        <div class="link">
            Sudah punya akun?
            <a href="login.php">Login</a>
        </div>
    </form>
</div>

</body>
</html>

