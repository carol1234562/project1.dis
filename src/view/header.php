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
?><?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/responsive.css">

<header class="cj-h-main">
    <div class="cj-h-logo">
        <a href="inicio1.php">
            <img src="../assets/img/logo.png" alt="NightFest Logo">
        </a>
    </div>

    <nav class="cj-h-nav">
        <ul>
            <li><a href="inicio1.php" class="<?= $current_page === 'inicio1.php' ? 'active' : '' ?>">HOME</a></li>
            <li><a href="destacados_page.php" class="<?= $current_page === 'destacados_page.php' ? 'active' : '' ?>">DESTACADOS</a></li>
            <li><a href="<?= $is_logged ? 'discotecas.php' : 'login.php' ?>" class="<?= in_array($current_page, ['discotecas.php', 'infoevento.php', 'mis_publicaciones.php']) ? 'active' : '' ?>">DISCOTECAS</a></li>
            <li><a href="<?= $is_logged ? 'bares.php' : 'login.php' ?>" class="<?= $current_page === 'bares.php' ? 'active' : '' ?>">BARES</a></li>
            <li><a href="<?= $is_logged ? 'festivales.php' : 'login.php' ?>" class="<?= $current_page === 'festivales.php' ? 'active' : '' ?>">FESTIVALES</a></li>
            <li><a href="<?= $is_logged ? 'restaurantes.php' : 'login.php' ?>" class="<?= $current_page === 'restaurantes.php' ? 'active' : '' ?>">RESTAURANTES</a></li>
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