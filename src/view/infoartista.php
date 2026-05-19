<?php
require_once '../static model/seguridad.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$conexion = new mysqli("localhost", "root", "", "NightFest");

if ($conexion->connect_error) {
    die("Error de conexión");
}

// Lógica de usuario logueado
$is_logged = isset($_SESSION['user_id']);
$es_admin = ($is_logged && $_SESSION['rol'] === 'admin');
$inicial = ($is_logged && isset($_SESSION['user_name'])) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : "";

// Obtener ID del evento
$id_evento = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Consulta de datos del artista/evento
$sql = "SELECT * FROM eventos WHERE id = $id_evento";
$resultado = $conexion->query($sql);
$evento = $resultado->fetch_assoc();

if (!$evento) {
    die("Artista no encontrado");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NightFest - <?php echo htmlspecialchars($evento['artista']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/infoartista.css">
</head>
<body id="infoartista-page">

    <?php include '../static model/header.php'; 
    if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>


    <main class="container">
        <div class="section-header">
            <h2 class="section-title-line"><?php echo htmlspecialchars($evento['artista']); ?></h2>
        </div>

        <div class="paginacion-container tabs-navegacion">
            <a href="infoevento.php?id=<?php echo $id_evento; ?>" class="pag-link">INFORMACIÓN DEL EVENTO</a>
            <a href="infoartista.php?id=<?php echo $id_evento; ?>" class="pag-link active">INFORMACIÓN DEL ARTISTA</a>
        </div>

        <div class="evento-row tarjeta-artista">
            <div class="artista-bio-flex">
                
                <div class="artista-col-img">
                    <div class="img-container profile-shadow">
                        <img src="../assets/img/<?php echo $evento['imagen']; ?>" alt="<?php echo htmlspecialchars($evento['artista']); ?>">
                    </div>
                </div>

                <div class="artista-col-info">
                    <span class="fecha-badge">BIOGRAFÍA Y TRAYECTORIA</span>
                    <h3 class="gold-subtitle">Sobre el artista</h3>
                    
                    <div class="bio-content">
                        <p>
                            <?php echo htmlspecialchars($evento['artista']); ?> representa una nueva ola de música auténtica que rompe moldes y conecta directamente con su audiencia. Con una propuesta sonora innovadora, ha logrado posicionarse en los mejores escenarios internacionales.
                        </p>
                        <p>
                            Su trayectoria se caracteriza por una evolución constante, mezclando ritmos vanguardistas con una puesta en escena de alto nivel, diseñada para ofrecer una experiencia nocturna inigualable en los clubes más exclusivos.
                        </p>
                    </div>

                    <div class="artista-meta">
                        <p class="origen-tag">
                            <i class="fas fa-map-marker-alt"></i> 
                            <strong>Origen:</strong> <?php echo htmlspecialchars($evento['ubicacion']); ?>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </main>

         <?php include '../static model/footer.php'; ?>



</body>
</html>