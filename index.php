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

    // Cambiar el ORDER BY para mostrar productos en orden aleatorio
    $query .= " ORDER BY RAND()";

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
                    <div class="wishlist-icon" data-product-id="<?php echo $producto['id']; ?>">
                        <i class="fas fa-heart"></i>
                    </div>
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <div class="product-card-content">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                        <div class="product-actions">
                            <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn">Ver Detalles</a>
                            <button class="btn btn-wishlist" 
                                    data-product-id="<?php echo $producto['id']; ?>">
                                <i class="fas fa-heart"></i> Añadir a la lista de deseos
                            </button>
                        </div>
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

/* Estilos para los iconos de lista de deseos */
.wishlist-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.wishlist-icon:hover {
    transform: scale(1.1);
    background-color: rgba(255, 255, 255, 0.9);
}

.wishlist-icon i {
    color: #ccc;
    font-size: 18px;
    transition: color 0.3s ease;
}

.wishlist-icon:hover i {
    color: #ff6b6b;
}

.wishlist-icon.active i {
    color: #ff6b6b;
}

/* Estilos para las tarjetas de producto */
.product-card {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Estilos para las notificaciones */
#notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.notification {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 350px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification.success {
    border-left: 4px solid #2ecc71;
}

.notification.error {
    border-left: 4px solid #e74c3c;
}

.notification-content {
    display: flex;
    align-items: center;
}

.notification-content i {
    margin-right: 10px;
    font-size: 18px;
}

.notification-content i.fa-check-circle {
    color: #2ecc71;
}

.notification-content i.fa-exclamation-circle {
    color: #e74c3c;
}

.notification-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #999;
}
</style>

<script>
    // JavaScript para controlar el video
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('hero-video');
        
        // Verificar si los elementos existen antes de agregar event listeners
        const playPauseBtn = document.getElementById('play-pause-btn');
        const muteBtn = document.getElementById('mute-btn');
        
        if (playPauseBtn) {
            const playIcon = document.querySelector('.play-icon');
            const pauseIcon = document.querySelector('.pause-icon');
            
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
        }
        
        if (muteBtn) {
            const muteIcon = document.querySelector('.mute-icon');
            const volumeIcon = document.querySelector('.volume-icon');
            
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
        }
        
        // Asegurarse de que el video se reproduzca automáticamente
        video.play().catch(function(error) {
            // El autoplay fue bloqueado por el navegador
            console.log("Autoplay bloqueado:", error);
            // Mostrar el icono de reproducción si existe
            if (playPauseBtn) {
                const playIcon = document.querySelector('.play-icon');
                const pauseIcon = document.querySelector('.pause-icon');
                if (playIcon && pauseIcon) {
                    playIcon.style.display = 'block';
                    pauseIcon.style.display = 'none';
                }
            }
        });
        
        // Configurar los iconos de la lista de deseos
        const wishlistIcons = document.querySelectorAll('.wishlist-icon');
        
        wishlistIcons.forEach(icon => {
            icon.addEventListener('click', async function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-product-id');
                
                try {
                    const response = await fetch('add_to_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ productId: productId })
                    });
                    
                    try {
                        // Intentar parsear la respuesta como JSON
                        const data = await response.json();
                        
                        if (data.success) {
                            // Cambiar el estilo del icono para indicar que está en la lista
                            this.classList.add('active');
                            // Mostrar notificación
                            showNotification(data.message, 'success');
                            
                            // Opcional: redirigir a la lista de deseos
                            // window.location.href = 'lista_deseos.php';
                        } else {
                            // Si hay un error, mostrar el mensaje
                            if (data.message === 'Debes iniciar sesión') {
                                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
                            } else {
                                showNotification(data.message, 'error');
                            }
                        }
                    } catch (jsonError) {
                        // Si no se puede parsear como JSON, mostrar el error
                        console.error('Error al parsear JSON:', jsonError);
                        const text = await response.text();
                        console.error('Contenido de la respuesta:', text);
                        showNotification('Error al procesar la respuesta del servidor', 'error');
                    }
                } catch (error) {
                    console.error('Error de red:', error);
                    showNotification('Ha ocurrido un error al procesar tu solicitud', 'error');
                }
            });
        });
    });
    
    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        // Verificar si ya existe un contenedor de notificaciones
        let notificationContainer = document.getElementById('notification-container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.style.position = 'fixed';
            notificationContainer.style.top = '20px';
            notificationContainer.style.right = '20px';
            notificationContainer.style.zIndex = '9999';
            document.body.appendChild(notificationContainer);
        }
        
        // Crear la notificación
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">&times;</button>
        `;
        
        // Añadir estilos inline para asegurar que se muestren correctamente
        notification.style.backgroundColor = 'white';
        notification.style.borderRadius = '8px';
        notification.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        notification.style.padding = '15px';
        notification.style.marginBottom = '10px';
        notification.style.display = 'flex';
        notification.style.alignItems = 'center';
        notification.style.justifyContent = 'space-between';
        notification.style.maxWidth = '350px';
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease';
        notification.style.borderLeft = `4px solid ${type === 'success' ? '#2ecc71' : '#e74c3c'}`;
        
        // Añadir al contenedor
        notificationContainer.appendChild(notification);
        
        // Mostrar con animación
        setTimeout(() => {
            notification.style.opacity = '1';
        }, 10);
        
        // Configurar cierre automático
        const timeout = setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
        
        // Configurar botón de cierre
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.style.background = 'none';
        closeBtn.style.border = 'none';
        closeBtn.style.fontSize = '20px';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.color = '#999';
        
        closeBtn.addEventListener('click', () => {
            clearTimeout(timeout);
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
    }
    
    // Verificar mensajes en la URL al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('wishlist_success')) {
            showNotification('¡Producto añadido a tu lista de deseos!', 'success');
        } else if (urlParams.has('wishlist_error')) {
            const errorMsg = urlParams.get('wishlist_error') === 'already_exists'
                ? 'Este producto ya está en tu lista de deseos'
                : 'Error al añadir a la lista de deseos';
            showNotification(errorMsg, 'error');
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>

