<?php
// 1. Activamos el búfer para la página que invoque este archivo
ob_start();

// 2. Control seguro de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. Muro anti-caché total para el historial del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// 4. El filtro de acceso: si no está logueado, se va directo a inicio1.php
if (!isset($_SESSION['user_id'])) {
    // Usamos la ruta completa desde la raíz de tu proyecto en localhost
    header("Location: ../src/view/inicio1.php");
    exit();
}