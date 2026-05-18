<?php

class UserController
{
    private mysqli $connection;

    public function __construct()
    {
        // --- CONFIGURACIÓN DE CONEXIÓN ---
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db   = "nightfest";

        $this->connection = new mysqli($host, $user, $pass, $db);

        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }

        $this->connection->set_charset("utf8mb4");
    }

    // --- MÉTODO: LOGIN ---
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $this->connection->real_escape_string(trim($_POST['email']));
            $password = $this->connection->real_escape_string(trim($_POST['password']));

            // SOLUCIÓN: Añadimos 'foto_perfil' a la consulta SQL
            $sql = "SELECT id, nombre, email, rol, foto_perfil FROM usuarios 
                    WHERE email = '$email' AND password = '$password'";
            
            $result = $this->connection->query($sql);

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['rol'] = $user['rol'];
                // SOLUCIÓN: Guardamos la foto en la sesión para que el header.php la pueda leer
                $_SESSION['user_photo'] = $user['foto_perfil']; 

                header("Location: ../view/inicio1.php");
                exit();
            } else {
                header("Location: ../view/login.php?error=Datos_Incorrectos");
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
                header("Location: ../view/registro_" . $rol . ".php?error=Formato de email invalido");
                exit();
            }

            if (strlen($pass) < 6) {
                header("Location: ../view/registro_" . $rol . ".php?error=La contrasena debe tener minimo 6 caracteres");
                exit();
            }

            if ($rol === 'admin') {
                $codigo = $_POST['admin_code'] ?? '';
                if ($codigo !== "admin123") {
                    header("Location: ../view/registro_admin.php?error=Codigo de administrador incorrecto");
                    exit();
                }
            }

            // Guardamos las imágenes de perfil en la subcarpeta organizada 'uploads'
            $nombre_foto = 'default.png';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $ext_permitidas)) {
                    header("Location: ../view/registro_" . $rol . ".php?error=Formato de imagen no permitido");
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
            $stmt->bind_param("sssss", $nombre, $email, $pass, $rol, $nombre_foto);

            if ($stmt->execute()) {
                header("Location: ../view/login.php?success=cuenta_creada");
            } else {
                header("Location: ../view/registro_" . $rol . ".php?error=El correo ya esta registrado");
            }
            exit();
        }
    }

    // --- MÉTODO: LOGOUT ---
    public function logout()
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    
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
    header("Location: ../view/inicio1.php");
    exit();
}

    // --- MÉTODO: OBTENER DATOS DE USUARIO ---
    public function getUserData($id) {
        $id = $this->connection->real_escape_string($id);
        $sql = "SELECT * FROM usuarios WHERE id = '$id'";
        $resultado = $this->connection->query($sql);
        return $resultado->fetch_assoc();
    }

    // --- MÉTODO: ELIMINAR CUENTA ---
    public function deleteAccount($id, $adminCode = null) {
        $id = $this->connection->real_escape_string($id);
        
        $user = $this->getUserData($id);
        if (!$user) {
            header("Location: ../view/perfil.php?error=usuario_no_encontrado");
            exit();
        }

        // Si el usuario es administrador, comprobar que el código sea correcto
        if ($user['rol'] === 'admin') {
            if ($adminCode !== 'admin123') {
                header("Location: ../view/perfil.php?error=codigo_admin_incorrecto");
                exit();
            }
        }
        
        if ($user['foto_perfil'] !== 'default.png') {
            $rutaFoto = "../assets/img/uploads/" . $user['foto_perfil'];
            if (file_exists($rutaFoto)) {
                unlink($rutaFoto); 
            }
        }

        $sql = "DELETE FROM usuarios WHERE id = '$id'";
        if ($this->connection->query($sql)) {
            $this->logout(); 
        } else {
            header("Location: ../view/perfil.php?error=no_se_pudo_borrar");
            exit();
        }
    }

    // --- MÉTODO: ACTUALIZAR PERFIL (Corregido de PDO a MySQLi uniforme) ---
    public function actualizarPerfil($userId, $datos, $archivoFoto) {
        $userId = $this->connection->real_escape_string($userId);
        $nombre = $this->connection->real_escape_string(trim($datos['nombre']));
        $email  = $this->connection->real_escape_string(trim($datos['email']));
        
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

        // 3. Actualizar base de datos usando MySQLi estándar
        $sql = "UPDATE usuarios SET nombre = '$nombre', email = '$email', foto_perfil = '$nombreFotoFinal' WHERE id = '$userId'";
        
        // Sincronizar el nombre en la sesión por si cambió
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['user_name'] = $nombre;

        return $this->connection->query($sql);
    }
}

// --- LÓGICA DE CONTROL DE RUTAS ---
$uc = new UserController();
$action = $_GET['action'] ?? '';

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
        header("Location: ../view/login.php");
    }
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uc->login();
    }
}
?>