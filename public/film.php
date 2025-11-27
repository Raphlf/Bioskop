<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM films";
$params = [];

if (!empty($search)) {
    $query .= " WHERE title LIKE ?";
    $params[] = '%' . $search . '%';
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$films = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
/* ===================== GLOBAL ===================== */
body {
    background: #f8fafb;
    margin: 0;
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    color: #111827;
}

/* ===================== TITLE ===================== */
.page-title {
    text-align: center;
    margin: 110px 0 30px;
    font-size: 32px;
    font-weight: 800;
    color: #1f2937;
}

/* ===================== GRID ===================== */
.film-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 26px;
    max-width: 1180px;
    width: 90%;
    margin: 0 auto 80px;
}

/* ===================== CARD FIX CLICK ===================== */
.film-card {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    text-align: center;
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
    transition: transform .2s ease, box-shadow .2s ease;
    position: relative;
}
.film-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 30px rgba(0,0,0,0.12);
}

.film-card a {
    display: block;
    width: 100%;
    height: 100%;
    color: inherit;
    text-decoration: none;
}

/* Poster */
.poster {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
}

/* Body */
.film-body {
    padding: 14px 16px 20px;
}
.film-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 6px;
}
.film-sub {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 10px;
}

/* Badge */
.film-badge {
    background: #33aa77;
    color: white;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 10px;
}

/* =========================================================
   SEARCH BAR SECTION
   ========================================================= */
.search-container {
    max-width: 600px;
    margin: 0 auto 40px;
    display: flex;
    justify-content: center;
}

.search-container form {
    display: flex;
    gap: 12px;
    align-items: center;
    width: 100%;
    max-width: 500px;
}

.search-input {
    flex: 1;
    padding: 14px 18px;
    border: 2px solid #d1d5db;
    border-radius: 12px;
    font-size: 16px;
    font-family: inherit;
    background: #ffffff;
    color: #374151;
    transition: border-color 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.search-btn {
    padding: 14px 24px;
    background: #6366f1;
    color: #ffffff;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}

.search-btn:hover {
    background: #4f46e5;
}

.clear-search {
    color: #6b7280;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    margin-left: 12px;
    transition: color 0.2s ease;
}

.clear-search:hover {
    color: #374151;
}

/* Responsive search */
@media (max-width: 600px) {
    .search-container form {
        flex-direction: column;
        gap: 10px;
    }

    .search-input {
        width: 100%;
    }

    .clear-search {
        margin-left: 0;
        align-self: center;
    }
}
</style>

<h2 class="page-title">Daftar Film</h2>

<!-- SEARCH BAR -->
<div class="search-container">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Cari film berdasarkan judul..." value="<?= esc($search) ?>" class="search-input">
        <button type="submit" class="search-btn">Cari</button>
        <?php if (!empty($search)): ?>
            <a href="<?= BASE_URL ?>/film.php" class="clear-search">Hapus Pencarian</a>
        <?php endif; ?>
    </form>
</div>

<div class="film-list">

<?php if (count($films) == 0): ?>
    <p style="grid-column: 1 / -1; text-align: center; color: #6b7280; font-size: 18px; padding: 40px;">
        <?php if (!empty($search)): ?>
            Tidak ada film yang ditemukan untuk pencarian "<?= esc($search) ?>".
        <?php else: ?>
            Belum ada film tersedia.
        <?php endif; ?>
    </p>
<?php else: ?>
    <?php foreach($films as $f): ?>
        <div class="film-card">
            <a href="<?= BASE_URL ?>/film_detail.php?id=<?= $f['id'] ?>">

                <img src="<?= BASE_URL ?>/<?= esc($f['poster']) ?>" 
                     alt="<?= esc($f['title']) ?>" 
                     class="poster">

                <div class="film-body">

                    <span class="film-badge">Get Ticket</span>

                    <div class="film-title"><?= esc($f['title']) ?></div>

                    <div class="film-sub">
                        <?= esc($f['genre']) ?> • <?= esc($f['duration']) ?> menit
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
