<?php
require_once __DIR__ . "/../../src/db.php";

// helper alert
function alert_back($msg) {
    echo "<script>alert(" . json_encode($msg) . "); history.back();</script>";
    exit;
}

// pastikan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: jadwal_form.php");
    exit;
}

// Ambil input aman
$film_id   = $_POST['film_id']   ?? '';
$studio_id = $_POST['studio_id'] ?? '';
$date      = $_POST['date']      ?? '';
$time      = $_POST['time']      ?? '';
$price     = $_POST['price']     ?? '';
$id        = $_POST['id']        ?? ''; // edit

// Validasi
if ($film_id === '' || $studio_id === '' || $date === '' || $time === '' || $price === '') {
    alert_back("Form tidak lengkap. Pastikan film, studio, tanggal, jam, dan harga telah diisi.");
}

// Validasi format tanggal
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
    alert_back("Format tanggal salah (YYYY-MM-DD).");
}

// Validasi format waktu
if (!preg_match("/^\d{2}:\d{2}$/", $time)) {
    alert_back("Format jam salah (HH:MM)");
}

$show_time = $date . " " . $time . ":00";

// Pastikan studio valid
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM studios WHERE id = ?");
    $stmt->execute([$studio_id]);
    if ($stmt->fetchColumn() == 0) {
        alert_back("Studio tidak ditemukan!");
    }
} catch (PDOException $e) {
    alert_back("DB Error (studio check): " . $e->getMessage());
}

// Cek bentrok jadwal
try {
    if ($id) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM schedules
            WHERE show_time = ? AND studio_id = ? AND id != ?
        ");
        $stmt->execute([$show_time, $studio_id, $id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM schedules
            WHERE show_time = ? AND studio_id = ?
        ");
        $stmt->execute([$show_time, $studio_id]);
    }

    if ($stmt->fetchColumn() > 0) {
        alert_back("Jadwal bentrok! Studio sudah dipakai pada waktu ini.");
    }

} catch (PDOException $e) {
    alert_back("DB Error (cek bentrok): " . $e->getMessage());
}

// INSERT / UPDATE
try {
    if ($id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE schedules
            SET film_id = ?, studio_id = ?, show_time = ?, price = ?
            WHERE id = ?
        ");
        $stmt->execute([$film_id, $studio_id, $show_time, $price, $id]);
        header("Location: jadwal_manage.php?success=Jadwal berhasil diperbarui");
        exit;

    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO schedules (film_id, studio_id, show_time, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$film_id, $studio_id, $show_time, $price]);
        header("Location: jadwal_manage.php?success=Jadwal berhasil ditambahkan");
        exit;
    }

} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        alert_back("Gagal menyimpan! Periksa foreign key film / studio.");
    }

    alert_back("DB Error (save): " . $e->getMessage());
}
