<?php
require_once '../Model/seguridad.php';
require_once '../Controller/EventController.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Muro de seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio1.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ec = new EventController();
$evento = $ec->getEventById($id);

if (!$evento) {
    header("Location: discotecas.php?error=evento_no_encontrado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Evento | NightFest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/registro_admin.css">
    <link rel="stylesheet" href="../assets/css/STYLE1.css">
    <style>
        .form-container-custom {
            background: #111;
            border: 1px solid #D4AF37;
            padding: 40px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            margin: 50px auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8), 0 0 20px rgba(212, 175, 55, 0.1);
        }
        .form-title {
            text-align: center;
            color: #D4AF37;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 30px;
            font-size: 1.5rem;
        }
        .input-group-custom {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .input-group-custom label {
            color: #ccc;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .input-group-custom input, .input-group-custom select {
            background: #000;
            border: 1px solid #333;
            padding: 14px;
            color: #fff;
            border-radius: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .input-group-custom input:focus, .input-group-custom select:focus {
            border-color: #D4AF37;
        }
        .submit-btn-custom {
            background: #D4AF37;
            color: #000;
            font-weight: 700;
            border: none;
            padding: 15px;
            border-radius: 6px;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            width: 100%;
            font-family: 'Montserrat', sans-serif;
            margin-top: 10px;
        }
        .submit-btn-custom:hover {
            background: #b8952e;
            transform: translateY(-2px);
        }
        .current-image-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 8px;
            background: #1e1e1e;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #333;
        }
        .current-image-preview img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #D4AF37;
        }
    </style>
</head>
<body class="login-page">

    <?php include 'header.php'; ?>

    <div class="login-screen" style="height: auto; padding: 40px 0;">
        <div class="return-container" onclick="window.location.href='infoevento.php?id=<?= $evento['id'] ?>'" style="margin-left: 5%; cursor: pointer;">
            <i class="fas fa-angle-left"></i>
            <span>Volver a Detalles</span>
        </div>

        <div class="form-container-custom">
            <h2 class="form-title">Editar Evento</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-msg" style="color: #ff4d4d; margin-bottom: 20px; text-align: center;">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </p>
            <?php endif; ?>

            <form action="../Controller/EventController.php?action=update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $evento['id'] ?>">

                <div class="input-group-custom">
                    <label>Nombre del Artista / Evento</label>
                    <input type="text" name="artista" value="<?= htmlspecialchars($evento['artista']) ?>" placeholder="Ej. Bizarrap Live Session" required>
                </div>

                <div class="input-group-custom">
                    <label>Fecha del Evento</label>
                    <input type="date" name="fecha_evento" value="<?= htmlspecialchars($evento['fecha_evento']) ?>" required>
                </div>

                <div class="input-group-custom">
                    <label>Hora de Apertura</label>
                    <input type="time" name="hora" value="<?= htmlspecialchars($evento['hora']) ?>" required>
                </div>

                <div class="input-group-custom">
                    <label>Localidad (Ciudad)</label>
                    <input type="text" name="localidad" value="<?= htmlspecialchars($evento['localidad']) ?>" placeholder="Ej. Barcelona" required>
                </div>

                <div class="input-group-custom">
                    <label>Ubicación (Lugar / Recinto)</label>
                    <input type="text" name="ubicacion" value="<?= htmlspecialchars($evento['ubicacion']) ?>" placeholder="Ej. Palau Sant Jordi" required>
                </div>

                <div class="input-group-custom">
                    <label>Estado del Evento</label>
                    <select name="estado">
                        <option value="DISPONIBLE" <?= $evento['estado'] === 'DISPONIBLE' ? 'selected' : '' ?>>DISPONIBLE</option>
                        <option value="AGOTADO" <?= $evento['estado'] === 'AGOTADO' ? 'selected' : '' ?>>AGOTADO</option>
                        <option value="PRÓXIMAMENTE" <?= $evento['estado'] === 'PRÓXIMAMENTE' ? 'selected' : '' ?>>PRÓXIMAMENTE</option>
                    </select>
                </div>

                <div class="input-group-custom">
                    <label>Imagen de Portada (Dejar vacío para mantener la actual)</label>
                    <input type="file" name="imagen" accept="image/*">
                    <div class="current-image-preview">
                        <img src="../assets/img/<?= $evento['imagen'] ?>" alt="Portada Actual">
                        <span style="color: #ccc; font-size: 0.85rem;">Imagen actual: <?= htmlspecialchars($evento['imagen']) ?></span>
                    </div>
                </div>

                <button type="submit" class="submit-btn-custom">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
