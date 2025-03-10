<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    // Verificar la conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener categorías
    $stmt = $pdo->query("SELECT id, nombre FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener filtros
    $categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    $rango_precio = isset($_GET['precio']) ? $_GET['precio'] : null;
    $busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : null;

    // Construir la consulta base
    $query = "SELECT p.*, c.nombre as categoria_nombre 
              FROM productos p 
              LEFT JOIN categorias c ON p.categoria_id = c.id 
              WHERE p.segunda_mano = 0";
    $params = [];

    // Añadir filtros si existen
    if ($categoria_id) {
        $query .= " AND p.categoria_id = ?";
        $params[] = $categoria_id;
    }

    // Añadir filtro de precio
    if ($rango_precio) {
        switch($rango_precio) {
            case '0-30':
                $query .= " AND p.precio < 30";
                break;
            case '30-40':
                $query .= " AND p.precio BETWEEN 30 AND 40";
                break;
            case '40-50':
                $query .= " AND p.precio BETWEEN 40 AND 50";
                break;
            case '50-60':
                $query .= " AND p.precio BETWEEN 50 AND 60";
                break;
            case '60+':
                $query .= " AND p.precio > 60";
                break;
        }
    }

    // Añadir búsqueda si existe
    if ($busqueda) {
        $query .= " AND (LOWER(p.nombre) LIKE LOWER(?) OR LOWER(p.descripcion) LIKE LOWER(?))";
        $busquedaParam = "%$busqueda%";
        $params[] = $busquedaParam;
        $params[] = $busquedaParam;
    }

    $query .= " ORDER BY p.id DESC";
    
    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<<<<<<< HEAD
<div class="container">
    <div class="filters-container">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <h3>Filtrar por Categoría</h3>
                <select name="categoria" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
=======
<div class="content">
    <!-- Hero Section con Video de Fondo -->
    <header class="hero">
        <!-- Contenedor del video -->
        <div class="video-container">
            <!-- Video de fondo -->
            <video id="hero-video" autoplay loop muted playsinline>
                <source src="fotosWeb/videoplayback (1).mp4" type="video/mp4">
                Tu navegador no soporta videos HTML5.
            </video>
            
            <!-- Overlay oscuro para mejorar la legibilidad del texto -->
            <div class="video-overlay"></div>
            
            <!-- Contenido del hero -->
            <div class="hero-content">
                <h1>Bienvenido a MGames</h1>
                <p>Tu destino para los mejores videojuegos</p>
                <br>
                <div class="hero-buttons">
                    <a href="segunda_mano.php" class="btn">Ver Productos de Segunda Mano</a>
                    <a href="lista_deseos.php" class="btn">Lista de Deseos</a>
                </div>
            </div>
            
            <!-- Controles de video -->
            <div class="video-controls">
                <button id="play-pause-btn" aria-label="Pausar video">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pause-icon">
                        <rect x="6" y="4" width="4" height="16"></rect>
                        <rect x="14" y="4" width="4" height="16"></rect>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="play-icon" style="display: none;">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                </button>
                <button id="mute-btn" aria-label="Activar sonido">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mute-icon">
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                        <path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"></path>
                        <path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"></path>
                        <line x1="12" y1="19" x2="12" y2="23"></line>
                        <line x1="8" y1="23" x2="16" y2="23"></line>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="volume-icon" style="display: none;">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Filtros -->
    <section class="filters">
        <div class="filter-container">
            <!-- Filtro de Categorías -->
            <div class="filter">
                <h2>Filtrar por Categoría</h2>
                <form method="GET" action="index.php" class="filter-form">
                    <?php if(isset($_GET['precio'])): ?>
                        <input type="hidden" name="precio" value="<?php echo htmlspecialchars($_GET['precio']); ?>">
                    <?php endif; ?>
                    <?php if(isset($_GET['buscar'])): ?>
                        <input type="hidden" name="buscar" value="<?php echo htmlspecialchars($_GET['buscar']); ?>">
                    <?php endif; ?>
                    <select name="categoria" onchange="this.form.submit()">
                        <option value="">Todas las categorías</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                                <?php echo (isset($categoria_id) && $categoria_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
>>>>>>> US16_Video_en_home
            </div>

            <div class="filter-group">
                <h3>Filtrar por Precio</h3>
                <select name="precio" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos los precios</option>
                    <option value="0-30" <?php echo $rango_precio == '0-30' ? 'selected' : ''; ?>>Menos de 30€</option>
                    <option value="30-40" <?php echo $rango_precio == '30-40' ? 'selected' : ''; ?>>30€ - 40€</option>
                    <option value="40-50" <?php echo $rango_precio == '40-50' ? 'selected' : ''; ?>>40€ - 50€</option>
                    <option value="50-60" <?php echo $rango_precio == '50-60' ? 'selected' : ''; ?>>50€ - 60€</option>
                    <option value="60+" <?php echo $rango_precio == '60+' ? 'selected' : ''; ?>>Más de 60€</option>
                </select>
            </div>

            <div class="filter-group">
                <h3>Buscar Juegos</h3>
                <div class="search-input-group">
                    <input type="text" 
                           name="buscar" 
                           class="filter-input" 
                           placeholder="Buscar juegos..." 
                           value="<?php echo htmlspecialchars($busqueda ?? ''); ?>">
                    <button type="submit" class="search-button">Buscar</button>
                </div>
            </div>
        </form>
        <?php if ($categoria_id || $rango_precio || $busqueda): ?>
            <a href="index.php" class="clear-filters">Limpiar filtros</a>
        <?php endif; ?>
    </div>

    <section class="featured-products">
        <h2>Productos</h2>
        
        <?php if (empty($productos)): ?>
            <div class="no-results">
                <p>No se encontraron productos con los filtros seleccionados.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($productos as $producto): ?>
                    <div class="product-card <?php echo (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN') ? 'admin-style' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                            <a href="detalle.php?id=<?php echo $producto['id']; ?>" class="btn">Ver Detalles</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<<<<<<< HEAD
=======
<!-- Agrega este CSS en tu archivo de estilos o en el head -->
<style>
    /* Estilos para el hero con video */
    .hero {
        position: relative;
        height: 400px;
        overflow: hidden;
        padding: 0;
        margin: 0;
    }
    
    .video-container {
        position: relative;
        width: 100%;
        height: 100%;
    }
    
    #hero-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .hero-content {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: white;
        text-align: center;
        padding: 0 20px;
    }
    
    .hero-content h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .hero-content p {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
    }
    
    .hero-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .video-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        display: flex;
        gap: 10px;
        z-index: 10;
    }
    
    .video-controls button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(0, 0, 0, 0.5);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s;
    }
    
    .video-controls button:hover {
        background-color: rgba(0, 0, 0, 0.7);
    }
    
    /* Asegúrate de que los botones se vean bien */
    .hero .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4a5af8;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    
    .hero .btn:hover {
        background-color: #3949e0;
    }
    
    .hero .btn:nth-child(2) {
        background-color: white;
        color: #4a5af8;
    }
    
    .hero .btn:nth-child(2):hover {
        background-color: #f0f0f0;
    }
