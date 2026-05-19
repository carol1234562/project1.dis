<?php
require_once __DIR__ . '/../Model/Database.php';

class EventController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function requireAdmin() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            http_response_code(403);
            die("Acceso denegado. Solo administradores pueden realizar esta acción.");
        }
    }

    public function getAllEvents($limit = null, $offset = null) {
        if ($limit !== null && $offset !== null) {
            $stmt = $this->db->prepare("SELECT * FROM eventos WHERE fecha_evento >= CURDATE() ORDER BY fecha_evento ASC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = $this->db->query("SELECT * FROM eventos ORDER BY fecha_evento ASC");
            return $stmt->fetchAll();
        }
    }

    public function getTotalEventsCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM eventos WHERE fecha_evento >= CURDATE()");
        $res = $stmt->fetch();
        return $res ? (int)$res['total'] : 0;
    }

    public function getEventById($id) {
        $stmt = $this->db->prepare("SELECT * FROM eventos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createEvent() {
        $this->requireAdmin();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $artista = trim($_POST['artista'] ?? '');
            $fecha = $_POST['fecha_evento'] ?? '';
            $hora = $_POST['hora'] ?? '';
            $localidad = trim($_POST['localidad'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $estado = $_POST['estado'] ?? 'DISPONIBLE';

            if (empty($artista) || empty($fecha) || empty($hora) || empty($localidad) || empty($ubicacion)) {
                header("Location: ../View/crear_evento.php?error=Todos los campos son obligatorios");
                exit();
            }

            $imagen = 'default.jpg';
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

                if (in_array($ext, $ext_permitidas)) {
                    $imagen = "evento_" . time() . "." . $ext;
                    move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/" . $imagen);
                }
            }

            $stmt = $this->db->prepare(
                "INSERT INTO eventos (artista, imagen, fecha_evento, hora, localidad, ubicacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            
            if ($stmt->execute([$artista, $imagen, $fecha, $hora, $localidad, $ubicacion, $estado])) {
                header("Location: ../View/discotecas.php?success=evento_creado");
            } else {
                header("Location: ../View/crear_evento.php?error=No se pudo guardar el evento");
            }
            exit();
        }
    }

    public function updateEvent() {
        $this->requireAdmin();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = (int)($_POST['id'] ?? 0);
            $artista = trim($_POST['artista'] ?? '');
            $fecha = $_POST['fecha_evento'] ?? '';
            $hora = $_POST['hora'] ?? '';
            $localidad = trim($_POST['localidad'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $estado = $_POST['estado'] ?? 'DISPONIBLE';

            if ($id <= 0 || empty($artista) || empty($fecha) || empty($hora) || empty($localidad) || empty($ubicacion)) {
                header("Location: ../View/editar_evento.php?id=$id&error=Todos los campos son obligatorios");
                exit();
            }

            // Obtener evento actual para mantener la imagen vieja si no se sube una nueva
            $event = $this->getEventById($id);
            if (!$event) {
                header("Location: ../View/discotecas.php?error=evento_no_encontrado");
                exit();
            }

            $imagen = $event['imagen'];
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

                if (in_array($ext, $ext_permitidas)) {
                    $new_imagen = "evento_" . time() . "." . $ext;
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../assets/img/" . $new_imagen)) {
                        // Borrar la anterior si no es la default
                        if ($imagen !== 'default.jpg' && file_exists("../assets/img/" . $imagen)) {
                            @unlink("../assets/img/" . $imagen);
                        }
                        $imagen = $new_imagen;
                    }
                }
            }

            $stmt = $this->db->prepare(
                "UPDATE eventos SET artista = ?, imagen = ?, fecha_evento = ?, hora = ?, localidad = ?, ubicacion = ?, estado = ? WHERE id = ?"
            );
            
            if ($stmt->execute([$artista, $imagen, $fecha, $hora, $localidad, $ubicacion, $estado, $id])) {
                header("Location: ../View/infoevento.php?id=$id&success=evento_actualizado");
            } else {
                header("Location: ../View/editar_evento.php?id=$id&error=No se pudo actualizar el evento");
            }
            exit();
        }
    }

    public function deleteEvent() {
        $this->requireAdmin();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = (int)($_POST['id'] ?? 0);
            
            $event = $this->getEventById($id);
            if (!$event) {
                header("Location: ../View/discotecas.php?error=evento_no_encontrado");
                exit();
            }

            // Eliminar imagen si no es la default
            $imagen = $event['imagen'];
            if ($imagen !== 'default.jpg' && file_exists("../assets/img/" . $imagen)) {
                @unlink("../assets/img/" . $imagen);
            }

            $stmt = $this->db->prepare("DELETE FROM eventos WHERE id = ?");
            if ($stmt->execute([$id])) {
                header("Location: ../View/discotecas.php?success=evento_eliminado");
            } else {
                header("Location: ../View/discotecas.php?error=no_se_pudo_eliminar");
            }
            exit();
        }
    }
}

// Router for action handling
$ec = new EventController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'create') {
    $ec->createEvent();
} elseif ($action === 'update') {
    $ec->updateEvent();
} elseif ($action === 'delete') {
    $ec->deleteEvent();
}
