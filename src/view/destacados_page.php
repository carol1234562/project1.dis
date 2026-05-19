<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$is_logged = isset($_SESSION['user_id']);
$es_admin = ($is_logged && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin');
$inicial = ($is_logged && isset($_SESSION['user_name'])) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : "";
$foto_perfil = $is_logged ? ($_SESSION['user_photo'] ?? null) : null;

$pagina_actual = 'destacados_page';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NightFest | Crónica de la Noche</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/destacados.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="news-layout">
    
    <?php include '../static model/header.php'; ?>

    <main class="container">
        <header class="page-intro">
            <h1 class="main-title">Cartelera de la Semana</h1>
            <p class="date">Barcelona, <?php echo date('d \d\e F, Y'); ?></p>
        </header>

        <section class="news-grid">
            
            <article class="news-card">
                <div class="news-image">
                    <img src="https://youbarcelona.com/uploads/images/c/opium%20barcelona%20sala%206/original.jpg" alt="Opium">
                </div>
                <div class="news-content">
                    <span class="category">DISCOTECA</span>
                    <h3>OPIUM: EL EPICENTRO DEL LUJO</h3>
                    <p>La combinación perfecta de restaurante, bar y club frente al mar. Una experiencia imprescindible en la costa barcelonesa.</p>
                    <a href="#" class="read-more">Leer crónica →</a>
                </div>
            </article>

            <article class="news-card">
                <div class="news-image">
                    <img src="https://youbarcelona.com/uploads/images/c/pacha%20barcelona%20entrada%203/400x300.webp" alt="Pacha">
                </div>
                <div class="news-content">
                    <span class="category">URBANO</span>
                    <h3>PACHA: RITMOS QUE NO DESCANSAN</h3>
                    <p>Elegancia y ritmos latinos con sonido de última generación. Un clásico que se reinventa cada noche.</p>
                    <a href="#" class="read-more">Leer crónica →</a>
                </div>
            </article>

            <article class="news-card">
                <div class="news-image">
                    <img src="https://i.blogs.es/e084e0/paradiso/840_560.jpg" alt="Paradiso">
                </div>
                <div class="news-content">
                    <span class="category">COCTELERÍA</span>
                    <h3>PARADISO: EL SECRETO MEJOR GUARDADO</h3>
                    <p>Coctelería conceptual tras una puerta de nevera. Descubre por qué es considerado uno de los mejores bares del mundo.</p>
                    <a href="#" class="read-more">Leer crónica →</a>
                </div>
            </article>

        </section>
    </main>
    <?php include '../static model/footer.php'; ?>

</body>
</html>