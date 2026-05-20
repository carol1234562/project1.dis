<?php

require_once __DIR__ . '/../../model/Db.php';
class UserController
{
    // Variable que guarda la conexión a la base de datos
    private $connection;

    public function __construct()
    {   // Usamos la clase Db.php para obtener la conexión PDO
        $this->connection = Db::getConexion();
    }

    // --- MÉTODO: LOGIN ---
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            // Buscamos solo por email, no comparamos password en el SQL
            $stmt = $this->connection->prepare(
                "SELECT id, nombre, email, rol, foto_perfil, password FROM usuarios WHERE email = ?"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Comparamos lo que escribió el usuario con el hash guardado en la base de datos
            if ($user && password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['rol'] = $user['rol'];
                // SOLUCIÓN: Guardamos la foto en la sesión para que el header.php la pueda leer
                $_SESSION['user_photo'] = $user['foto_perfil']; 

                header("Location: ../View/inicio1.php");
                exit();
            } else {
                header("Location: ../View/login.php?error=Datos_Incorrectos");
                exit();
            }
        }
    } 

    // --- MÉTODO: REGISTRO ---
    public function register()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre'] ?? '');
            $email  = trim($_POST['email'] ?? '');
            $pass   = trim($_POST['password'] ?? '');
            $rol    = $_POST['rol'] ?? 'estandar';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../View/registro_" . $rol . ".php?error=Formato de email invalido");
                exit();
            }

            if (strlen($pass) < 6) {
                header("Location: ../View/registro_" . $rol . ".php?error=La contrasena debe tener minimo 6 caracteres");
                exit();
            }

            if ($rol === 'admin') {
                $codigo = $_POST['admin_code'] ?? '';
                if ($codigo !== "admin123") {
                    header("Location: ../View/registro_admin.php?error=Codigo de administrador incorrecto");
                    exit();
                }
            }

            // Guardamos las imágenes de perfil en la subcarpeta organizada 'uploads'
            $nombre_foto = 'default.png';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $ext_permitidas)) {
                    header("Location: ../View/registro_" . $rol . ".php?error=Formato de imagen no permitido");
                    exit();
                }

                $nombre_foto = "perfil_" . time() . "." . $ext;
                
                // Creamos la carpeta por código si no existiera
                if (!is_dir("../assets/img/uploads/")) {
                    mkdir("../assets/img/uploads/", 0777, true);
                }
                
                move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/uploads/" . $nombre_foto);
            }

            $stmt = $this->connection->prepare(
                "INSERT INTO usuarios (nombre, email, password, rol, foto_perfil) VALUES (?, ?, ?, ?, ?)"
            );
            //$stmt->bind_param("sssss", $nombre, $email, $pass, $rol, $nombre_foto);

            //if ($stmt->execute()) {
            $passHash = password_hash($pass, PASSWORD_BCRYPT); // Convierte la contraseña en un hash seguro antes de guardarla


            if ($stmt->execute([$nombre, $email, $passHash, $rol, $nombre_foto])) {
                header("Location: ../view/login.php?success= Cuenta creada correctamente");
            } else {
                header("Location: ../View/registro_" . $rol . ".php?error=El correo ya esta registrado");
            }
            exit();
        }
    }

    // --- MÉTODO: LOGOUT ---
    public function logout($bypass_csrf = false)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Error: Método no permitido. Cierre de sesión requiere una petición POST.";
            exit();
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!$bypass_csrf) {
            $token = $_POST['csrf_token'] ?? '';
            if (empty($token) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                echo "Error: Token CSRF no válido.";
                exit();
            }
        }
        
        // 1. Limpiar el array de sesión en el servidor
        $_SESSION = array(); 
        session_unset();
        
        // 2. Destruir la cookie del navegador del cliente (¡El toque maestro!)
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // 3. Destruir el archivo de sesión en el servidor
        session_destroy();
        
        // 4. Redirigir
        header("Location: ../View/inicio1.php");
        exit();
    }

    // --- MÉTODO: OBTENER DATOS DE USUARIO ---
        public function getUserData($id) {
            // Buscamos el usuario por su id usando PDO
            $stmt = $this->connection->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }

    // --- MÉTODO: ELIMINAR CUENTA ---
    public function deleteAccount($id, $adminCode = null) {
        
        $user = $this->getUserData($id);
        if (!$user) {
            header("Location: ../View/perfil.php?error=usuario_no_encontrado");
            exit();
        }

        // Si el usuario es administrador, comprobar que el código sea correcto
        if ($user['rol'] === 'admin') {
            if ($adminCode !== 'admin123') {
                header("Location: ../View/perfil.php?error=codigo_admin_incorrecto");
                exit();
            }
        }
        
        if ($user['foto_perfil'] !== 'default.png') {
            $rutaFoto = "../assets/img/uploads/" . $user['foto_perfil'];
            if (file_exists($rutaFoto)) {
                unlink($rutaFoto); 
            }
        }

        $stmt = $this->connection->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt->execute([$id])) {
            $this->logout(true);
        } else {
            header("Location: ../View/perfil.php?error=no_se_pudo_borrar");
            exit();
        }
    }

    // MÉTODO: ACTUALIZAR PERFIL
    public function actualizarPerfil($userId, $datos, $archivoFoto) {

        $nombre = trim($datos['nombre']);
        $email  = trim($datos['email']);
        // 1. Obtener la foto actual
        $usuario = $this->getUserData($userId);
        $nombreFotoFinal = $usuario['foto_perfil'] ?? 'default.png';

        // 2. Procesar nueva foto si se ha subido una
        if (isset($archivoFoto['name']) && $archivoFoto['error'] === UPLOAD_ERR_OK) {
            $carpeta_dest = '../assets/img/uploads/'; 
            $extension = pathinfo($archivoFoto['name'], PATHINFO_EXTENSION);
            
            $nombreFotoFinal = "perfil_" . $userId . "_" . time() . "." . $extension;
            
            if (move_uploaded_file($archivoFoto['tmp_name'], $carpeta_dest . $nombreFotoFinal)) {
                // Borrar foto anterior si no es la default
                if ($usuario['foto_perfil'] !== 'default.png') {
                    $fotoAnterior = $carpeta_dest . $usuario['foto_perfil'];
                    if (file_exists($fotoAnterior)) {
                        unlink($fotoAnterior);
                    }
                }
                // Actualizar la foto en la sesión activa inmediatamente para evitar desfases
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_photo'] = $nombreFotoFinal;
            }
        }     
        // Sincroniza el nombre en la sesión por si cambió
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_name'] = $nombre;
        // Comprobamos si el usuario quiere cambiar la contraseña
        $nueva_password = trim($datos['nueva_password'] ?? '');

        if (!empty($nueva_password)) {
            // Si ha escrito una contraseña nueva la hasheamos y actualizamos también
            $passHash = password_hash($nueva_password, PASSWORD_BCRYPT);
            $stmt = $this->connection->prepare(
                "UPDATE usuarios SET nombre = ?, foto_perfil = ?, password = ? WHERE id = ?"
            );
            return $stmt->execute([$nombre, $nombreFotoFinal, $passHash, $userId]);
        } else {
            // Si no ha escrito nada solo actualizamos nombre y foto
            $stmt = $this->connection->prepare(
                "UPDATE usuarios SET nombre = ?, foto_perfil = ? WHERE id = ?"
            );
            return $stmt->execute([$nombre, $nombreFotoFinal, $userId]);
        }
    }
}

// --- LÓGICA DE CONTROL DE RUTAS ---
$uc = new UserController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'logout') {
    $uc->logout();
} elseif ($action === 'register') {
    $uc->register();
} elseif ($action === 'deleteAccount') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['user_id'])) {
        $adminCode = $_POST['admin_code'] ?? null;
        $uc->deleteAccount($_SESSION['user_id'], $adminCode);
    } else {
        header("Location: ../View/login.php");
    }
} elseif ($action === 'actualizarPerfil') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['user_id'])) {
        // Llamamos al método con los datos del formulario
        $resultado = $uc->actualizarPerfil($_SESSION['user_id'], $_POST, $_FILES['foto'] ?? []);
        if ($resultado) {
            header("Location: ../View/perfil.php?success=Perfil actualizado correctamente");
        } else {
            header("Location: ../View/editar_perfil.php?error=No se pudo actualizar el perfil");
        }
        exit();
    } else {
        header("Location: ../View/login.php");
    }
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uc->login();
    }
}
?>