<?php
?>
<style>
.footer {
    width: 100%;
    background: #0a1d33;
    color: #e5eaf0;
    padding: 15px 0 15px;
    font-family: "Poppins", sans-serif;
    margin-top: 40px;
}
.footer-bottom {
    text-align: center;
    padding-top: 10px;
    font-size: 13px;
    color: #b5c0cc;
}
</style>

<footer class="footer">
    <div class="footer-bottom">
        © <?= date("Y") ?> Bioskop App
    </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js">
document.querySelectorAll('.footer-social i').forEach(icon => {
    icon.addEventListener('click', () => {
        alert("Fitur sosial media belum diaktifkan.");
    });
});
</script>

