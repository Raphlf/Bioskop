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
    /* ===================== TEMA TERANG (LIGHT THEME) ===================== */
    body {
        margin: 0;
        padding: 0;
        background: #f4f6f8; 
        font-family: "Poppins", sans-serif;
        color: #1f2937; 
        padding-top: 118px;
    }

    .edit-container {
        max-width: 480px;
        margin: 40px auto;
        padding: 30px;
        background: #ffffff; 
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08); 
        border: 1px solid #e5e7eb;
    }

    .edit-container h2 {
        text-align: center;
        color: #0f172a; 
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        color: #4b5563; 
    }

    input {
        width: 100%;
        padding: 12px;
        background: #f9fafb; 
        border: 1px solid #d1d5db;
        border-radius: 8px;
        color: #1f2937;
        margin-bottom: 15px;
        transition: border-color 0.2s;
    }

    input:focus {
        outline: none;
        border-color: #6366f1; 
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }

    input:disabled {
        background: #f3f4f6;
        opacity: 1;
    }

    /* CONTAINER BARU UNTUK DUA TOMBOL */
    .button-actions {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 20px;
    }

    /* STYLING UMUM UNTUK KEDUA TOMBOL */
    .button-actions button,
    .button-actions .btn-link {
        padding: 12px 20px; /* Padding yang cukup */
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        letter-spacing: 0.5px;
        transition: 0.2s;
        text-align: center;
        width: 50%; /* Membuat tombol berbagi lebar 50% */
    }

    /* TOMBOL SIMPAN (PRIMARY) */
    .button-actions button[type="submit"] {
        background: #6366f1; 
        color: white;
    }

    .button-actions button[type="submit"]:hover {
        background: #4f46e5;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    /* TOMBOL KEMBALI (SECONDARY/LINK) */
    .button-actions .btn-link {
        /* Menggunakan warna background terang dengan border */
        background: #ffffff;
        color: #4b5563;
        border: 1px solid #d1d5db;
        text-decoration: none;
    }
    
    .button-actions .btn-link:hover {
        background: #f3f4f6;
        color: #1f2937;
    }

    .message {
        padding: 12px;
        background: #ecfdf5; 
        border-left: 4px solid #10b981; 
        border-radius: 5px;
        margin-bottom: 20px;
        color: #065f46; 
        font-weight: 500;
        text-align: center;
    }
    
    <?php if ($message && strpos($message, 'kosong') !== false): ?>
    .message {
        background: #fee2e2;
        border-left-color: #ef4444;
        color: #991b1b;
    }
    <?php endif; ?>

    /* Hapus styling back-btn lama */
    .back-btn {
        display: none;
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
        
        <div class="button-actions">
            <button type="submit">Simpan Perubahan</button>
            
            <a href="profil.php" class="btn-link">← Kembali</a>
        </div>
    </form>

    </div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>