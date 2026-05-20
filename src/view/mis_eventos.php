<?php
require_once '../Model/seguridad.php';
require_once '../Controller/EventController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Muro de seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio1.php");
    exit();
}

$ec = new EventController();

// Configuración de paginación
$eventos_por_pagina = 4;
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
    <title>Administrar Eventos | NightFest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/discotecas.css">
    <link rel="stylesheet" href="../assets/css/mis_eventos.css">
</head>
<body id="discotecas-page">

    <?php include 'header.php'; ?>

    <main class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
        <div class="admin-header-box">
            <div class="section-header" style="margin: 0;">
                <h2 class="section-title-line" style="margin: 0; text-align: left;">Administrar Eventos</h2>
            </div>
            <a href="crear_evento.php" class="btn-create-new">
                <i class="fas fa-plus"></i> CREAR NUEVO EVENTO
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

        <div class="event-grid-admin">
            <?php if (!empty($eventos)): ?>
                <?php foreach ($eventos as $row): 
                    $statusClass = 'status-available';
                    if ($row['estado'] === 'AGOTADO') $statusClass = 'status-soldout';
                    elseif ($row['estado'] === 'PRÓXIMAMENTE') $statusClass = 'status-upcoming';
                ?>
                    <div class="event-card-admin">
                        <div class="card-img-wrap" onclick="window.location.href='infoevento.php?id=<?php echo $row['id']; ?>'" style="cursor: pointer;">
                            <img src="../assets/img/<?php echo $row['imagen']; ?>" alt="Portada">
                        </div>
                        <div class="card-body">
                            <span class="card-date"><?php echo date('d M Y', strtotime($row['fecha_evento'])); ?> | <?php echo $row['hora']; ?></span>
                            <h3 class="card-title"><?php echo htmlspecialchars($row['artista']); ?></h3>
                            <span class="card-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['ubicacion']); ?> (<?php echo htmlspecialchars($row['localidad']); ?>)</span>
                            <span class="card-status <?php echo $statusClass; ?>"><?php echo $row['estado']; ?></span>
                        </div>
                        <div class="card-footer-actions">
                            <a href="editar_evento.php?id=<?php echo $row['id']; ?>" class="action-link-btn action-edit-btn">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="../Controller/EventController.php?action=delete" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este evento?');" style="display: flex; flex: 1;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="action-link-btn action-delete-btn" style="width: 100%; border: none;">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #ccc; grid-column: 1 / -1; text-align: center; font-size: 1.1rem; padding: 40px 0;">No hay eventos registrados en el sistema.</p>
            <?php endif; ?>
        </div>

        <!-- Paginación -->
        <?php if ($total_paginas > 1): ?>
            <div class="paginacion-container" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px; margin-bottom: 20px;">
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
