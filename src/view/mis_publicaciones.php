<?php
require_once '../Model/seguridad.php';
require_once '../Controller/EventController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio1.php");
    exit();
}

$ec = new EventController();

// Configuración de paginación
$eventos_por_pagina = 10;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $eventos_por_pagina;

$eventos = $ec->getAllEvents($eventos_por_pagina, $offset);
$total_registros = $ec->getTotalEventsCount();
$total_paginas = ceil($total_registros / $eventos_por_pagina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Publicaciones | NightFest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/STYLE1.css">
    <link rel="stylesheet" href="../assets/css/mis_publicaciones.css">
</head>
<body style="background-color: #000; color: #fff;">

    <?php include 'header.php'; ?>

    <main class="container">
        <div class="return-container" onclick="window.location.href='perfil.php'" style="margin-bottom: 30px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; color: #D4AF37; font-weight: 600;">
            <i class="fas fa-angle-left"></i>
            <span>Volver al Perfil</span>
        </div>

        <div class="section-header">
            <h2 class="section-title-line">MIS PUBLICACIONES</h2>
            <a href="crear_evento.php" class="admin-btn admin-btn-edit" style="background-color: #D4AF37; color: #000; border-color: #D4AF37;">
                <i class="fas fa-plus"></i> CREAR NUEVA
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-alert" style="background: rgba(46, 204, 113, 0.15); color: #2ecc71; border: 1px solid #2ecc71; padding: 15px; border-radius: 6px; margin-bottom: 30px; font-weight: 600; text-align: center;">
                <i class="fas fa-check-circle"></i>
                <?php 
                    $msg = $_GET['success'];
                    if ($msg === 'evento_creado') echo "El evento ha sido creado exitosamente.";
                    elseif ($msg === 'evento_actualizado') echo "El evento ha sido actualizado exitosamente.";
                    elseif ($msg === 'evento_eliminado') echo "El evento ha sido eliminado correctamente.";
                    else echo htmlspecialchars($msg);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-alert" style="background: rgba(231, 76, 60, 0.15); color: #e74c3c; border: 1px solid #e74c3c; padding: 15px; border-radius: 6px; margin-bottom: 30px; font-weight: 600; text-align: center;">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="eventos-cascada">
            <?php if (!empty($eventos)): ?>
                <?php foreach ($eventos as $row): 
                    $statusClass = '';
                    if ($row['estado'] === 'AGOTADO') $statusClass = 'status-agotado';
                    elseif ($row['estado'] === 'PRÓXIMAMENTE') $statusClass = 'status-proximamente';
                ?>
                    <div class="evento-row" onclick="window.location.href='infoevento.php?id=<?php echo $row['id']; ?>'">
                        <div class="img-container">
                            <img src="../assets/img/<?php echo $row['imagen']; ?>" alt="Portada">
                        </div>
                        <div class="info-principal">
                            <span class="fecha-badge"><?php echo date('d M Y', strtotime($row['fecha_evento'])); ?> - <?php echo $row['hora']; ?></span>
                            <h3><?php echo htmlspecialchars($row['artista']); ?></h3>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['ubicacion']); ?> (<?php echo htmlspecialchars($row['localidad']); ?>)</p>
                        </div>
                        <div class="estado-accion">
                            <span class="status-badge <?php echo $statusClass; ?>"><?php echo $row['estado']; ?></span>
                            <div class="admin-row-actions">
                                <a href="editar_evento.php?id=<?php echo $row['id']; ?>" class="admin-btn admin-btn-edit" onclick="event.stopPropagation();">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="../Controller/EventController.php?action=delete" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este evento?');" style="display: inline;" onclick="event.stopPropagation();">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="admin-btn admin-btn-delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #ccc; text-align: center; font-size: 1.1rem; padding: 40px 0;">No tienes publicaciones registradas en el sistema.</p>
            <?php endif; ?>
        </div>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
            <div class="paginacion-container">
                <?php if ($pagina_actual > 1): ?>
                    <a href="?p=<?php echo $pagina_actual - 1; ?>" class="pag-link"><i class="fas fa-angle-left"></i></a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?p=<?php echo $i; ?>" class="pag-link <?php echo ($i === $pagina_actual) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?p=<?php echo $pagina_actual + 1; ?>" class="pag-link"><i class="fas fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>
