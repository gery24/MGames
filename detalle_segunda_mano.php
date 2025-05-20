<?php
session_start();
require_once 'config/database.php';

// Verificar si se ha pasado un ID de producto
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Obtener información del producto de segunda mano
try {
    $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre, u.nombre as usuario_nombre FROM segunda_mano p LEFT JOIN categorias c ON p.categoria_id = c.id LEFT JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        header('Location: index.php');
        exit;
    }

    // Obtener juegos similares en la misma categoría
    $stmt_similares = $pdo->prepare("SELECT * FROM segunda_mano WHERE categoria_id = ? AND id != ? ORDER BY RAND() LIMIT 4");
    $stmt_similares->execute([$producto['categoria_id'], $id]);
    $juegos_similares = $stmt_similares->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

$titulo = htmlspecialchars($producto['nombre']);
require_once 'includes/header.php';
?>

<!-- Estilos específicos para la página de detalle de producto -->
<style>
    :root {
        --primary-color: #6d28d9;
        --primary-light: #8b5cf6;
        --primary-dark: #5b21b6;
        --secondary-color: #e0d5f7;
        --accent-color: #f97316;
        --text-color: #1f2937;
        --text-light: #6b7280;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius: 0.5rem;
        --radius-lg: 1rem;
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }

    .product-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: var(--text-light);
    }

    .breadcrumb a {
        color: var(--primary-color);
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb a:hover {
        color: var(--primary-dark);
    }

    .breadcrumb .separator {
        margin: 0 0.5rem;
        color: var(--text-light);
    }

    /* Product Detail Card */
    .product-detail-card {
        background-color: var(--bg-white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        margin-bottom: 2rem;
        padding: 20px; /* Añadir padding aquí si el diseño de producto.php lo tiene en la tarjeta principal */
    }

    .product-detail-content {
        display: flex;
        flex-wrap: wrap; /* Permite que los elementos se apilen en pantallas pequeñas */
        gap: 30px; /* Espacio entre la imagen y la información */
        align-items: flex-start; /* Alinea los elementos hijos (imagen y info) al inicio */
    }

    .product-image-section {
        width: 300px;
        height: auto;
        position: relative;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: auto;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-image:hover {
        transform: scale(1.03);
    }

    .product-info-section {
        flex: 1;
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        display: inline-block;
        background-color: var(--secondary-color);
        color: var(--primary-dark);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 0.8rem;
        border-radius: 2rem;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-uploader {
        font-size: 0.9rem;
        color: var(--text-light);
        margin-top: -0.5rem; /* Ajusta el margen superior para acercarlo a la categoría */
        margin-bottom: 1rem; /* Espacio debajo del nombre del usuario */
    }

    .product-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 1rem;
        color: var(--text-color);
        line-height: 1.2;
    }

    .product-estado {
        display: inline-flex;
        align-items: center;
        background-color: var(--primary-color);
        color: white;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 0.8rem;
        border-radius: 2rem;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
    }

    .product-estado i {
        margin-right: 0.4rem;
        font-size: 0.9rem;
    }

    .product-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0.5rem 0;
    }

    .product-description {
        margin-bottom: 1.5rem;
        color: var(--text-light);
        line-height: 1.7;
    }

    .product-features {
        margin-bottom: 2rem;
    }

    .product-features h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text-color);
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .features-list li {
        display: flex;
        align-items: center;
        margin-bottom: 0.8rem;
        color: var(--text-light);
    }

    .features-list li i {
        color: var(--primary-color);
        margin-right: 0.8rem;
        font-size: 1rem;
    }

    .add-to-cart-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-color);
        color: white;
        font-size: 1rem;
        font-weight: 600;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
        margin-top: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .add-to-cart-btn:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .add-to-cart-btn i {
        margin-right: 0.8rem;
        font-size: 1.1rem;
    }

    /* Product Tabs */
    .product-tabs {
        background-color: var(--bg-white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        margin-bottom: 3rem;
        overflow: hidden;
    }

    .tabs-header {
        display: flex;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .tab-button {
        padding: 1.2rem 1.5rem;
        background: none;
        border: none;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-light);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .tab-button.active {
        color: var(--primary-color);
    }

    .tab-button.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--primary-color);
    }

    .tab-content {
        padding: 2rem;
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .tab-content h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text-color);
    }

    .tab-content p {
        color: var(--text-light);
        line-height: 1.7;
        margin-bottom: 1rem;
    }

    /* Similar Games Section */
    .similar-games-section {
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: var(--text-color);
        text-align: center;
        position: relative;
        padding-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background-color: var(--primary-color);
        border-radius: 2px;
    }

    .similar-games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .game-card {
        background-color: var(--bg-white);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .game-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .game-card-image-container {
        position: relative;
        overflow: hidden;
        height: 150px;
    }

    .game-card-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .game-card:hover .game-card-image {
        transform: scale(1.05);
    }

    .game-card-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: var(--primary-color);
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 1rem;
        text-transform: uppercase;
    }

    .game-card-content {
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .game-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 0.5rem;
        color: var(--text-color);
        line-height: 1.3;
    }

    .game-card-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0.5rem 0 1rem;
    }

    .game-card-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-light);
        color: white;
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.6rem 1rem;
        border-radius: var(--radius);
        transition: background-color 0.3s ease;
        margin-top: auto;
        text-align: center;
    }

    .game-card-button:hover {
        background-color: var(--primary-color);
    }

    /* Scroll to top button */
    #scrollToTopBtn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: background-color 0.3s, transform 0.3s;
        z-index: 1000;
    }

    #scrollToTopBtn:hover {
        background-color: var(--primary-dark);
        transform: scale(1.1);
    }

    #scrollToTopBtn svg {
        width: 24px;
        height: 24px;
    }

    /* Alert message */
    .alert {
        padding: 1rem;
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        font-weight: 600;
        text-align: center;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .product-detail-content {
            flex-direction: column;
        }

        .product-image-section {
            width: 100%;
            max-width: 300px;
            margin: 0 auto 20px;
        }

        .product-info-section {
            padding: 0 1rem;
        }

        .product-title {
            font-size: 1.8rem;
        }

        .product-price {
            font-size: 2rem;
        }

        .tabs-header {
            overflow-x: auto;
            white-space: nowrap;
        }

        .tab-button {
            padding: 1rem;
            font-size: 0.9rem;
        }

        .similar-games-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }

    @media (max-width: 480px) {
        .product-info-section {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.5rem;
        }

        .product-price {
            font-size: 1.8rem;
        }

        .tab-content {
            padding: 1.5rem;
        }

        .similar-games-grid {
            grid-template-columns: 1fr;
        }
    }

    .platform img {
        width: 20px;
        height: auto;
        margin-right: 10px; /* Espacio entre las imágenes */
    }

    .platform-icons .platform-icon {
        width: 24px; /* Ajusta el tamaño del icono de la plataforma */
        height: 24px; /* Asegura que la altura sea igual al ancho */
        margin-right: 8px; /* Espacio entre iconos */
        object-fit: contain; /* Asegura que la imagen se ajuste sin recortar */
        vertical-align: middle; /* Alinea iconos con el texto si hay */
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 10px;
    }

    /* === Estilos copiados/ajustados de producto.php === */

    /* Estilos generales de tarjeta */
    .card {
        background-color: var(--bg-white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px; /* Ajustar padding si es necesario */
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }

    /* Contenedor principal de detalles */
    .product-detail-card .product-detail-content {
        display: flex;
        align-items: flex-start; /* Alinea elementos arriba */
        gap: 30px; /* Espacio entre columnas */
        flex-wrap: wrap; /* Permite apilar en pantallas pequeñas */
    }

    /* Sección de la imagen principal */
    .product-image-section {
        flex: 0 0 300px; /* Ancho fijo para la columna de imagen */
        max-width: 300px; /* Asegura que no exceda este ancho */
        position: relative;
        overflow: hidden;
        border-radius: var(--radius); /* Ajustar si se usaba radius-lg antes */
        box-shadow: var(--shadow); /* Añadir sombra como en producto.php */
    }

    .product-image-section .product-image {
        display: block; /* Ayuda con el dimensionamiento */
        width: 100%;
        height: auto; /* Altura automática */
        object-fit: cover; /* Asegura que cubra el área */
    }

    /* Sección de información del producto */
    .product-info-section {
        flex: 1; /* Ocupa el espacio restante */
        display: flex;
        flex-direction: column;
        /* Padding ya definido en el contenedor principal si aplica */
    }

    /* Título del producto */
    .product-info-section .product-title {
        font-size: 1.75rem; /* Ajustar tamaño de fuente */
        font-weight: 700;
        margin: 0 0 15px 0; /* Ajustar margen */
        color: var(--text-color);
        line-height: 1.3;
    }

    /* Estado del producto (Usado/Seminuevo/Nuevo) */
    .product-info-section .product-estado {
        display: inline-block; /* Para que ocupe solo el espacio necesario */
        background-color: var(--primary-color); /* Usar color primario */
        color: white;
        font-size: 0.9rem; /* Ajustar tamaño de fuente */
        font-weight: 600;
        padding: 4px 10px; /* Ajustar padding */
        border-radius: var(--radius); /* Usar radio estándar */
        margin-bottom: 15px; /* Margen inferior */
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-info-section .product-estado i {
        margin-right: 5px; /* Espacio para el icono */
    }

    /* Precio del producto */
    .product-info-section .product-price {
        font-size: 1.5rem; /* Ajustar tamaño de fuente */
        font-weight: 700;
        color: var(--primary-color); /* Usar color primario */
        margin: 0 0 15px 0; /* Ajustar margen */
    }

    /* Categoría */
    .product-info-section .category {
        background-color: var(--bg-light); /* Usar fondo ligero */
        padding: 8px 15px; /* Ajustar padding */
        border-radius: var(--radius); /* Usar radio estándar */
        margin: 0 0 15px 0; /* Ajustar margen */
        display: inline-block;
        font-size: 0.9rem; /* Ajustar tamaño de fuente */
        color: var(--text-color);
    }

    /* Subido por (uploader) */
    .product-info-section .product-uploader {
        font-size: 0.9rem; /* Ajustar tamaño de fuente */
        color: var(--text-light); /* Usar color de texto ligero */
        margin: 0 0 15px 0; /* Ajustar margen */
    }

    /* Contenedor de iconos de plataforma */
    .product-info-section .platform-icons {
        display: flex; /* Para alinear los iconos en fila */
        align-items: center;
        margin-bottom: 20px; /* Margen inferior */
    }

    .product-info-section .platform-icons .platform-icon {
        width: 24px; /* Tamaño del icono */
        height: 24px;
        margin-right: 10px; /* Espacio entre iconos */
        object-fit: contain;
    }

    /* Grupo de botones */
    .product-info-section .button-group {
        display: flex; /* Para alinear los botones en fila */
        gap: 10px; /* Espacio entre botones */
        margin-top: 20px; /* Margen superior */
    }

    /* Estilos de botones (clases .btn, .btn-primary, .btn-wishlist ya deberían existir en tus estilos generales) */
    .product-info-section .add-to-cart-btn {
        display: inline-flex; /* Para alinear icono y texto */
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem; /* Ajustar padding */
        border-radius: var(--radius);
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }

    .product-info-section .add-to-cart-btn i {
        margin-right: 5px; /* Espacio para el icono en el botón */
    }

    .product-info-section .add-to-cart-btn.wishlist-btn {
        background-color: #e74c3c; /* Color para wishlist */
        color: white;
    }

    .product-info-section .add-to-cart-btn.wishlist-btn:hover {
        background-color: #c0392b; /* Color hover wishlist */
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    /* Estilos responsivos específicos para esta sección */
    @media (max-width: 768px) {
        .product-detail-card .product-detail-content {
            flex-direction: column; /* Apilar columnas */
            gap: 20px; /* Ajustar espacio apilado */
        }

        .product-image-section {
            width: 100%; /* Ancho completo */
            max-width: 300px; /* Limita el ancho incluso en pantalla pequeña */
            margin: 0 auto; /* Centrar imagen apilada */
        }

        .product-info-section {
            padding: 0; /* Eliminar padding si el contenedor principal ya lo tiene */
        }

        .product-info-section .button-group {
            flex-direction: column; /* Apilar botones */
            gap: 10px;
        }
    }
</style>

<div class="product-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Inicio</a>
        <span class="separator">/</span>
        <a href="segunda_mano.php">Segunda Mano</a>
        <span class="separator">/</span>
        <span><?php echo htmlspecialchars($producto['nombre']); ?></span>
    </div>

    <?php if (isset($_GET['already_in_cart'])): ?>
    <div class="alert alert-danger">
        Este producto ya está en tu carrito.
    </div>
    <?php endif; ?>

    <!-- Product Detail Card -->
    <div class="product-detail-card">
        <div class="product-detail-content">
            <!-- Product Image -->
            <div class="product-image-section">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
            </div>
            
            <!-- Product Info -->
            <div class="product-info-section">
                <h1 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                
                <span class="product-estado">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($producto['estado'] ?? 'Usado'); ?>
                </span>
                
                <p class="product-price">€<?php echo number_format($producto['precio'], 2); ?></p>
                
                <div class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></div>
                <p class="product-uploader">Subido por: <?php echo htmlspecialchars($producto['usuario_nombre']); ?></p>

                <!-- Mostrar plataformas -->
                <div class="platform-icons">
                    <?php
                    // Mostrar las imágenes de las plataformas desde las columnas separadas
                    $plataformas_mostradas = false;
                    for ($i = 1; $i <= 4; $i++) {
                        $columna_plataforma = 'plataforma' . $i;
                        if (!empty($producto[$columna_plataforma])) {
                            echo '<img src="' . htmlspecialchars($producto[$columna_plataforma]) . '" alt="Plataforma" class="platform-icon">';
                            $plataformas_mostradas = true;
                        }
                    }
                    // Mostrar mensaje si no hay plataformas
                    if (!$plataformas_mostradas) {
                        echo '<p>No hay plataformas disponibles.</p>';
                    }
                    ?>
                </div>

                <p class="product-description">
                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                </p>
                
                <div class="button-group">
                    <form method="POST" action="agregar_al_carrito.php" style="display:inline-block; margin:0;">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                        <button type="submit" class="add-to-cart-btn">Añadir al Carrito</button>
                    </form>
                    <!-- Asumiendo que tienes un script agregar_lista_deseos_segunda_mano.php -->
                    <form method="POST" action="agregar_lista_deseos.php" style="display:inline-block; margin:0;">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <input type="hidden" name="tipo" value="segunda_mano">
                        <button type="submit" class="add-to-cart-btn wishlist-btn"><i class="fas fa-heart"></i> Añadir a Lista de Deseos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="product-tabs">
        <div class="tabs-header">
            <button class="tab-button active" data-tab="description">Descripción</button>
            <?php if (!empty($producto['acerca_de'])): ?>
            <button class="tab-button" data-tab="about">Acerca del juego</button>
            <?php endif; ?>
            <?php if (!empty($producto['descripcion_adicional'])): ?>
            <button class="tab-button" data-tab="additional">Información adicional</button>
            <?php endif; ?>
            <?php if (!empty($producto['comentario'])): ?>
            <button class="tab-button" data-tab="comments">Comentarios</button>
            <?php endif; ?>
        </div>
        
        <div id="description" class="tab-content active">
            <h3>Descripción detallada</h3>
            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
        </div>
        
        <?php if (!empty($producto['acerca_de'])): ?>
        <div id="about" class="tab-content">
            <h3>Acerca del juego</h3>
            <p><?php echo htmlspecialchars($producto['acerca_de']); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($producto['descripcion_adicional'])): ?>
        <div id="additional" class="tab-content">
            <h3>Información adicional</h3>
            <p><?php echo htmlspecialchars($producto['descripcion_adicional']); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($producto['comentario'])): ?>
        <div id="comments" class="tab-content">
            <h3>Comentarios del vendedor</h3>
            <p><?php echo htmlspecialchars($producto['comentario']); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Similar Games Section -->
    <?php if ($juegos_similares): ?>
    <div class="similar-games-section">
        <h2 class="section-title">Juegos similares</h2>
        <div class="similar-games-grid">
            <?php foreach ($juegos_similares as $juego): ?>
            <a href="detalle_segunda_mano.php?id=<?php echo $juego['id']; ?>" class="game-card">
                <div class="game-card-image-container">
                    <img src="<?php echo htmlspecialchars($juego['imagen']); ?>" alt="<?php echo htmlspecialchars($juego['nombre']); ?>" class="game-card-image">
                    <span class="game-card-badge"><?php echo htmlspecialchars($juego['estado'] ?? 'Usado'); ?></span>
                </div>
                <div class="game-card-content">
                    <h3 class="game-card-title"><?php echo htmlspecialchars($juego['nombre']); ?></h3>
                    <p class="game-card-price">€<?php echo number_format($juego['precio'], 2); ?></p>
                    <span class="game-card-button">Ver detalles</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Botón scroll arriba -->
<button id="scrollToTopBtn" aria-label="Volver arriba">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
       stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
    <polyline points="18 15 12 9 6 15"></polyline>
  </svg>
</button>

<!-- Script JS -->
<script>
    // Scroll to top button
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

    // Tabs functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Show corresponding content
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>