<?php
/**
 * NightFest - Footer Component (Clases Aisladas CJ)
 */
?>
<footer class="cj-f-main">
    <div class="cj-f-wrap">
        <div class="cj-f-networks">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
        </div>
        <div class="cj-f-links">
            <a href="#">Términos y Condiciones</a>
            <span class="cj-f-sep">|</span>
            <a href="#">Ayuda</a>
        </div>
        <p class="cj-f-copy">© <?php echo date('Y'); ?> NightFest. Johan & Carolina.</p>
    </div>
</footer>

<link rel="stylesheet" href="../assets/css/footer.css">

<div id="cookie-banner">
    <span>NightFest utiliza cookies para mejorar tu experiencia. Debes aceptarlas para poder iniciar sesión o registrarte.</span>
    <button id="accept-cookies">ACEPTAR</button>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const banner = document.getElementById("cookie-banner");
    const acceptBtn = document.getElementById("accept-cookies");
    const submitButtons = document.querySelectorAll(".btn-login-submit");
    const loginLinks = document.querySelectorAll(".cj-h-link-login, .btn-login, .btn-register");

    function checkCookies() {
        if (localStorage.getItem("cookiesAccepted") === "true") {
            if (banner) banner.style.setProperty("display", "none", "important");
            submitButtons.forEach(btn => {
                btn.removeAttribute("disabled");
                btn.style.opacity = "1";
                btn.style.cursor = "pointer";
            });
        } else {
            if (banner) banner.style.setProperty("display", "flex", "important");
            submitButtons.forEach(btn => {
                btn.setAttribute("disabled", "true");
                btn.style.opacity = "0.5";
                btn.style.cursor = "not-allowed";
                btn.title = "Debes aceptar las cookies para poder continuar.";
            });
            // Prevenir navegación a login/registro desde el header
            loginLinks.forEach(link => {
                link.addEventListener("click", function(e) {
                    if (localStorage.getItem("cookiesAccepted") !== "true") {
                        e.preventDefault();
                        if (banner) {
                            banner.style.transform = "translateX(-50%) scale(1.05)";
                            setTimeout(() => {
                                banner.style.transform = "translateX(-50%) scale(1)";
                            }, 200);
                        }
                    }
                });
            });
        }
    }

    if (acceptBtn) {
        acceptBtn.addEventListener("click", function() {
            localStorage.setItem("cookiesAccepted", "true");
            checkCookies();
        });
    }

    checkCookies();
});
</script>