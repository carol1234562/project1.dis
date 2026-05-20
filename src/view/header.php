<?php
// 1. Activamos el búfer para que PHP guarde el HTML de forma segura en memoria
ob_start();

// 2. Control seguro de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. Destrucción de la caché del navegador (Previene el fallo al volver atrás)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");;

// Identificación de usuario 
$is_logged = isset($_SESSION['user_id']);
$es_admin = ($is_logged && ($_SESSION['rol'] ?? '') === 'admin');

// Lógica de destino para secciones privadas
$destino_privado = $is_logged ? "reservar.php" : "login.php";

// Sincronización del Avatar
$foto_perfil = $_SESSION['user_photo'] ?? 'default.png';
$inicial = "U";
if ($is_logged && isset($_SESSION['user_name'])) {
    $inicial = strtoupper(substr($_SESSION['user_name'], 0, 1));
}
?>

<style>
/* =========================================================================
   NIGHTFEST - CSS COMPLETO DEL HEADER (CLASES AISLADAS CJ)
   ========================================================================= */

.cj-h-main {
    background-color: #000000 !important; /* Fondo negro puro */
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 20px 40px !important;
    height: 80px !important;
    box-sizing: border-box !important;
    width: 100% !important;
    position: relative !important;
    z-index: 9999 !important; /* Por encima de mapas o sliders */
    border-bottom: 4px solid #D4AF37 !important; /* Línea dorada */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.8), 0 2px 8px rgba(225, 177, 44, 0.4) !important;
}

/* LOGO */
.cj-h-logo a img {
    height: 120px !important;
    object-fit: contain !important;
    display: block !important;
}

/* MENÚ DE NAVEGACIÓN */
.cj-h-nav ul {
    display: flex !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
    gap: 25px !important;
    align-items: center !important;
}

.cj-h-nav ul li a {
    color: #ffffff !important;
    text-decoration: none !important;
    font-family: 'Montserrat', sans-serif !important;
    font-weight: bold !important;
    font-size: 16px !important;
    text-transform: uppercase !important;
    transition: color 0.2s ease-in-out !important; 
    letter-spacing: 0.5px !important;
}

/* Efectos de enlaces */
.cj-h-nav ul li a:hover,
.cj-h-nav ul li a.active {
    color: #D4AF37 !important; 
}

/* CONTROLES DE USUARIO (AUTH) */
.cj-h-controls {
    display: flex !important;
    align-items: center !important;
    gap: 20px !important;
    margin-right: 20px !important;
}

.cj-h-controls a {
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
}

/* Enlace Iniciar Sesión */
.cj-h-controls a.cj-h-link-login {
    color: #ffffff !important;
    text-decoration: none !important;
    font-weight: bold !important;
    font-size: 16px !important;
    transition: color 0.2s ease-in-out !important;
}

.cj-h-controls a.cj-h-link-login:hover {
    color: #D4AF37 !important;
}

/* Botón Mis Eventos / Registrarse */
.cj-h-btn-gold {
    background-color: #D4AF37 !important;
    color: #000000 !important;
    padding: 8px 18px !important;
    border-radius: 6px !important;
    text-decoration: none !important;
    font-weight: bold !important;
    font-size: 14px !important;
    transition: background-color 0.2s ease !important;
}

.cj-h-btn-gold:hover {
    background-color: #c79a1e !important;
}

/* AVATAR DE USUARIO */
.cj-h-avatar-wrap {
    width: 42px !important;
    height: 42px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: transform 0.2s ease !important;
}

.cj-h-avatar-wrap:hover {
    transform: scale(1.1) !important;
}

.cj-h-avatar-img {
    width: 100% !important;
    height: 100% !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 2px solid #D4AF37 !important;
    box-shadow: 0 0 8px rgba(212, 175, 55, 0.4) !important;
}

/* Inicial de reserva */
.cj-h-avatar-fallback {
    width: 100% !important;
    height: 100% !important;
    border-radius: 50% !important;
    background-color: #D4AF37 !important;
    color: #000000 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: bold !important;
    font-size: 16px !important;
    text-transform: uppercase !important;
    box-shadow: 0 0 8px rgba(212, 175, 55, 0.3) !important;
}

