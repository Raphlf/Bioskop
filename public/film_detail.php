<?php
// PHP LOGIC SECTION (Tidak ada perubahan pada logika pengambilan data)
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

if (!isset($_GET['id'])) {
    die("Film tidak ditemukan.");
}

$film_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM films WHERE id = ? LIMIT 1");
$stmt->execute([$film_id]);
$film = $stmt->fetch();

if (!$film) {
    die("Film tidak ditemukan.");
}

// Ambil list jadwal film
$jadwal = $pdo->prepare("
    SELECT s.*, st.name AS studio_name
    FROM schedules s
    JOIN studios st ON s.studio_id = st.id
    WHERE film_id = ?
    ORDER BY show_time
");
$jadwal->execute([$film_id]);
$jadwals = $jadwal->fetchAll();

include __DIR__ . '/../src/templates/header.php';
?>

<style>
/* ------------------------------------------- */
/* CSS FIX UNTUK STICKY FOOTER */
/* ------------------------------------------- */
html {
    height: 100%;
}
body {
    display: flex;
    flex-direction: column; 
    min-height: 100vh;      
    margin: 0;
    padding: 0;
    background: #f9fafb;
    font-family: system-ui, sans-serif;
}
/* Bagian ini membungkus semua konten di antara header dan footer */
.main-content-wrapper { 
    flex: 1; /* Konten utama akan meregang dan mendorong footer ke bawah */
    width: 100%;
}

/* ------------------------------------------- */
/* CSS DETAIL FILM */
/* ------------------------------------------- */

/* Container */
.detail-container {
    max-width: 1100px;
    margin: 120px auto;
    display: grid;
    grid-template-columns: 1fr 2fr; /* Layout Poster (1fr) dan Info (2fr) */
    gap: 40px;
    padding: 0 20px;
}

/* Poster */
.detail-poster img {
    width: 100%;
    border-radius: 16px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

/* Info */
.detail-info h1 {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 10px;
}
.detail-meta {
    color: #6b7280;
    margin-bottom: 20px;
}
.detail-desc {
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 25px;
}

/* Jadwal */
.jadwal-box {
    margin-top: 30px;
}
.jadwal-item {
    background: white;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 14px;
    border: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.jadwal-item button {
    background: #2563eb;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}

/* FOOTER (margin-top dihapus) */
footer {
    margin: 0; /* PENTING: Menghapus margin-top lama */
    padding: 0;
    width: 100%;
    background: #0C1E35;
    color: white;
    text-align: center;
}
</style>

<main class="main-content-wrapper"> 
    <div class="detail-container">

        <div class="detail-poster">
            <img src="<?= BASE_URL ?>/<?= esc($film['poster']) ?>">
        </div>

        <div class="detail-info">
            <h1><?= esc($film['title']) ?></h1>
            <div class="detail-meta">
                <?= esc($film['genre']) ?> • <?= esc($film['duration']) ?> menit
            </div>

            <p class="detail-desc"><?= nl2br(esc($film['description'])) ?></p>

            <h3>Sinopsis</h3>
            <p class="detail-synopsis"><?= nl2br(esc($film['description'])) ?></p>

            <h3>Jadwal Tersedia</h3>

            <div class="jadwal-box">
                <?php if (count($jadwals) == 0): ?>
                    <p style="color:#777;">Belum ada jadwal.</p>
                <?php else: ?>
                    
                    <?php foreach($jadwals as $j): ?>
                        <div class="jadwal-item">
                            <div>
                                <b><?= date('d M Y H:i', strtotime($j['show_time'])) ?></b><br>
                                Studio: <?= $j['studio_name'] ?><br>
                                Harga: Rp<?= number_format($j['price'],0,',','.') ?>
                            </div>

                            <form method="GET" action="<?= BASE_URL ?>/choose_seat.php">
                                <input type="hidden" name="schedule_id" value="<?= $j['id'] ?>">
                                <button type="submit">Pesan</button>
                            </form>
                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>
            </div>

        </div>

    </div>

</main>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>