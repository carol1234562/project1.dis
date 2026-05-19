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
$eventos = $ec->getAllEvents();
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
    <style>
        .admin-header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
            width: 100%;
        }
        .btn-create-new {
            background-color: #D4AF37;
            color: #000;
            padding: 12px 25px;
            font-weight: 700;
            text-decoration: none;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
        }
        .btn-create-new:hover {
            background-color: #b8952e;
            transform: translateY(-2px);
        }
        .event-grid-admin {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        .event-card-admin {
            background: #111;
            border: 1px solid #222;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .event-card-admin:hover {
            border-color: #D4AF37;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.1);
        }
        .card-img-wrap {
            height: 180px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .event-card-admin:hover .card-img-wrap img {
            transform: scale(1.05);
        }
        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            gap: 10px;
        }
        .card-date {
            color: #D4AF37;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
        }
        .card-location {
            color: #aaa;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .card-status {
            align-self: flex-start;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-available { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .status-soldout { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
        .status-upcoming { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }

        .card-footer-actions {
            display: flex;
            border-top: 1px solid #222;
            background: #0d0d0d;
        }
        .action-link-btn {
            flex: 1;
            padding: 15px;
            text-align: center;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.3s ease;
        }
        .action-edit-btn {
            color: #D4AF37;
            border-right: 1px solid #222;
        }
        .action-edit-btn:hover {
            background: rgba(212, 175, 55, 0.05);
        }
        .action-delete-btn {
            color: #ff4d4d;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
        }
        .action-delete-btn:hover {
            background: rgba(255, 77, 77, 0.05);
        }
    </style>
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
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>
