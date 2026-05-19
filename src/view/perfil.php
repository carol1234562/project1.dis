<?php
require_once '../Model/segurity.php';
require_once '../Controller/UserController.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Seguridad: Evitar caché y verificar sesión
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache"); 
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uc = new UserController();
$user = $uc->getUserData($_SESSION['user_id']);

if (!$user) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION = array(); 
    session_unset();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: login.php");
    exit();
}
$foto_usuario = $user['foto_profile'] ?? $user['foto_perfil']; 
$carpeta_img = '../assets/img/';

// Variables de usuario
$nombre   = $user['nombre'];
$email    = $user['email'];
$rol      = $user['rol'];
$es_admin = ($rol === 'admin');

// Inicial para el avatar circular
$inicial  = strtoupper(substr(trim($nombre), 0, 1));

// CORRECCIÓN: Validar si existe el archivo físico en la carpeta
if (!empty($foto_usuario) && file_exists($carpeta_img . $foto_usuario)) {
    $foto_url = $carpeta_img . $foto_usuario;
} else {
    // Si en la BD dice 'default.png' pero tu archivo físico es 'default.jpg' (o viceversa)
    // Nos aseguramos de apuntar al archivo real que tienes en tu carpeta de assets
    $foto_url = $carpeta_img . 'default.jpg'; 
}

include_once 'header.php'; 

// 2. MURO DE SEGURIDAD: Si no hay sesión válida, lo expulsamos inmediatamente
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil | NightFest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/perfil.css">
</head>

<body class="pf-body">


    <main class="container pf-main-content">
        <h2 class="section-title">MI PERFIL</h2>

        <div class="pf-profile-card">
            <div class="pf-card-header">
                <div class="pf-avatar-container">
            <img src="<?php echo $foto_url; ?>" alt="Foto Perfil" class="pf-avatar-img">
                </div>
                <div class="pf-user-info">
                    <h2><?php echo htmlspecialchars($nombre); ?></h2>
                    <p class="pf-email"><?php echo htmlspecialchars($email); ?></p>
                    <span class="pf-role-badge" data-role="<?php echo $rol; ?>">
                        <?php echo strtoupper($rol); ?>
                    </span>
                </div>
            </div>

            <div class="pf-card-options">
                <h4 class="pf-options-title">Opciones de Cuenta</h4>
                <div class="pf-options-grid">
                    <?php if ($es_admin): ?>
                        <button class="pf-btn pf-btn-admin" onclick="window.location.href='admin_panel.php'">PANEL ADMIN</button>
                        <button class="pf-btn" onclick="window.location.href='mis_publicaciones.php'">PUBLICACIONES</button>
                    <?php else: ?>
                        <button class="pf-btn" onclick="window.location.href='favoritos.php'">FAVORITOS</button>
                        <button class="pf-btn" onclick="window.location.href='reservas.php'">RESERVAS</button>
                    <?php endif; ?>
                    
                    <button class="pf-btn">SEGURIDAD</button>
                    <form action="../Controller/UserController.php?action=logout" method="POST" style="display: contents;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="pf-btn pf-btn-logout">
                            CERRAR SESIÓN
                        </button>
                    </form>
                    <button class="pf-btn pf-btn-danger" id="open-delete-modal-btn">
                        ELIMINAR CUENTA
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de confirmación de eliminación de cuenta -->
    <div id="delete-account-modal" class="modal-overlay">
        <div class="modal-content <?php echo $es_admin ? 'modal-admin' : 'modal-standard'; ?>">
            <div class="modal-header">
                <i class="fa-solid fa-triangle-exclamation modal-icon"></i>
                <h3>Eliminar Cuenta</h3>
            </div>
            <div class="modal-body">
                <?php if ($es_admin): ?>
                    <p class="warning-text"><strong>¡ADVERTENCIA DE ADMINISTRADOR!</strong></p>
                    <p>Estás a punto de eliminar de forma permanente una cuenta de <strong>ADMINISTRADOR</strong>. Esta acción es crítica y no se puede deshacer.</p>
                    <p class="instruction-text">Para confirmar la eliminación, introduce el código de seguridad de administrador:</p>
                    
                    <form action="../Controller/UserController.php?action=deleteAccount" method="POST" id="delete-account-form">
                        <div class="input-container">
                            <input type="text" id="admin-confirm-code" name="admin_code" class="modal-input" placeholder="Introduce 'admin123'" autocomplete="off" required>
                            <span class="validation-badge invalid" id="validation-badge"><i class="fa-solid fa-xmark"></i></span>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="pf-btn modal-btn-cancel" id="close-delete-modal">CANCELAR</button>
                            <button type="submit" class="pf-btn pf-btn-danger modal-btn-confirm" id="confirm-delete-btn" disabled>ELIMINAR PERMANENTEMENTE</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p>¿Estás seguro de que deseas eliminar tu cuenta de <strong>NightFest</strong>?</p>
                    <p class="warning-text">Esta acción es irreversible y borrará toda tu información de perfil de forma permanente.</p>
                    
                    <form action="../Controller/UserController.php?action=deleteAccount" method="POST" id="delete-account-form">
                        <div class="modal-actions">
                            <button type="button" class="pf-btn modal-btn-cancel" id="close-delete-modal">CANCELAR</button>
                            <button type="submit" class="pf-btn pf-btn-danger modal-btn-confirm" id="confirm-delete-btn">SÍ, ELIMINAR CUENTA</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

       <?php include 'footer.php'; ?>


