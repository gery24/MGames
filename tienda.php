<?php
session_start();
// Incluir archivo de configuración de la base de datos
require_once 'config/database.php';
require_once 'includes/header.php';

// Verificar si el usuario es admin para añadir la clase 'admin' al body
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
$bodyClass = $isAdmin ? 'admin' : '';

// Inicializar $ofertas como un array vacío por defecto para evitar warnings
$ofertas = [];

try {
    // Verificar la conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener categorías con la columna foto
    $stmt = $pdo->query("SELECT id, nombre, foto FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar el número de productos por categoría
    $stmt = $pdo->query("SELECT c.id, c.nombre, COUNT(p.id) as count 
                        FROM categorias c 
                        LEFT JOIN productos p ON c.id = p.categoria_id 
                        GROUP BY c.id");
    $categorias_count = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener productos para la sección de ofertas (TODOS los productos con descuento > 0)
    $query_ofertas = "SELECT p.*, c.nombre as categoria_nombre 
                      FROM productos p 
                      LEFT JOIN categorias c ON p.categoria_id = c.id
                      WHERE p.descuento > 0"; // Filtrar por productos con descuento > 0

    $stmt_ofertas = $pdo->prepare($query_ofertas);
    $stmt_ofertas->execute();
    $ofertas = $stmt_ofertas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
    if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
        echo "<br>SQL State: " . $e->getCode();
    }
    die();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGames - Tienda de Videojuegos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tienda.css">
    <link rel="stylesheet" href="css/admin.css">
</head>

<body class="<?php echo $bodyClass; ?>">

<div class="content">
    <!-- Hero Section con Video de Fondo -->
    <header class="hero">
        <div class="video-container">
            <video id="hero-video" autoplay loop muted playsinline>
                <source src="FotosWeb/videoplayback (1).mp4" type="video/mp4">
                Tu navegador no soporta videos HTML5.
            </video>
            <br>
            <br>
            <br>
            <div class="video-overlay"></div>
            <div class="hero-content">
                <h1>Explora Nuestro Universo Gaming</h1>
                <p>Descubre los mejores títulos, ofertas exclusivas y contenido premium para todos los gamers</p>
                <br>
                <div class="hero-buttons">
                    <a href="#categorias" class="btn btn-primary">Explorar Categorías</a>
                    <a href="#ofertas" class="btn btn-transparent">Ver Ofertas</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Categorías Visuales -->
    <section id="categorias" class="categorias-visuales">
        <div class="container">
            <style>
                /* Estilos para que el contenedor principal de cada sección apile sus hijos verticalmente */
                section .container {
                    display: flex;
                    flex-direction: column;
                }

                /* Estilos para la cabecera de la sección de Categorías */
                .categories-header {
                    margin-bottom: 2rem; /* Espacio entre el encabezado y la cuadrícula */
                }

                .categories-header .section-title {
                    margin-bottom: 0; /* Elimina el margen inferior predeterminado del título */
                }

                /* Estilo para el contenedor del botón debajo de la cuadrícula */
                 .categories-footer-button {
                    text-align: center; /* Centra el botón */
                    margin-top: 1rem; /* Espacio arriba del botón */
                 }

                /* --- Estilos para las secciones en horizontal con scroll --- */

                /* Regla general restaurada para todas las secciones de grid horizontal */
                .categorias-grid,
                .ofertas-grid,
                .lanzamientos-grid,
                .valorados-grid,
                .eventos-grid,
                .blog-grid {
                    display: flex; /* Muestra los elementos en una fila horizontal */
                    overflow-x: auto; /* Añade scroll horizontal si los elementos exceden el ancho */
                    gap: 1.5rem; /* Espacio entre los elementos */
                    padding-bottom: 1rem; /* Espacio inferior para el scrollbar si aparece */
                    flex-wrap: nowrap; /* Asegurar que los elementos flex no se envuelvan */
                    /* Animación de desplazamiento suave */
                    scroll-behavior: smooth;
                }

                /* Regla específica para la cuadrícula de categorías con recuadro blanco */
                .categories-horizontal-scroll {
                 display: flex;
                 overflow-x: auto;
                 overflow-y: hidden; /* Ocultar scroll vertical por si acaso */
                 gap: 1.5rem;
                 padding-bottom: 1rem;
                 flex-wrap: nowrap;
                 scroll-behavior: smooth;
                 /* Estilos para el recuadro blanco */
                 background-color: white; /* Fondo blanco */
                 padding: 1.5rem; /* Espaciado interno */
                 border-radius: var(--radius); /* Bordes redondeados (reutilizo la variable global si está definida) */
                 box-shadow: var(--shadow); /* Sombra (reutilizo la variable global) */
                 /* Asegurar que el contenedor ocupe el ancho completo y maneje su propio overflow */
                 width: 100%; /* Ocupar el 100% del ancho del padre */
                 box-sizing: border-box; /* Incluir padding y borde en el ancho */
                 /* Añadir margen arriba para separarlo del container superior */
                 margin-top: 2rem; /* Espacio entre el título/botón y el recuadro */
             }


             /* Asegurar que las tarjetas individuales dentro de estas secciones no se encojan */
             .categoria-card,
             .oferta-card,
             .lanzamiento-card,
             .valorado-card,
             .evento-card,
             .blog-card {
                 flex: 0 0 auto; /* Evita que las tarjetas se encojen */
                 width: 280px; /* Ancho fijo para las tarjetas (ajústalo si es necesario) */
                 /* Asegúrate de que otros estilos como height, border-radius, etc. sigan aplicándose */
             }

            /* Asegurar que el contenido dentro de las tarjetas se apile verticalmente */
            .oferta-content,
            .lanzamiento-content,
            .valorado-content,
            .evento-content,
            .blog-content {
                display: flex;
                flex-direction: column;
            }

            /* Añado estilos para que el texto dentro de categoria-content sea blanco */
            .categoria-content h3,
            .categoria-content p {
                color: white; /* Establecer el color del texto a blanco */
            }

            /* Forzar a los elementos directos dentro de los contenedores de contenido a ocupar todo el ancho */
            .oferta-content > *,
            .lanzamiento-content > *,
            .valorado-content > *,
            .evento-content > *,
            .blog-content > * {
                 width: 100%;
            }

            /* Asegurar que los contenedores de precios y acciones también apilen su contenido si es necesario */
            .oferta-prices,
            .lanzamiento-actions,
            .valorado-actions,
            .evento-info, /* El contenedor info en eventos tiene varios p */
            .blog-meta /* El contenedor meta en blog tiene varios span */
            {
                display: flex;
                flex-direction: column;
                /* Ajusta el gap o margin si necesitas espacio entre estos elementos apilados */
                 gap: 0.5rem; /* Espacio entre elementos apilados como precios o iconos/texto en info/meta */
            }

            /* Estilos para hacer el scrollbar más visible en Webkit (Chrome, Safari, Edge) */
            .categories-horizontal-scroll::-webkit-scrollbar {
              height: 10px; /* Altura del scrollbar horizontal */
            }

            .categories-horizontal-scroll::-webkit-scrollbar-track {
              background: #f1f1f1; /* Color del fondo del track */
              border-radius: 5px;
            }

            .categories-horizontal-scroll::-webkit-scrollbar-thumb {
              background: #888; /* Color del "pulgar" del scrollbar */
              border-radius: 5px;
            }

            .categories-horizontal-scroll::-webkit-scrollbar-thumb:hover {
              background: #555; /* Color al pasar el ratón */
            }

            /* Estilos para hacer el scrollbar más visible en Firefox */
            .categories-horizontal-scroll {
              scrollbar-width: thin; /* "auto" o "thin" */
              scrollbar-color: #888 #f1f1f1; /* color del pulgar y color del track */
            }

        </style>
            <div class="categories-header">
                <h2 class="section-title">Explora por Categorías</h2>
            </div>
        </div>
        <div class="categorias-grid categories-horizontal-scroll">
            <?php
            $colors = [
                'from-red-500 to-orange-500',
                'from-blue-500 to-indigo-500',
                'from-green-500 to-emerald-500',
                'from-yellow-500 to-amber-500',
                'from-purple-500 to-pink-500',
                'from-indigo-500 to-purple-500'
            ];
            $i = 0;

            $first_four_categories = array_slice($categorias, 0, 3);
            foreach ($first_four_categories as $cat):
                $current_cat_count = 0;
                if (isset($cat['id'])) {
                    foreach ($categorias_count as $cat_count_data) {
                        if (isset($cat_count_data['id']) && $cat_count_data['id'] == $cat['id']) {
                            $current_cat_count = $cat_count_data['count'];
                            break;
                        }
                    }
                }

                $color_class = $colors[$i % count($colors)];
                $i++;
                ?>
                <a href="todos_productos.php?categoria=<?php echo htmlspecialchars($cat['id'] ?? ''); ?>"
                    class="categoria-card <?php echo htmlspecialchars($color_class); ?>"
                    style="background-image: url('<?php echo htmlspecialchars($cat['foto'] ?? ''); ?>'); background-size: cover; background-position: center;">
                    <div class="categoria-overlay"></div>
                </a>
            <?php endforeach; ?>
            <?php
            // Las demás categorías se añadirán vía JavaScript por toggleCategories()
            ?>
        </div>
    </section>

    <!-- Sección de Ofertas -->
    <section id="ofertas" class="ofertas">
        <div class="container">
            <h2 class="section-title">Ofertas Imperdibles</h2>
            <div class="ofertas-grid" id="ofertas-grid">
                <?php
                foreach ($ofertas as $oferta):
                    $precio_original = $oferta['precio'];
                    $descuento_porcentaje = $oferta['descuento'] ?? 0;
                    $precio_descuento = $precio_original * (1 - ($descuento_porcentaje / 100));
                    ?>
                    <div class="oferta-card" style="position: relative;">
                        <?php if ($descuento_porcentaje > 0): ?>
                            <div class="oferta-discount-badge" style="position: absolute !important; top: 0 !important; left: 0 !important; margin: 1rem !important; z-index: 10 !important;">-<?php echo $descuento_porcentaje; ?>%</div>
                        <?php endif; ?>
                        <div class="oferta-image">
                            <img src="<?php echo htmlspecialchars($oferta['imagen'] ?? ''); ?>"
                                alt="<?php echo htmlspecialchars($oferta['nombre'] ?? ''); ?>">
                        </div>
                        <div class="oferta-content">
                            <h3><?php echo htmlspecialchars($oferta['nombre'] ?? ''); ?></h3>
                            <div class="oferta-prices">
                                <span class="original-price">€<?php echo number_format($precio_original, 2); ?></span>
                                <span class="discount-price">€<?php echo number_format($precio_descuento, 2); ?></span>
                            </div>
                            <div class="oferta-actions">
                                <a href="producto.php?id=<?php echo htmlspecialchars($oferta['id'] ?? ''); ?>"
                                    class="btn btn-primary btn-sm">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
             <!-- Nuevo contenedor para el botón Mostrar Todas las Ofertas -->
             <div class="text-center categories-footer-button">
                 <a href="todos_productos.php?descuento=true" class="btn btn-outline">Ver Todas las Ofertas</a>
             </div>
        </div>
    </section>

    <!-- Próximos Lanzamientos -->
    <section class="proximos-lanzamientos">
        <div class="container">
            <h2 class="section-title">Próximos Lanzamientos</h2>
            <div class="lanzamientos-grid">
                <div class="lanzamiento-card">
                    <div class="lanzamiento-image">
                        <img src="FotosWeb/FINAL GTA6.png" alt="Grand Theft Auto VI">
                        <div class="lanzamiento-fecha">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="lanzamiento-content">
                        <h3>Grand Theft Auto VI</h3>
                        <p>Juego de mundo abierto con una gran historia....</p>
                        <div class="lanzamiento-actions">
                            <span class="lanzamiento-price">€90.00</span>
                            <a href="producto.php?id=120" class="btn btn-secondary btn-sm">Pre-ordenar</a>
                        </div>
                    </div>
                </div>
                <div class="lanzamiento-card">
                    <div class="lanzamiento-image">
                        <img src="FotosWeb/youtuberslife.png" alt="Youtubers Life 3">
                        <div class="lanzamiento-fecha">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="lanzamiento-content">
                        <h3>Youtubers Life 3</h3>
                        <p>Conviértete en una estrella digital desde cero....</p>
                        <div class="lanzamiento-actions">
                            <span class="lanzamiento-price">€29.99</span>
                            <a href="producto.php?id=119" class="btn btn-secondary btn-sm">Pre-ordenar</a>
                        </div>
                    </div>
                </div>
                <div class="lanzamiento-card">
                    <div class="lanzamiento-image">
                        <img src="FotosWeb/Torrente2.png" alt="Torrente 2">
                        <div class="lanzamiento-fecha">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="lanzamiento-content">
                        <h3>Torrente 2</h3>
                        <p>orrente regresa con una misión delirante...</p>
                        <div class="lanzamiento-actions">
                            <span class="lanzamiento-price">€39.99</span>
                            <a href="producto.php?id=118" class="btn btn-secondary btn-sm">Pre-ordenar</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Nuevo contenedor para el botón Ver Todos los Lanzamientos -->
            <div class="text-center categories-footer-button">
                 <a href="todos_productos.php?lanzamiento=true" class="btn btn-outline">Ver Próximos Lanzamientos</a>
            </div>
        </div>
    </section>

    <!-- Mejor Valorados -->
    <section class="mejor-valorados">
        <div class="container">
            <h2 class="section-title">Los Mejor Valorados</h2>
            <div class="valorados-grid">
                <div class="valorado-card">
                    <div class="valorado-rating">
                        <span class="rating-number">5.0</span>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="valorado-image">
                        <img src="fotosWeb/God of War.png" alt="God of War">
                    </div>
                    <div class="valorado-content">
                        <h3>God of War</h3>
                        <span class="valorado-category">Aventura</span>
                        <div class="valorado-price">€49.99</div>
                        <a href="producto.php?id=7" class="btn btn-primary btn-sm">Ver
                            Detalles</a>
                    </div>
                </div>
                <div class="valorado-card">
                    <div class="valorado-rating">
                        <span class="rating-number">4.0</span>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                    </div>
                    <div class="valorado-image">
                        <img src="fotosWeb/tarjeta_xbox_10.jpg" alt="Tarjeta Xbox 10€">
                    </div>
                    <div class="valorado-content">
                        <h3>Tarjeta Xbox 10€</h3>
                        <span class="valorado-category">Tarjeta XBOX</span>
                        <div class="valorado-price">€10.00</div>
                        <a href="producto.php?id=30" class="btn btn-primary btn-sm">Ver Detalles</a>
                    </div>
                </div>
            </div>
             <!-- Nuevo contenedor para el botón Ver Todos los Mejor Valorados -->
             <div class="text-center categories-footer-button">
                 <a href="todos_productos.php?valorado=true" class="btn btn-outline">Ver Los Mejor Valorados</a>
             </div>
        </div>
    </section>

    <!-- Eventos de Gaming -->
    <section class="eventos-gaming">
        <div class="container">
            <h2 class="section-title">Eventos de Gaming</h2>
            <div class="eventos-grid">
                <div class="evento-card">
                    <div class="evento-image">
                        <img src="FotosWeb/Torneo MGames.png" alt="Imagen Torneo MGames 2025">
                        <div class="evento-fecha">
                            <span class="evento-dia">15</span>
                            <span class="evento-mes">JUN</span>
                        </div>
                    </div>
                    <div class="evento-content">
                        <h3>Torneo MGames 2025</h3>
                        <div class="evento-info">
                            <p><i class="fas fa-map-marker-alt"></i> Centro de Convenciones, Madrid</p>
                            <p><i class="far fa-clock"></i> 10:00 AM - 8:00 PM</p>
                        </div>
                        <p class="evento-descripcion">Participa en el torneo de MGames de 2025.</p>
                        <a href="eventos.php?id=torneo2025" class="btn btn-primary">Más Información</a>
                    </div>
                </div>
                <div class="evento-card">
                    <div class="evento-image">
                        <img src="FotosWeb/Torrente2.png" alt="Imagen Lanzamiento Exclusivo">
                        <div class="evento-fecha">
                            <span class="evento-dia">22</span>
                            <span class="evento-mes">JUL</span>
                        </div>
                    </div>
                    <div class="evento-content">
                        <h3>Lanzamiento Exclusivo</h3>
                        <div class="evento-info">
                            <p><i class="fas fa-map-marker-alt"></i> Tienda MGames, Barcelona</p>
                            <p><i class="far fa-clock"></i> 8:00 PM - 12:00 AM</p>
                        </div>
                        <p class="evento-descripcion">Sé el primero en probar el nuevo título exclusivo.</p>
                        <a href="eventos.php?id=lanzamiento" class="btn btn-primary">Más Información</a>
                    </div>
                </div>
                <div class="evento-card">
                    <div class="evento-image">
                        <img src="FotosWeb/Convencion gamer 25.png" alt="Imagen Convención Gamer 2025">
                        <div class="evento-fecha">
                            <span class="evento-dia">10</span>
                            <span class="evento-mes">AGO</span>
                        </div>
                    </div>
                    <div class="evento-content">
                        <h3>Convención Gamer 2025</h3>
                        <div class="evento-info">
                            <p><i class="fas fa-map-marker-alt"></i> IFEMA, Madrid</p>
                            <p><i class="far fa-clock"></i> 9:00 AM - 7:00 PM</p>
                        </div>
                        <p class="evento-descripcion">La mayor convención de videojuegos de 2025.</p>
                        <a href="eventos.php?id=convencion2025" class="btn btn-primary">Más Información</a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-6">
                <a href="eventos.php" class="btn btn-outline">Ver Todos los Eventos</a>
            </div>
        </div>
    </section>

    <!-- Blog y Guías -->
    <section class="blog-guias">
        <div class="container">
            <h2 class="section-title">Blog y Guías de Juegos</h2>
            <div class="blog-grid">
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="FotosWeb/guia.png" alt="Guía para principiantes">
                        <div class="blog-category">Guía</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="far fa-calendar"></i> 10 Mayo, 2023</span>
                            <span><i class="far fa-user"></i> Admin</span>
                        </div>
                        <h3>Guía para principiantes: Cómo mejorar en juegos competitivos</h3>
                        <p>Aprende las estrategias básicas para mejorar tu rendimiento en los juegos competitivos
                            más populares.</p>
                        <a href="blog.php?id=1" class="btn btn-link">Leer más <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="FotosWeb/futuro de los videojuegos.png" alt="Análisis de juego">
                        <div class="blog-category">Análisis</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="far fa-calendar"></i> 5 Mayo, 2023</span>
                            <span><i class="far fa-user"></i> GameExpert</span>
                        </div>
                        <h3>Análisis en profundidad: El último RPG que está revolucionando el género</h3>
                        <p>Un análisis detallado del juego que está cambiando las reglas de los RPG modernos.</p>
                        <a href="blog.php?id=2" class="btn btn-link">Leer más <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="FotosWeb/tendencias.png" alt="Noticias de la industria">
                        <div class="blog-category">Noticias</div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span><i class="far fa-calendar"></i> 1 Mayo, 2023</span>
                            <span><i class="far fa-user"></i> NewsGamer</span>
                        </div>
                        <h3>Las tendencias que definirán el futuro de los videojuegos en 2025</h3>
                        <p>Descubre las tecnologías y tendencias que marcarán el rumbo de la industria de los
                            videojuegos este año.</p>
                        <a href="blog.php?id=3" class="btn btn-link">Leer más <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            </div>
            <div class="text-center mt-6">
                <a href="blog.php" class="btn btn-outline">Ver Todos los Artículos</a>
            </div>
        </div>
    </section>

    <!-- Comunidad de Gamers -->
    <section class="comunidad-gamers">
        <div class="container">
        <h1 style="text-align: center; color: white; font-size: 36px;">Únete a Nuestra Comunidad</h1>
<br>
            <div class="comunidad-content">
                <div class="comunidad-info">
                    <p>Forma parte de la comunidad de MGames y disfruta de beneficios exclusivos:</p>
                    <ul class="comunidad-beneficios">
                        <li><i class="fas fa-check-circle"></i> Acceso anticipado a nuevos lanzamientos</li>
                        <li><i class="fas fa-check-circle"></i> Descuentos exclusivos para miembros</li>
                        <li><i class="fas fa-check-circle"></i> Participación en torneos y eventos especiales</li>
                        <li><i class="fas fa-check-circle"></i> Foros de discusión con otros gamers</li>
                        <li><i class="fas fa-check-circle"></i> Guías y tutoriales exclusivos</li>
                    </ul>
                    <div class="comunidad-cta">
                        <a href="eventos.php" class="btn btn-primary">Unirse Ahora</a>
                        <a href="blog.php" class="btn btn-primary">Más Información</a>
                    </div>
                </div>
                <div class="comunidad-imagen">
                <img src="FotosWeb/logo.png" alt="Comunidad de Gamers" style="width:100px; height:100px; border-radius:50%;">
                </div>
            </div>
        </div>
    </section>

    <!-- Suscripción a Newsletter -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-info">
                    <h2>Mantente Informado</h2>
                    <p>Suscríbete a nuestro boletín para recibir las últimas noticias, ofertas exclusivas y
                        lanzamientos de juegos.</p>
                </div>
                <form class="newsletter-form" action="suscribir.php" method="post">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Tu correo electrónico" required>
                        <button type="submit" class="btn btn-primary">Suscribirse</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>


<!-- Botón scroll arriba -->
<button id="scrollToTopBtn" aria-label="Volver arriba">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
       stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
    <polyline points="18 15 12 9 6 15"></polyline>
  </svg>
</button>

<script>
    var todasLasCategorias = <?php echo json_encode($categorias); ?>;
    var categoriasCount = <?php echo json_encode($categorias_count); ?>;
    var colors = <?php echo json_encode($colors); ?>;
    var mostrandoTodas = true;

    function toggleOffers() {
        const offersGrid = document.getElementById('ofertas-grid');
        const button = document.getElementById('toggle-offers');
        const offerItems = offersGrid.querySelectorAll('.oferta-card');
        const initialOffersToShow = 4;
        
        let showingAll = button.innerText.includes("Ocultar");

        if (!showingAll) {
            offerItems.forEach(item => {
                item.classList.remove('hidden-offer-item');
            });
            button.innerText = "Ocultar Ofertas";
        } else {
            offerItems.forEach((item, index) => {
                if (index >= initialOffersToShow) {
                    item.classList.add('hidden-offer-item');
                }
            });
            button.innerText = "Mostrar Todas las Ofertas";
        }
        return false;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const categoriesGrid = document.querySelector('.categorias-grid');
        categoriesGrid.innerHTML = '';

        // Asegurarme de que la clase categories-horizontal-scroll está presente al cargar
         if (!categoriesGrid.classList.contains('categories-horizontal-scroll')) {
            categoriesGrid.classList.add('categories-horizontal-scroll');
        }

        // Muestro *todas* las categorías por defecto al cargar la página
        todasLasCategorias.forEach(function (cat) {
            const categoryCard = document.createElement('a');
            categoryCard.href = "todos_productos.php?categoria=" + cat.id;

            let current_cat_count = 0;
            const count_data = categoriasCount.find(item => item.id === cat.id);
            if (count_data) {
                current_cat_count = count_data.count;
            }

            let color_class = '';
            if (cat.color) {
                color_class = cat.color;
            } else {
                let index = todasLasCategorias.findIndex(item => item.id === cat.id);
                if (index !== -1) {
                    color_class = colors[index % colors.length];
                }
            }
            categoryCard.className = "categoria-card " + color_class;
            categoryCard.style.backgroundImage = "url('" + (cat.foto || '') + "')";
            categoryCard.style.backgroundSize = "cover";
            categoryCard.style.backgroundPosition = "center";
            categoryCard.innerHTML = `
                    <div class="categoria-overlay"></div>
                `;

            categoriesGrid.appendChild(categoryCard);
        });

        const initialOffersButton = document.getElementById('toggle-offers');
        if (initialOffersButton) {
            const offersGrid = document.getElementById('ofertas-grid');
            const offerItems = offersGrid.querySelectorAll('.oferta-card');
            const initialOffersToShow = 4;

            offerItems.forEach((item, index) => {
                if (index >= initialOffersToShow) {
                    item.classList.add('hidden-offer-item');
                }
            });

            if (offerItems.length > initialOffersToShow) {
                initialOffersButton.innerText = "Mostrar Todas las Ofertas";
            } else {
                initialOffersButton.style.display = 'none';
            }
        }

    });

</script>

<script>
 const scrollBtn = document.getElementById('scrollToTopBtn');

 window.addEventListener('scroll', () => {
  scrollBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
 });

 scrollBtn.addEventListener('click', () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
 });
</script>

</body>

</html>