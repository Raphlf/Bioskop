<?php
// PHP LOGIC
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
/* ===================================================
   GLOBAL — agar footer selalu di bawah
=================================================== */
html, body {
    height: 100%;
    margin: 0;
}
body {
    display: flex;
    flex-direction: column;
    background: #f5f6fa;
    font-family: "Inter", system-ui, sans-serif;
}
#content-wrapper {
    flex: 1; /* biar footer turun */
    padding-top: 110px;
    max-width: 1180px;
    width: 92%;
    margin: auto;
}

/* ===================================================
   TITLE
=================================================== */
.page-title {
    text-align: center;
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 40px;
}

/* ===================================================
   SEARCH BAR — Clean TIX ID Style
=================================================== */

.search-container {
    max-width: 650px;
    margin: 0 auto 45px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-input {
    flex: 1;
    padding: 14px 48px 14px 18px;
    border-radius: 12px;
    border: 2px solid #d1d5db;
    font-size: 16px;
    transition: .2s;
    background: #fff;
}

.search-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    outline: none;
}

/* Tombol X */
.clear-x {
    position: relative;
    margin-left: -40px;
    margin-right: 5px;
    font-size: 20px;
    cursor: pointer;
    color: #9ca3af;
    display: none;
}

/* Tombol Cari */
.search-btn {
    padding: 14px 28px;
    border-radius: 12px;
    border: none;
    background: #6366f1;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: .2s;
    font-size: 16px;
}

.search-btn:hover {
    background: #4f46e5;
}

/* ===================================================
   GRID FILM
=================================================== */

.film-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 26px;
    margin-bottom: 80px;
}

.film-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    transition: .25s ease;
}

.film-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 34px rgba(0,0,0,.12);
}

.poster {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
}

.film-body {
    padding: 14px;
    text-align: center;
}

.film-title {
    font-size: 17px;
    font-weight: 700;
    color: #111827;
}

.film-sub {
    font-size: 14px;
    color: #6b7280;
}

/* Responsive */
@media (max-width: 600px) {
    .search-box { flex-direction: column; }
    .search-btn { width: 100%; }
    .clear-x { margin-left: -30px; }
}
</style>


<div id="content-wrapper">

    <h2 class="page-title">Daftar Film</h2>

    <!-- ==================== SEARCH BAR ==================== -->
    <div class="search-container">
        <form method="GET" action="">
            <div class="search-box">

                <input type="text" 
                       name="search" 
                       id="searchInput"
                       placeholder="Cari film berdasarkan judul..." 
                       value="<?= esc($search) ?>"
                       class="search-input">

                <!-- X -->
                <span id="clearSearch" class="clear-x">&times;</span>

                <!-- Tombol cari -->
                <button type="submit" class="search-btn">Cari</button>

            </div>
        </form>
    </div>

    <!-- ==================== LIST FILM ==================== -->
    <div class="film-list">

        <?php if (count($films) == 0): ?>
            <p style="grid-column:1/-1; text-align:center; color:#6b7280; font-size:18px; padding:30px;">
                Film '<?= esc($search) ?>' tidak ditemukan.
            </p>
        <?php else: ?>
            <?php foreach ($films as $f): ?>
                <div class="film-card" onclick="location.href='<?= BASE_URL ?>/film_detail.php?id=<?= $f['id'] ?>'">

                    <img src="<?= BASE_URL ?>/assets/uploads/<?= esc($f['poster']) ?>" 
                         class="poster"
                         alt="<?= esc($f['title']) ?>">

                    <div class="film-body">
                        <div class="film-title"><?= esc($f['title']) ?></div>
                        <div class="film-sub">
                            <?= esc($f['genre']) ?> • <?= esc($f['duration']) ?> menit
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>


<!-- ==================== JS SEARCH ==================== -->
<script>
const input = document.getElementById("searchInput");
const clearBtn = document.getElementById("clearSearch");

function toggleClear() {
    clearBtn.style.display = input.value.length > 0 ? "block" : "none";
}

toggleClear();

input.addEventListener("input", toggleClear);

clearBtn.addEventListener("click", () => {
    input.value = "";
    clearBtn.style.display = "none";
    input.form.submit();
});
</script>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