</style>

<script>
    // JavaScript para controlar el video
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('hero-video');
        const playPauseBtn = document.getElementById('play-pause-btn');
        const muteBtn = document.getElementById('mute-btn');
        const playIcon = document.querySelector('.play-icon');
        const pauseIcon = document.querySelector('.pause-icon');
        const muteIcon = document.querySelector('.mute-icon');
        const volumeIcon = document.querySelector('.volume-icon');
        
        // Función para alternar reproducción/pausa
        playPauseBtn.addEventListener('click', function() {
            if (video.paused) {
                video.play();
                playIcon.style.display = 'none';
                pauseIcon.style.display = 'block';
                playPauseBtn.setAttribute('aria-label', 'Pausar video');
            } else {
                video.pause();
                playIcon.style.display = 'block';
                pauseIcon.style.display = 'none';
                playPauseBtn.setAttribute('aria-label', 'Reproducir video');
            }
        });
        
        // Función para alternar silencio
        muteBtn.addEventListener('click', function() {
            if (video.muted) {
                video.muted = false;
                muteIcon.style.display = 'none';
                volumeIcon.style.display = 'block';
                muteBtn.setAttribute('aria-label', 'Silenciar');
            } else {
                video.muted = true;
                muteIcon.style.display = 'block';
                volumeIcon.style.display = 'none';
                muteBtn.setAttribute('aria-label', 'Activar sonido');
            }
        });
        
        // Asegurarse de que el video se reproduzca automáticamente
        video.play().catch(function(error) {
            // El autoplay fue bloqueado por el navegador
            console.log("Autoplay bloqueado:", error);
            // Mostrar el icono de reproducción
            playIcon.style.display = 'block';
            pauseIcon.style.display = 'none';
        });
    });
</script>

>>>>>>> US16_Video_en_home
<?php require_once 'includes/footer.php'; ?>