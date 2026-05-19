<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: inicio1.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Cliente | NightFest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/registro_estandar.css">

</head>
<body class="login-page">

    <div class="login-screen">
        <div class="return-container" onclick="window.location.href='inicio1.php'">
            <i class="fas fa-angle-left"></i>
            <span>Volver</span>
        </div>

        <form action="../Controller/UserController.php?action=register" method="POST" class="login-form-container">

            <input type="hidden" name="rol" value="estandar">

            <img src="../assets/img/logo.png" class="logo-login" alt="Logo NightFest">

            <h2 style="text-align:center; color:#D4AF37; margin-bottom:25px; font-size:1rem; letter-spacing:2px; text-transform:uppercase;">Registro Estándar</h2>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </p>
            <?php endif; ?>

            <div class="input-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" placeholder="Tu nombre completo" required
                    style="background:#111; border:1px solid #333; padding:14px; color:#fff; border-radius:6px; font-family:'Montserrat',sans-serif; font-size:0.95rem;">
            </div>

            <div class="input-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" placeholder="ejemplo@correo.com" required>
            </div>

            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required minlength="6">
            </div>

            <p class="texto-cuenta">
                ¿Eres administrador? <a href="registro_admin.php" class="link-registro">Registrate como administrador</a>
            </p>
            <p class="texto-cuenta">
                ¿Ya tienes cuenta? <a href="login.php" class="link-registro">Iniciar sesión</a>
            </p>

            <input type="submit" value="Crear Cuenta" class="btn-login-submit">
        </form>
    </div>
        <?php include 'footer.php'; ?>


</body>
</html>