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