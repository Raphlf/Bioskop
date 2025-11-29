<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

/* Ambil semua film */
$stmt = $pdo->query("SELECT * FROM films ORDER BY created_at DESC");
$films = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>

/* ======================
   GLOBAL
====================== */
body {
    margin: 0;
    font-family: "Inter", system-ui, sans-serif;
    background: #ffffff;
}

/* ======================
   HERO SLIDER
====================== */

.hero-wrapper { margin-top: 120px; }

.hero-slider {
    position: relative;
    width: 100%;
    height: 420px;
    overflow: hidden;
    box-shadow: 0px -26px 45px rgba(0,0,0,0.15);
}

.hero-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity .7s ease;
}

.hero-slide.active { opacity: 1; }

.hero-overlay {
    position: absolute;
    inset: 0;
    padding: 40px 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: white;
    text-shadow: 0 4px 12px rgba(0,0,0,0.6);
}

.hero-btn {
    margin-top: 20px;
    padding: 12px 26px;
    background: #16a34a;
    color: #fff;
    border-radius: 999px;
    font-weight: 700;
    width: fit-content;
    box-shadow: 0 6px 20px rgba(22,163,74,0.4);
    transition: .25s;
}

.hero-btn:hover {
    background: #128a3f;
    transform: translateY(-3px);
}

.hero-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    border: none;
    background: rgba(0,0,0,0.45);
    color: white;
    border-radius: 999px;
    cursor: pointer;
    z-index: 20;
}
.hero-nav.prev { left: 14px; }
.hero-nav.next { right: 14px; }

/* ======================
   MOVIE SECTION
====================== */

.section-heading {
    margin-top: 50px;
    text-align: center;
    font-size: 32px;
    font-weight: 800;
    letter-spacing: .08em;
}

.section-line {
    width: 85%;
    max-width: 850px;
    margin: 12px auto 40px;
    border-top: 3px solid #ddd; /* agak tebal */
}

.movies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 18px;
    padding: 0 40px 60px;
    justify-items: center;
}

.movie-card {
    width: 220px; /* ukuran kecil ala film.php */
    background: white;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    transition: .25s ease;
}

.movie-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 32px rgba(0,0,0,.12);
}

.movie-card img {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
}

/* badge nomor */
.movie-number {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ef4444;
    padding: 4px 10px;
    color: white;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
}

/* overlay */
.movie-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    opacity: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    justify-content: center;
    align-items: center;
    transition: .25s ease;
}

.movie-card:hover .movie-overlay {
    opacity: 1;
}

/* tombol emas */
.overlay-btn {
    padding: 10px 22px;
    background: #d4af37;
    border-radius: 999px;
    color: white;
    font-weight: 700;
    border: none;
    font-size: 14px;
    cursor: pointer;
    transition: .2s ease;
}

.overlay-btn:hover {
    background: #b8932e;
    transform: translateY(-2px);
}

</style>

<main>

<!-- ==========================
     HERO SLIDER
========================== -->
<section class="hero-wrapper">
    <div class="hero-slider" id="heroSlider">

        <!-- slide 1 -->
        <div class="hero-slide active" style="background-image:url('<?= BASE_URL ?>/assets/uploads/tes.jpg');">
            <div class="hero-overlay">
                <h1>BUY 1 GET 1 FREE</h1>
                <p>Promo terbatas sampai akhir bulan.</p>
                <a href="#movie-selection" class="hero-btn">Cek Tiket</a>
            </div>
        </div>

        <!-- slide 2 -->
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/assets/uploads/studio.jpg');">
            <div class="hero-overlay">
                <h1>STUDIO PREMIERE</h1>
                <p>Film terbaru dengan kualitas terbaik.</p>
                <a href="#movie-selection" class="hero-btn">Lihat Film</a>
            </div>
        </div>

        <!-- slide 3 -->
        <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/assets/uploads/kasir1.jpg');">
            <div class="hero-overlay">
                <h1>WEEKEND FUN</h1>
                <p>Promo nonton bareng keluarga!</p>
                <a href="#movie-selection" class="hero-btn">Pesan Tiket</a>
            </div>
        </div>

        <button class="hero-nav prev" id="heroPrev">‹</button>
        <button class="hero-nav next" id="heroNext">›</button>
    </div>
</section>

<!-- ==========================
     MOVIE LIST
========================== -->
<section id="movie-selection">

    <h2 class="section-heading">MOVIE SELECTION</h2>
    <div class="section-line"></div>

    <div class="movies-grid">
        <?php $no = 1; foreach ($films as $f): ?>
        <div class="movie-card">

            <div class="movie-number"><?= $no++; ?></div>

            <img src="<?= BASE_URL ?>/assets/uploads/<?= esc($f['poster']) ?>" 
                 alt="<?= esc($f['title']) ?>">

            <div class="movie-overlay">
                <a href="https://youtube.com" target="_blank" class="overlay-btn">
                    Watch Trailer
                </a>
                <a href="<?= BASE_URL ?>/film_detail.php?id=<?= $f['id'] ?>" 
                   class="overlay-btn">
                    Get Ticket
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</section>

</main>

<script>
// Slider JS
const slides = document.querySelectorAll('.hero-slide');
let idx = 0;

function show(i) {
    slides[idx].classList.remove('active');
    idx = (i + slides.length) % slides.length;
    slides[idx].classList.add('active');
}

document.getElementById("heroPrev").onclick = () => show(idx - 1);
document.getElementById("heroNext").onclick = () => show(idx + 1);

setInterval(() => show(idx + 1), 5000);
</script>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
