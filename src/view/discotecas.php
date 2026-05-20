<?php
require_once '../Model/seguridad.php';
require_once '../Controller/EventController.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$es_admin = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');

$ec = new EventController();

$eventos_por_pagina = 10;

$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

$offset = ($pagina_actual - 1) * $eventos_por_pagina;

$resultado = $ec->getAllEvents($eventos_por_pagina, $offset);
$total_registros = $ec->getTotalEventsCount();
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

    <?php include 'header.php';
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    } ?>

    <main class="container">
        <div class="section-header">
            <h2 class="section-title-line">DISCOTECAS</h2>
        </div>

        <div class="eventos-cascada">
            <?php if (!empty($resultado)): ?>
                <?php foreach ($resultado as $row): ?>
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
                        <div class="estado-accion" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 8px;">
                            <span class="status-disponible"><?php echo $row['estado']; ?></span>
                            <?php if ($es_admin): ?>
                                <div class="admin-actions" style="display: flex; gap: 15px;">
                                    <a href="editar_evento.php?id=<?php echo $row['id']; ?>" class="btn-edit-mini" onclick="event.stopPropagation();" style="color: #D4AF37; font-size: 1.1rem;" title="Editar"><i class="fas fa-edit"></i></a>
                                    <form action="../Controller/EventController.php?action=delete" method="POST" onsubmit="event.stopPropagation(); return confirm('¿Estás seguro de que deseas eliminar este evento?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn-delete-mini" style="background: none; border: none; color: #ff4d4d; cursor: pointer; padding: 0; font-size: 1.1rem;" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <i class="fas fa-chevron-right"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
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

    <?php include 'footer.php'; ?>

</body>
</html>