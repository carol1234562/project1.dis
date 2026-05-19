<?php
/**
 * NightFest - Footer Component (Clases Aisladas CJ)
 */
?>
<style>
/* Estilos con el nuevo prefijo exclusivo para evitar cualquier conflicto */
.cj-f-main {
    background-color: var(--negro-suave);
    border-top: 1px solid #222222;
    padding: 50px 5% 30px;
    text-align: center;
    color: #666;
    width: 100%;
    margin-top: 50px;
}

.cj-f-wrap {
    max-width: 1200px;
    margin: 0 auto;
}

.cj-f-networks {
    margin-bottom: 25px;
}

.cj-f-networks a {
    color: var(--oro-premium);
    font-size: 1.5rem;
    margin: 0 15px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.cj-f-networks a:hover {
    color: var(--white);
    transform: translateY(-3px);
    text-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
}

.cj-f-links {
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.cj-f-links a {
    color: #888;
    text-decoration: none;
    transition: color 0.3s ease;
}

.cj-f-links a:hover {
    color: var(--oro-premium);
}

.cj-f-links .cj-f-sep {
    margin: 0 10px;
    color: #333;
}

.cj-f-copy {
    font-size: 0.8rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #444;
    margin-top: 20px;
}
</style>

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

<style>
#cookie-banner {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: 90%;
    max-width: 600px;
    background-color: #1a1a1a;
    border: 1px solid #D4AF37;
    padding: 20px;
    border-radius: 12px;
    z-index: 10001;
    display: none;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.9);
    text-align: center;
    transition: transform 0.2s ease-in-out;
}

#cookie-banner p, #cookie-banner span {
    color: #ffffff;
    font-size: 0.9rem;
    font-family: 'Montserrat', sans-serif;
    margin: 0;
    line-height: 1.4;
}

#accept-cookies {
    background-color: #D4AF37;
    color: #000000;
    border: none;
    padding: 10px 30px;
    font-weight: bold;
    text-transform: uppercase;
    border-radius: 5px;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    transition: background 0.3s ease, transform 0.2s ease;
}

#accept-cookies:hover {
    background-color: #b8952e;
    transform: scale(1.05);
}
</style>

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