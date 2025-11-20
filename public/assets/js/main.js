// Basic client-side validation for required inputs
document.addEventListener("submit", function(e) {
    const inputs = e.target.querySelectorAll("input[required], select[required], textarea[required]");
    for (let input of inputs) {
        if (input.value.trim() === "") {
            alert("Semua field wajib diisi!");
            e.preventDefault();
            return;
        }
    }
});
