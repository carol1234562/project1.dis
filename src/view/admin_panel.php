<?php
require_once '../Model/seguridad.php';
require_once '../Controller/EventController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Muro de seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio1.php");
    exit();
}

$ec = new EventController();
$eventos_todos = $ec->getAllEvents();
$total_eventos = count($eventos_todos);
$proximos_eventos = $ec->getTotalEventsCount();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración | NightFest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/perfil.css">
    <link rel="stylesheet" href="../assets/css/admin_panel.css">
</head>
<body class="pf-body">

    <?php include 'header.php'; ?>

    <main class="admin-panel-container">
        <div class="return-container" onclick="window.location.href='perfil.php'" style="margin-bottom: 30px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; color: #D4AF37; font-weight: 600;">
            <i class="fas fa-angle-left"></i>
            <span>Volver al Perfil</span>
        </div>

        <h2 class="section-title" style="text-align: left; margin-bottom: 30px;">PANEL DE ADMINISTRACIÓN</h2>

        <!-- Sección de Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_eventos; ?></div>
                <div class="stat-label">Total de Eventos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $proximos_eventos; ?></div>
                <div class="stat-label">Eventos Próximos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">Admin</div>
                <div class="stat-label">Tu Rol de Acceso</div>
            </div>
        </div>

        <!-- Sección de Accesos y Acciones -->
        <div class="admin-options-grid">
            <div class="option-card" onclick="window.location.href='mis_eventos.php'">
                <i class="fas fa-calendar-alt option-icon"></i>
                <h3 class="option-title">Administrar Eventos</h3>
                <p class="option-desc">Ver la lista completa de eventos, editarlos, actualizarlos o eliminarlos de forma permanente.</p>
            </div>

            <div class="option-card" onclick="window.location.href='crear_evento.php'">
                <i class="fas fa-calendar-plus option-icon"></i>
                <h3 class="option-title">Crear Nuevo Evento</h3>
                <p class="option-desc">Lanzar y publicar una nueva sesión o concierto definiendo artista, recinto, fecha e imagen de portada.</p>
            </div>
            
            <div class="option-card" onclick="window.location.href='perfil.php'">
                <i class="fas fa-user-cog option-icon"></i>
                <h3 class="option-title">Ver Mi Perfil</h3>
                <p class="option-desc">Ir a la configuración de cuenta, cambiar foto de perfil o gestionar los ajustes de seguridad.</p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>
