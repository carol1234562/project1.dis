<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Si ya hay una sesión activa, lo mandamos al inicio (esto está perfecto)
if (isset($_SESSION['user_id'])) {
    header("Location: inicio1.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NightFest</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/login.css">
    <script>
    // Aplicar tema guardado inmediatamente antes de renderizar para evitar destellos
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (savedTheme === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    })();
    </script>
</head>

<body class="login-page">

    <div class="login-screen">
        <div class="return-container" onclick="window.location.href='inicio1.php'">
            <i class="fas fa-angle-left"></i>
            <span>Volver</span>
        </div>

        <form action="../Controller/UserController.php" method="POST" class="login-form-container">
            
            <img src="../assets/img/logo.png" class="logo-login" alt="Logo NightFest">

            <?php if (isset($_GET['error'])): ?>
                <p class="error-msg" style="color: #ff4d4d; text-align: center; margin-bottom: 15px; font-weight: 600; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 5px;">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </p>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <p class="success-msg" style="color: #2ecc71; text-align: center; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </p>
            <?php endif; ?>

            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <a href="recuperar.php" class="forgot-link">¿Olvidaste tu contraseña?</a>
            </div>

            <p class="texto-cuenta">
                ¿No tienes cuenta? <a href="registro_admin.php" class="link-registro">Regístrate</a>
            </p>

            <input type="submit" name= "login" value="Iniciar sesión" class="btn-login-submit">
        </form>
    </div>
         <?php include 'footer.php'; ?>

</body>
</html>