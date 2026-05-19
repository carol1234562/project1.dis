<?php
require_once '../Model/seguridad.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Muro de seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio1.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Evento | NightFest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/registro_admin.css">
    <link rel="stylesheet" href="../assets/css/STYLE1.css">
    <link rel="stylesheet" href="../assets/css/crear_evento.css">
</head>
<body class="login-page">

    <?php include 'header.php'; ?>

    <div class="login-screen" style="height: auto; padding: 40px 0;">
        <div class="return-container" onclick="window.location.href='discotecas.php'" style="margin-left: 5%; cursor: pointer;">
            <i class="fas fa-angle-left"></i>
            <span>Volver a Eventos</span>
        </div>

        <div class="form-container-custom">
            <h2 class="form-title">Crear Nuevo Evento</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-msg" style="color: #ff4d4d; margin-bottom: 20px; text-align: center;">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </p>
            <?php endif; ?>

            <form action="../Controller/EventController.php?action=create" method="POST" enctype="multipart/form-data">
                <div class="input-group-custom">
                    <label>Nombre del Artista / Evento</label>
                    <input type="text" name="artista" placeholder="Ej. Bizarrap Live Session" required>
                </div>

                <div class="input-group-custom">
                    <label>Fecha del Evento</label>
                    <input type="date" name="fecha_evento" required>
                </div>

                <div class="input-group-custom">
                    <label>Hora de Apertura</label>
                    <input type="time" name="hora" required>
                </div>

                <div class="input-group-custom">
                    <label>Localidad (Ciudad)</label>
                    <input type="text" name="localidad" placeholder="Ej. Barcelona" required>
                </div>

                <div class="input-group-custom">
                    <label>Ubicación (Lugar / Recinto)</label>
                    <input type="text" name="ubicacion" placeholder="Ej. Palau Sant Jordi" required>
                </div>

                <div class="input-group-custom">
                    <label>Estado del Evento</label>
                    <select name="estado">
                        <option value="DISPONIBLE">DISPONIBLE</option>
                        <option value="AGOTADO">AGOTADO</option>
                        <option value="PRÓXIMAMENTE">PRÓXIMAMENTE</option>
                    </select>
                </div>

                <div class="input-group-custom">
                    <label>Imagen de Portada (Opcional)</label>
                    <input type="file" name="imagen" accept="image/*">
                </div>

                <button type="submit" class="submit-btn-custom">Guardar Evento</button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
