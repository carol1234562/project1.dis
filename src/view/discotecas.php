<?php
require_once '../static model/seguridad.php';
$conexion = new mysqli("localhost", "root", "", "NightFest");

if ($conexion->connect_error) {
    die("Error de conexión");
}

$eventos_por_pagina = 10;

$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

$offset = ($pagina_actual - 1) * $eventos_por_pagina;

$sql = "SELECT * FROM eventos 
        WHERE fecha_evento >= CURDATE() 
        ORDER BY fecha_evento ASC 
        LIMIT $eventos_por_pagina OFFSET $offset";

$resultado = $conexion->query($sql);

$conteo_query = "SELECT COUNT(*) as total FROM eventos WHERE fecha_evento >= CURDATE()";
$conteo_res = $conexion->query($conteo_query);
$total_registros = ($conteo_res) ? $conteo_res->fetch_assoc()['total'] : 0;
$total_paginas = ceil($total_registros / $eventos_por_pagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NightFest - Discotecas</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/discotecas.css">

</head>
<body id="discotecas-page">

        <?php include '../static model/header.php';
        if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
} ?>

    <main class="container">
        <div class="section-header">
            <h2 class="section-title-line">DISCOTECAS</h2>
        </div>

        <div class="eventos-cascada">
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($row = $resultado->fetch_assoc()): ?>
                    <div class="evento-row" onclick="window.location.href='infoevento.php?id=<?php echo $row['id']; ?>'">
                        <div class="img-container">
                            <img src="../assets/img/<?php echo $row['imagen']; ?>" alt="Evento">
                        </div>
                        <div class="info-principal">
                            <span class="fecha-badge">
                                <?php echo date('D, d M', strtotime($row['fecha_evento'])); ?> | <?php echo $row['hora']; ?>
                            </span>
                            <h3><?php echo htmlspecialchars($row['artista']); ?></h3>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['ubicacion']); ?> • <?php echo htmlspecialchars($row['localidad']); ?></p>
                        </div>
                        <div class="estado-accion">
                            <span class="status-disponible"><?php echo $row['estado']; ?></span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No hay discotecas con eventos próximos.</p>
            <?php endif; ?>
        </div>

        <?php if ($total_paginas > 1): ?>
        <div class="paginacion-container">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?p=<?php echo $i; ?>" class="pag-link <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </main>

    <?php include '../static model/footer.php'; ?>


</body>
</html>