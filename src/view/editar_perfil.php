<?php
require_once '../Controller/UserController.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Si no hay sesión lo mandamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uc   = new UserController();
$user = $uc->getUserData($_SESSION['user_id']);

include_once 'header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil | NightFest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/registro_estandar.css">
</head>

<body class="login-page">
    <div class="login-screen">
        <form action="../Controller/UserController.php?action=actualizarPerfil" method="POST" enctype="multipart/form-data" class="login-form-container">

            <h2 style="text-align:center; color:#D4AF37; margin-bottom:25px; font-size:1rem; letter-spacing:2px; text-transform:uppercase;">Editar Perfil</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </p>
            <?php endif; ?>

            <!-- Muestra el nombre para modificar -->
            <div class="input-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
            </div>

            <!-- Solo muestra el email que tiene pero no se puede cambiar -->
            <div class="input-group">
                <label>Correo Electrónico</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                    style="opacity:0.5; cursor:not-allowed;">
            </div>

            <!-- Cambiar contraseña si se quiere -->
            <div class="input-group">
                <label>Nueva Contraseña (opcional)</label>
                <input type="password" name="nueva_password" placeholder="••••••••" minlength="6">
            </div>

            <!-- Cambiar la foto solo es para administradores -->
            <?php if ($user['rol'] === 'admin'): ?>
            <div class="input-group">
                <label>Foto de Perfil</label>
                <input type="file" name="foto" accept="image/*">
            </div>
            <?php endif; ?>

            <input type="submit" value="Guardar Cambios" class="btn-login-submit">
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>