<?php
    ob_start();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    // Identificación de usuario 
    $is_logged = isset($_SESSION['user_id']);
    $es_admin = ($is_logged && ($_SESSION['rol'] ?? '') === 'admin');

    // Lógica de destino para secciones privadas
    $destino_privado = $is_logged ? "reservar.php" : "login.php";

    // Sincronización del Avatar
    $foto_perfil = $_SESSION['user_photo'] ?? 'default.png';
    $inicial = "U";
    if ($is_logged && isset($_SESSION['user_name'])) {
        $inicial = strtoupper(substr($_SESSION['user_name'], 0, 1));
    }
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NightFest - Home</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/inicio1.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    
</head>

<body id="inicio">

    <div id="welcome-modal">
        <div class="modal-content">
            <h2 style="color:#D4AF37">¡BIENVENIDOS A NIGHTFEST!</h2>
            <p style="margin: 15px 0;">Proyecto por Johan Agreda y Carolina Solorzano.</p>
            <button id="close-welcome" class="btn-gold">ENTRAR</button>
        </div>
    </div>

    <div id="cookie-banner">
        <span>NightFest utiliza cookies para mejorar tu experiencia. ¿Aceptas?</span>
        <button id="accept-cookies" class="btn-gold" style="padding: 5px 15px;">ACEPTAR</button>
    </div>

    <?php include 'header.php'; ?>

    <main class="container">
        
        <section class="section-top">
            <h2 class="section-title">Destacados</h2>
            <div class="hero-container">
                <div class="hero-item">
                    <img src="https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80" alt="Duro" loading="lazy">
                    <div class="overlay-info">
                        <h1>DURO</h1>
                        <p>18 de nov. / 50€</p>
                    </div>
                </div>
                <div class="hero-sidebar">
                    <div class="hero-item">
                        <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/06/e6/28/1a/opium-mar-club.jpg?w=1200&h=-1&s=1" alt="Opium" loading="lazy">
                        <div class="overlay-info">
                            <h2>OPIUM - GALA</h2>
                            <p>17 de nov / 25€</p>
                        </div>
                    </div>
                    <div class="hero-item">
                        <img src="https://youbarcelona.com/uploads/images/c/Downtown%20barcelona%20club%20gente%202/400x300.webp?v=63811631314" alt="Downtown" loading="lazy">
                        <div class="overlay-info">
                            <h2>DOWN TOWN</h2>
                            <p>27 de nov / 25€</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-map">
            <h2 class="section-title">Cerca de ti</h2>
            <div class="map-wrapper">
                <div id="map-container" style="height: 450px; border-radius: 10px;"></div>
                <button id="btn-recenter">
                    <i class="fas fa-location-arrow"></i> MI UBICACIÓN
                </button>
            </div>
        </section>

        <?php
        $categorias = [
            "Discotecas" => [
                ["SUTTON", "https://images.unsplash.com/photo-1566737236500-c8ac43014a67?w=400"],
                ["PACHA", "https://images.unsplash.com/photo-1545128485-c400e7702796?w=400"],
                ["SAOKO", "https://images.unsplash.com/photo-1598387181032-a3103a2db5b3?w=400"]
            ],
            "Bares" => [
                ["REY DE COPAS", "https://privateaser-media.s3.eu-west-1.amazonaws.com/etab_photos/51437/original/427278.jpg"],
                ["NEON BAR", "https://i.etsystatic.com/37167070/r/il/7382dc/4585963861/il_570xN.4585963861_7zi0.jpg"],
                ["OPIUM BAR", "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/06/e6/28/1a/opium-mar-club.jpg?w=1200"]
            ],
            "Festivales" => [
                ["DURO", "https://wololosound.com/wp-content/uploads/468153119_18030427481409963_1982800582124372713_n.jpg"],
                ["SONAR 2026", "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSMmKfrEezCa8xnuyy5eSxAjH6L-ptWErxtxw&s"],
                ["BARCELONA ROCK", "https://youbarcelona.com/uploads/images/c/sala-apolo-barcelona-rock/original.jpg"]
            ],
            "Restaurantes" => [
                ["TAGLIATELLA", "https://img.carta.menu/storage/media/company_gallery/9355408/conversions/contribution_gallery.jpg"],
                ["HARD ROCK CAFE", "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/29/97/65/0a/caption.jpg?w=900&h=500&s=1"],
                ["ABaC", "https://cdn.thefork.com/tf-lab/image/upload/w_500,h_500,c_fill,q_auto,f_auto,g_auto:subject/restaurant/765beae9-f2d2-413d-ad33-9cb3a8a1b588/dd5fe68f-4943-430c-ae0d-946d73c6cfbe.jpg"]
            ]
        ];

        foreach ($categorias as $titulo => $locales): ?>
            <section class="section-bottom">
                <h2 class="section-title"><?php echo e($titulo); ?></h2>
                <div class="clubs-grid">
                    <?php foreach ($locales as $local): ?>
                        <div class="club-card">
                            <img src="<?php echo e($local[1]); ?>" alt="<?php echo e($local[0]); ?>" loading="lazy">
                            <div class="club-info">
                                <h4><?php echo e($local[0]); ?></h4>
                                <a href="<?php echo e($destino_privado); ?>" class="btn">Más información</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <section class="slider-section">
            <h2 class="section-title">Galería NightFest</h2>
            <div class="slider-galeria">
                <div class="slick-box"><img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=500" alt="G1"></div>
                <div class="slick-box"><img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=500" alt="G2"></div>
                <div class="slick-box"><img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=500" alt="G3"></div>
            </div>
        </section>

    </main>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
    $(document).ready(function() {
        // 1. Gestión de Modales y Cookies
        $('#close-welcome').click(function() { $('#welcome-modal').fadeOut(); });

        if (!localStorage.getItem('cookiesAccepted')) {
            $('#cookie-banner').css('display', 'flex');
        }
        $('#accept-cookies').click(function() {
            localStorage.setItem('cookiesAccepted', 'true');
            $('#cookie-banner').fadeOut();
        });

        // 2. Inicialización de Sliders
        $('.slider-galeria').slick({
            dots: true,
            infinite: true,
            speed: 500,
            slidesToShow: 3,
            slidesToScroll: 1,
            autoplay: true,
            responsive: [
                { breakpoint: 768, settings: { slidesToShow: 1 } }
            ]
        });

        // 3. Inicialización del Mapa
        const initMap = () => {
            const map = L.map('map-container').setView([41.3851, 2.1734], 14);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; CARTO'
            }).addTo(map);

            const goldIcon = L.divIcon({ className: 'gold-marker', iconSize: [12, 12] });

            const locales = [
                { name: "SUTTON", lat: 41.3965, lng: 2.1523, type: "Discoteca" },
                { name: "PACHA", lat: 41.3831, lng: 2.1973, type: "Discoteca" },
                { name: "OPIUM", lat: 41.3842, lng: 2.1965, type: "Discoteca" }
            ];

            locales.forEach(loc => {
                L.marker([loc.lat, loc.lng], { icon: goldIcon })
                 .addTo(map)
                 .bindPopup(`<b style="color:#D4AF37">${loc.name}</b><br>${loc.type}<br><a href="reservar.php" class="btn" style="font-size:10px; padding:4px">RESERVAR</a>`);
            });

            // Botón Recenter
            $('#btn-recenter').click(function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(pos => {
                        const coords = [pos.coords.latitude, pos.coords.longitude];
                        map.flyTo(coords, 15);
                        L.circleMarker(coords, { color: '#D4AF37', radius: 10 }).addTo(map).bindPopup("Estás aquí").openPopup();
                    }, () => {
                        alert("No se pudo obtener tu ubicación actual. Revisa los permisos de tu navegador.");
                    });
                } else {
                    alert("Tu navegador no soporta geolocalización.");
                }
            });

            // Ajuste de tamaño por si hay lags en el renderizado
            setTimeout(() => { map.invalidateSize(); }, 500);
        };

        initMap();
    });
    </script>
</body>
</html>