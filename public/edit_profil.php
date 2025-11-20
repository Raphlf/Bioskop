<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_login();

$user = $_SESSION['user'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name == '') {
        $message = "Nama tidak boleh kosong!";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute([$name, $user['id']]);
        $_SESSION['user']['name'] = $name;
        $message = "Profil berhasil diperbarui!";
    }
}
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
    body {
        background: #0d0d16;
        font-family: "Poppins", sans-serif;
        color: #e8e8e8;
    }

    .edit-container {
        max-width: 480px;
        margin: 40px auto;
        padding: 25px;
        background: #141426;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(122, 0, 255, 0.25);
    }

    .edit-container h2 {
        text-align: center;
        color: #c084ff;
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        color: #cfcfcf;
    }

    input {
        width: 100%;
        padding: 10px;
        background: #1e1e32;
        border: 1px solid #3b3b5c;
        border-radius: 8px;
        color: white;
        margin-bottom: 15px;
    }

    input:disabled {
        opacity: 0.6;
    }

    button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(90deg, #7e22ce, #a855f7);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: white;
        font-weight: bold;
        letter-spacing: 0.5px;
        transition: 0.2s;
    }

    button:hover {
        background: linear-gradient(90deg, #a855f7, #7e22ce);
        transform: scale(1.02);
    }

    .message {
        padding: 10px;
        background: #1e1e32;
        border-left: 4px solid #a855f7;
        border-radius: 5px;
        margin-bottom: 15px;
        color: #d3b4ff;
        text-align: center;
    }

    .back-btn {
        display: block;
        margin-top: 15px;
        color: #b37bff;
        text-align: center;
        font-size: 14px;
    }

    .back-btn:hover {
        text-decoration: underline;
    }
</style>

<div class="edit-container">
    <h2>Edit Profil</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nama Lengkap</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label>Email</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a href="profil.php" class="back-btn">Kembali ke Profil</a>
</div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
