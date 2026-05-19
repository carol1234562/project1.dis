<?php
require_once '../Model/seguridad.php';
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

// Consulta de datos del evento
$sql = "SELECT * FROM eventos WHERE id = $id_evento";
$resultado = $conexion->query($sql);
$evento = $resultado->fetch_assoc();

if (!$evento) {
    die("Evento no encontrado");
}

// Coordenadas por defecto si no existen en la BD (ejemplo Barcelona)
$lat = !empty($evento['latitud']) ? $evento['latitud'] : 41.3851;
$lng = !empty($evento['longitud']) ? $evento['longitud'] : 2.1734;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NightFest - <?php echo htmlspecialchars($evento['artista']); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <link rel="stylesheet" href="../assets/css/infoevento.css">
</head>
<body id="infoevento-page">

        <?php include 'header.php';
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
        <a href="infoevento.php?id=<?php echo $id_evento; ?>" class="pag-link active">INFORMACIÓN DEL EVENTO</a>
        <a href="infoartista.php?id=<?php echo $id_evento; ?>" class="pag-link">INFORMACIÓN DEL ARTISTA</a>
    </div>

    <div class="evento-row-split">
        
        <div class="col-izquierda">
            <div class="img-container profile-shadow">
                <img src="../assets/img/<?php echo $evento['imagen']; ?>" alt="Portada Evento">
            </div>
        </div>

        <div class="col-derecha">
            <div class="detalles-evento-header">
                <p class="fecha-badge">
                    <?php echo htmlspecialchars($evento['ubicacion']); ?> | 
                    <?php echo date('D, d M Y', strtotime($evento['fecha_evento'])); ?>
                </p>
            </div>

            <div class="info-principal">
                <h3 class="gold-subtitle">Ubicación del Recinto</h3>
                <p class="texto-blanco">
                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($evento['ubicacion']); ?><br>
                    <?php echo htmlspecialchars($evento['localidad']); ?>, España
                </p>
                <p class="texto-gris">Apertura de puertas: <?php echo htmlspecialchars($evento['hora']); ?></p>
                <button id="btn-recenter" class="btn-ubicacion">
                    <i class="fas fa-location-arrow"></i> MI UBICACIÓN
                </button>
            </div>

            <div id="map" class="mapa-container-mini"></div>
        </div>

    </div>
</main>

        <?php include 'footer.php'; ?>



    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // 1. Inicializar mapa
        var map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 15);

        // 2. Capa oscura (Stadia Maps o CartoDB)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: 'NightFest'
        }).addTo(map);

        // 3. Icono Dorado Personalizado
        var goldIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // 4. Añadir marcador del Evento
        L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>], { icon: goldIcon }).addTo(map)
            .bindPopup(`
                <div style="text-align:center; font-family: 'Montserrat', sans-serif;">
                    <strong style="color:#D4AF37; font-size:14px;"><?php echo htmlspecialchars($evento['artista']); ?></strong><br>
                    <span style="color:#333; font-size:11px;"><?php echo htmlspecialchars($evento['ubicacion']); ?></span><br>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $lat; ?>,<?php echo $lng; ?>" target="_blank" style="display:inline-block; margin-top:8px; padding:5px 10px; background:#D4AF37; color:white; text-decoration:none; border-radius:4px; font-size:10px; font-weight:bold;">CÓMO LLEGAR</a>
                </div>
            `).openPopup();

        // 5. Botón Mi Ubicación
        document.getElementById('btn-recenter').onclick = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latlng = [position.coords.latitude, position.coords.longitude];
                    L.circleMarker(latlng, {
                        radius: 8,
                        fillColor: "#fff",
                        color: "#D4AF37",
                        weight: 2,
                        fillOpacity: 0.8
                    }).addTo(map).bindPopup("Estás aquí").openPopup();
                    map.flyTo(latlng, 15);
                });
            }
        };

        // Forzar renderizado
        setTimeout(function() { map.invalidateSize(); }, 500);
    </script>
</body>
</html>