<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/auth.php';

$stmt = $pdo->query("SELECT * FROM films ORDER BY created_at DESC");
$films = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../src/templates/header.php'; ?>

<style>
/* =========================================================
   GLOBAL STYLES
   ========================================================= */
*,
*::before,
*::after {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    background: #ffffff;
    color: #111827;
}

main {
    max-width: 100%;
    margin: 0;
    padding: 0;
}

/* =========================================================
   HERO SLIDER + SHADOW
   ========================================================= */
.hero-wrapper {
    margin-top: 120px !important; /* memberi jarak dari navbar */
}

.hero-slider {
    position: relative;
    width: 100%;
    height: 420px;
    overflow: hidden;
    border-radius: 0;

    /* Soft Shadow Above Slider */
    box-shadow: 0px -26px 45px rgba(0, 0, 0, 0.14);
}

.hero-slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
}

.hero-slide.active {
    opacity: 1;
}

/* Overlay text */
.hero-overlay {
    position: absolute;
    inset: 0;
    padding: 32px 64px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: #ffffff;
    text-shadow: 0 4px 12px rgba(0,0,0,0.7);
}

.hero-tag {
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.hero-title {
    font-size: 48px;
    font-weight: 900;
    line-height: 1.05;
}
.hero-title span {
    color: #ef4444;
}

.hero-sub {
    margin-top: 18px;
    font-size: 16px;
    max-width: 420px;
}

.hero-cta {
    margin-top: 22px;
}

.hero-btn {
    display: inline-block;
    padding: 12px 28px;
    border-radius: 999px;
    background: #16a34a;
    color: #ffffff;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    box-shadow: 0 8px 24px rgba(22,163,74,0.5);
}

.hero-btn:hover {
    background: #15803d;
}

/* Slider navigation */
.hero-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: rgba(15,23,42,0.6);
    color: #ffffff;
    width: 32px;
    height: 32px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 18px;
    z-index: 20;
}

.hero-nav.prev { left: 12px; }
.hero-nav.next { right: 12px; }

/* Responsive slider */
@media (max-width: 768px) {
    .hero-slider {
        height: 320px;
    }

    .hero-overlay {
        padding: 20px;
    }

    .hero-title {
        font-size: 34px;
    }
}

/* =========================================================
   MOVIE SELECTION SECTION
   ========================================================= */
.section-heading {
    text-align: center;
    font-size: 30px;
    font-weight: 800;
    margin: 50px 0 8px;
    letter-spacing: 0.12em;
    color: #0f172a;
}

.section-line {
    width: 85%;
    max-width: 800px;
    margin: 0 auto 30px;
    border-top: 2px solid #11182710;
}

/* Grid */
.movies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 28px;
    margin-bottom: 50px;
    padding: 0 80px;
}

/* Card Film */
.movie-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    cursor: pointer;
    transition: 0.25s ease;
    background: #fff;
}

.movie-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 34px rgba(0,0,0,0.18);
}

.movie-card img {
    width: 100%;
    display: block;
    aspect-ratio: 2/3;
    object-fit: cover;
}

/* Badge nomor */
.movie-number {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ef4444;
    color: #ffffff;
    font-weight: 800;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 14px;
}

/* Hover overlay */
.movie-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.55);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 12px;
    opacity: 0;
    transition: 0.3s ease;
}

.movie-card:hover .movie-overlay {
    opacity: 1;
}

.movie-overlay button,
.movie-overlay a {
    width: 70%;
    max-width: 200px;
    padding: 10px 0;
    border-radius: 999px;
    border: 2px solid #ffffff;
    background: transparent;
    color: #ffffff;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
}

.movie-overlay a.get-ticket {
    background: #ef4444;
    border-color: #ef4444;
}

/* Responsive movie grid */
@media (max-width: 600px) {
    .movies-grid {
        padding: 0 20px;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
}

</style>

<main>
    <!-- HERO SLIDER -->
    <section class="hero-wrapper">
        <div class="hero-slider" id="heroSlider">

            <div class="hero-slide active" style="background-image:url('<?= BASE_URL ?>/assets/uploads/tes.jpg');">
                <div class="hero-overlay">
                    <div class="hero-tag">Advance Ticket Sales</div>
                    <div class="hero-title">
                        BUY 1 GET 1<br><span>FREE TICKET</span>
                    </div>
                    <div class="hero-sub">
                        Mulai pembelian tanggal 25 November 2025
                        untuk penayangan 27 November 2025.
                    </div>
                    <div class="hero-cta">
                        <a href="#movie-selection" class="hero-btn">Beli di sini!</a>
                    </div>
                </div>
            </div>

            <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/assets/hero/slide2.jpg');">
                <div class="hero-overlay">
                    <div class="hero-tag">New Release</div>
                    <div class="hero-title">
                        MIDNIGHT<br><span>MYSTERY</span>
                    </div>
                    <div class="hero-sub">
                        Rasakan pengalaman menegangkan di malam hari
                        dengan format layar terbaik di kota kamu.
                    </div>
                    <div class="hero-cta">
                        <a href="#movie-selection" class="hero-btn">Lihat jadwal</a>
                    </div>
                </div>
            </div>

            <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/assets/hero/slide3.jpg');">
                <div class="hero-overlay">
                    <div class="hero-tag">Family Time</div>
                    <div class="hero-title">
                        WEEKEND<br><span>WITH FAMILY</span>
                    </div>
                    <div class="hero-sub">
                        Paket spesial tiket keluarga untuk akhir pekan.
                        Pilih film favorit dan pesan sekarang.
                    </div>
                    <div class="hero-cta">
                        <a href="#movie-selection" class="hero-btn">Pesan tiket</a>
                    </div>
                </div>
            </div>

            <button class="hero-nav prev" id="heroPrev">‹</button>
            <button class="hero-nav next" id="heroNext">›</button>
        </div>
    </section>

    <!-- MOVIE SELECTION -->
    <section id="movie-selection">
        <h2 class="section-heading">MOVIE SELECTION</h2>
        <div class="section-line"></div>

        <div class="movies-grid">
            <?php $no = 1; foreach ($films as $f): ?>
                <article class="movie-card">
                    <div class="movie-number"><?= $no++; ?></div>
                    <img src="<?= BASE_URL . '/' . esc($f['poster']) ?>" alt="<?= esc($f['title']) ?>">

                    <div class="movie-overlay">
                        <button type="button">Watch Trailer</button>

                        <a class="get-ticket"
                           href="<?= BASE_URL ?>/film_detail.php?id=<?= $f['id'] ?>">
                            Get Ticket
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<script>
// ====== HERO SLIDER JS ======
const slides = document.querySelectorAll('.hero-slide');
let heroIndex = 0;
const prevBtn = document.getElementById('heroPrev');
const nextBtn = document.getElementById('heroNext');

function showSlide(idx) {
    slides[heroIndex].classList.remove('active');
    heroIndex = (idx + slides.length) % slides.length;
    slides[heroIndex].classList.add('active');
}

prevBtn.addEventListener('click', () => showSlide(heroIndex - 1));
nextBtn.addEventListener('click', () => showSlide(heroIndex + 1));

setInterval(() => {
    showSlide(heroIndex + 1);
}, 5000);
</script>

<?php include __DIR__ . '/../src/templates/footer.php'; ?>
