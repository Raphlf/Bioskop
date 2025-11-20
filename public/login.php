<?php
session_start();
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/db.php';

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

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    background: #0d1b2a;
    color: #eee;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.page-center {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.form-card {
    background: #14243c;
    padding: 35px;
    width: 100%;
    max-width: 420px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.05);
}

.form-card h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #ffb3b3;
    font-size: 26px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-size: 15px;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    background: #0f1a2b;
    border: 1px solid #22344f;
    border-radius: 30px;
    color: white;
    font-size: 15px;
}

input:focus {
    border-color: #4ac1ff;
    box-shadow: 0 0 8px rgba(74,193,255,0.4);
    outline: none;
}

button[type="submit"] {
    width: 100%;
    padding: 12px;
    background: linear-gradient(45deg, #ff7b93, #ffc3c3, #95e3e8);
    border: none;
    border-radius: 30px;
    font-size: 17px;
    font-weight: 600;
    cursor: pointer;
    color: #000;
}

button[type="submit"]:hover {
    opacity: .85;
    transform: translateY(-2px);
}

.error {
    background: #3b0e0e;
    border-left: 4px solid #ff4e4e;
    color: #ffb2b2;
    padding: 10px 12px;
    border-radius: 6px;
    margin-bottom: 12px;
}

@media (max-width: 480px) {
    .form-card {
        padding: 25px;
        border-radius: 14px;
    }
    input, button[type="submit"] {
        font-size: 14px;
        padding: 11px;
    }
}
</style>

</head>
<body>

<div class="page-center">
    <div class="form-card">
        <h2>Login</h2>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <button type="submit">Login</button>

            <p style="margin-top: 12px; text-align:center;">
                Belum punya akun?
                <a href="register.php" style="color:#7ecbff; text-decoration:none;">Register</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