/* ICONOS DE ACCIÓN RAPIDA */
.cj-h-icon-add, 
.cj-h-icon-logout {
    color: #ffffff !important;
    font-size: 18px !important;
    cursor: pointer !important;
    transition: color 0.2s ease, transform 0.2s ease !important;
}

.cj-h-icon-logout {
    font-size: 20px !important;
    padding: 5px !important;
}

.cj-h-icon-add:hover {
    color: #4cd137 !important;
    transform: scale(1.15) !important;
}

.cj-h-icon-logout:hover {
    color: #e84118 !important;
    transform: scale(1.15) !important;
}
</style>

<header class="cj-h-main">
    <div class="cj-h-logo">
        <a href="inicio1.php">
            <img src="../assets/img/logo.png" alt="NightFest Logo">
        </a>
    </div>

    <nav class="cj-h-nav">
        <ul>
            <li><a href="inicio1.php" class="active">HOME</a></li>
            <li><a href="destacados_page.php">DESTACADOS</a></li>
            <li><a href="<?= $is_logged ? 'discotecas.php' : 'login.php' ?>">DISCOTECAS</a></li>
            <li><a href="<?= $is_logged ? 'bares.php' : 'login.php' ?>">BARES</a></li>
            <li><a href="<?= $is_logged ? 'festivales.php' : 'login.php' ?>">FESTIVALES</a></li>
            <li><a href="<?= $is_logged ? 'restaurantes.php' : 'login.php' ?>">RESTAURANTES</a></li>
            <?php if ($es_admin): ?>
                <li><a href="mis_eventos.php" class="cj-h-btn-gold">MIS EVENTOS</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="cj-h-controls">
        <?php if ($is_logged): ?>
            
            <a href="perfil.php" title="Mi Perfil">
                <div class="cj-h-avatar-wrap">
                    <?php 
                    if (isset($_SESSION['user_photo']) && !empty($_SESSION['user_photo']) && $_SESSION['user_photo'] !== 'default.png'): 
                        $archivo_foto = trim($_SESSION['user_photo']);
                    ?>
                    <?php
                    // Comprobamos en que carpeta esta la foto
                    $ruta_foto = file_exists("../assets/img/uploads/" . $archivo_foto) 
                        ? "../assets/img/uploads/" . $archivo_foto 
                        : "../assets/img/" . $archivo_foto;
                    ?>
                    <img src="<?= htmlspecialchars($ruta_foto, ENT_QUOTES, 'UTF-8') ?>"
                    alt="Perfil" 
                    class="cj-h-avatar-img"
                    onerror="this.style.display='none';">
                        
                    <?php else: ?>
                        <div class="cj-h-avatar-fallback"><?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
            </a>
            
            <?php if ($es_admin): ?>
                <a href="crear_evento.php" title="Crear Evento" style="margin-left: 10px;">
                    <i class="fas fa-plus cj-h-icon-add"></i>
                </a>
            <?php endif; ?>
            
            <form action="../Controller/UserController.php?action=logout" method="POST" onsubmit="localStorage.removeItem('welcomeShown');" style="display: inline-flex; align-items: center; margin-left: 10px;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" title="Cerrar Sesión" style="background: none; border: none; padding: 0; cursor: pointer; color: inherit; display: inline-flex; align-items: center;">
                    <i class="fas fa-sign-out-alt cj-h-icon-logout"></i>
                </button>
            </form>

        <?php else: ?>
            <a href="login.php" class="cj-h-link-login" style="margin-right: 15px;">Iniciar Sesión</a>
            <a href="registro_estandar.php" class="cj-h-btn-gold">Registrarse</a>
        <?php endif; ?>
    </div>
</header>
<?php ob_end_flush(); ?>

<script>
// 🌟 ROMPER EL BFCACHE (BACK-FORWARD CACHE) DEL NAVEGADOR
window.addEventListener('pageshow', function (event) {
    // Si event.persisted es true, significa que el navegador intentó meter la página congelada desde su memoria interna
    if (event.persisted || (typeof window.performance !== "undefined" && window.performance.navigation.type === 2)) {
        
        console.log("Detectado intento de volver atrás. Forzando recarga segura...");
        
        // Obliga a la página a descargarse limpiamente desde el servidor de nuevo
        window.location.reload(true);
    }
});
</script>