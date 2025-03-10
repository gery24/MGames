<?php
session_start();
require_once 'config/database.php';

try {
    // Verificar la conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener categorías
    $stmt = $pdo->query("SELECT id, nombre FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener filtros
    $categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    $rango_precio = isset($_GET['precio']) ? $_GET['precio'] : null;
    $busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : null;

    // Construir la consulta base
    $query = "SELECT p.*, c.nombre as categoria_nombre 
              FROM productos p 
              LEFT JOIN categorias c ON p.categoria_id = c.id";
    $params = [];

    // Añadir filtros si existen
    $where = [];
    if ($categoria_id) {
        $where[] = "p.categoria_id = ?";
        $params[] = $categoria_id;
    }

    // Añadir filtro de precio
    if ($rango_precio) {
        switch($rango_precio) {
            case '0-30':
                $where[] = "p.precio < 30";
                break;
            case '30-40':
                $where[] = "p.precio BETWEEN 30 AND 40";
                break;
            case '40-50':
                $where[] = "p.precio BETWEEN 40 AND 50";
                break;
            case '50-60':
                $where[] = "p.precio BETWEEN 50 AND 60";
                break;
            case '60+':
                $where[] = "p.precio > 60";
                break;
        }
    }

    // Añadir búsqueda al WHERE si existe
    if ($busqueda) {
        $where[] = "p.nombre LIKE ?";
        $params[] = "%$busqueda%";
    }

    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }

    $query .= " ORDER BY p.id DESC";

    // Ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Mostrar información detallada del error
    echo "Error en la base de datos: " . $e->getMessage() . "<br>";
    echo "SQL State: " . $e->getCode() . "<br>";
    // Mostrar la estructura actual de las tablas
    try {
        echo "<h3>Estructura de la tabla categorias:</h3>";
        foreach($pdo->query("DESCRIBE categorias") as $row) {
            print_r($row);
            echo "<br>";
        }
        echo "<h3>Estructura de la tabla productos:</h3>";
        foreach($pdo->query("DESCRIBE productos") as $row) {
            print_r($row);
            echo "<br>";
        }
    } catch(PDOException $e2) {
        echo "Error al obtener la estructura de las tablas: " . $e2->getMessage();
    }
    die();
}

// Incluir el header compartido
$titulo = "MGames - Tu tienda de videojuegos";
require_once 'includes/header.php';
?>

<div class="content">
    <!-- Hero Section con Video de Fondo -->
    <header class="hero">
        <div class="video-container">
            <video id="hero-video" autoplay loop muted playsinline>
                <source src="FotosWeb/videoplayback (1).mp4" type="video/mp4">
                Tu navegador no soporta videos HTML5.
            </video>
            <div class="video-overlay"></div>
            <div class="hero-content">
                <h1>Bienvenido a MGames</h1>
                <p>Tu destino para los mejores videojuegos</p>
                <br>
                <div class="hero-buttons">
                    <a href="segunda_mano.php" class="btn">Ver Productos de Segunda Mano</a>
                    <a href="lista_deseos.php" class="btn">Lista de Deseos</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Filtros -->
    <section class="filters">
        <div class="filters-container">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <h3>Filtrar por Categoría</h3>
                    <select name="categoria" class="filter-select" onchange="this.form.submit()">
                        <option value="">Todas las categorías</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                                    <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                        <input type="text" name="buscar" placeholder="Buscar juegos..." 
                               value="<?php echo htmlspecialchars($busqueda ?? ''); ?>" class="filter-input">
                        <button type="submit" class="search-button">Buscar</button>
                    </div>
                </div>
            </form>
            <?php if ($categoria_id || $rango_precio || $busqueda): ?>
                <a href="index.php" class="clear-filters">Limpiar filtros</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Productos -->
    <section class="featured-products">
        <h2>Productos</h2>
        <div class="products-grid">
            <?php foreach($productos as $producto): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <div class="product-card-content">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn">Ver Detalles</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

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

<?php require_once 'includes/footer.php'; ?>