</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openModalBtn = document.getElementById('open-delete-modal-btn');
    const closeModalBtn = document.getElementById('close-delete-modal');
    const modal = document.getElementById('delete-account-modal');
    const adminInput = document.getElementById('admin-confirm-code');
    const confirmBtn = document.getElementById('confirm-delete-btn');
    const validationBadge = document.getElementById('validation-badge');

    // Abrir Modal
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.add('active');
            if (adminInput) {
                setTimeout(() => adminInput.focus(), 150);
            }
        });
    }

    // Cerrar Modal al hacer clic en Cancelar
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            modal.classList.remove('active');
            if (adminInput) {
                adminInput.value = '';
                resetAdminValidation();
            }
        });
    }

    // Cerrar Modal al hacer clic fuera de la caja
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
            if (adminInput) {
                adminInput.value = '';
                resetAdminValidation();
            }
        }
    });

    // Cerrar Modal con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            modal.classList.remove('active');
            if (adminInput) {
                adminInput.value = '';
                resetAdminValidation();
            }
        }
    });

    // Validación en tiempo real para Administradores
    if (adminInput && confirmBtn && validationBadge) {
        adminInput.addEventListener('input', function() {
            const value = adminInput.value.trim();
            if (value === 'admin123') {
                // Estado VÁLIDO
                confirmBtn.removeAttribute('disabled');
                validationBadge.className = 'validation-badge valid';
                validationBadge.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
                adminInput.style.borderColor = '#2ecc71';
                adminInput.style.boxShadow = '0 0 10px rgba(46, 204, 113, 0.3)';
            } else {
                // Estado INVÁLIDO
                confirmBtn.setAttribute('disabled', 'true');
                validationBadge.className = 'validation-badge invalid';
                validationBadge.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                adminInput.style.borderColor = '#e74c3c';
                adminInput.style.boxShadow = '0 0 10px rgba(231, 76, 60, 0.3)';
            }
        });
    }

    function resetAdminValidation() {
        if (confirmBtn && validationBadge && adminInput) {
            confirmBtn.setAttribute('disabled', 'true');
            validationBadge.className = 'validation-badge invalid';
            validationBadge.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            adminInput.style.borderColor = '';
            adminInput.style.boxShadow = '';
        }
    }
});
</script>

