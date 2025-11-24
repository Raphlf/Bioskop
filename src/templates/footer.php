<style>
.footer {
    width: 100%;
    background: #0a1d33;
    color: #e5eaf0;
    padding: 20px 0;
    font-family: "Poppins", sans-serif;
    margin-top: 60px;
}

.footer-bottom {
    text-align: center;
    font-size: 13px;
    color: #b5c0cc;
}
</style>

<footer class="footer">
    <div class="footer-bottom">
        © <?= date("Y") ?> Bioskop App - All rights reserved
    </div>
</footer>

<script>
document.querySelectorAll('.footer-social i').forEach(icon => {
    icon.addEventListener('click', () => {
        alert("Fitur sosial media belum diaktifkan.");
    });
});
</script>
