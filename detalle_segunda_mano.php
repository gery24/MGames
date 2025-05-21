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
    $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre FROM segunda_mano p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
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

// Verificar si el usuario es admin para añadir la clase 'admin' al body
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
$bodyClass = $isAdmin ? 'admin' : '';

// Mostrar mensaje si el producto ya está en el carrito
if (isset($_GET['already_in_cart'])) {
    echo '<div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i> Este producto ya está en tu carrito.</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Segunda Mano</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    /* Variables para mantener consistencia en todo el sitio */
    :root {
        --primary-color: #4f46e5;
        --primary-dark: #4338ca;
        --secondary-color: #6366f1;
        --accent-color: #818cf8;
        --text-color: #1f2937;
        --text-light: #6b7280;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius: 0.5rem;
        --admin-color: #ff0000;
        --admin-dark: #cc0000;
        --admin-bg-light: #fff0f0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--text-color);
        line-height: 1.6;
        background-color: var(--bg-light);
        margin: 0;
        padding: 0;
    }

    .product-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 20px;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    /* Estilos de tarjeta principal */
    .card {
        background-color: var(--bg-white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 2rem;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }

    /* Estilos del producto */
    .product-header {
        display: flex;
        align-items: flex-start;
        gap: 2.5rem;
    }

    .product-image {
        width: 350px;
        height: auto;
        border-radius: var(--radius);
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Contenedor de la imagen principal para posicionar el distintivo */
    .product-image-container {
        position: relative;
        width: 350px; /* Asegurar que el contenedor tenga el mismo ancho que la imagen */
        height: auto; /* Asegurar que la altura se ajuste al contenido */
    }

    .product-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .product-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-color);
        line-height: 1.3;
    }

    .price {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .badges-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 0.5rem 0;
    }

    .category {
        background-color: var(--bg-light);
        padding: 0.5rem 1rem;
        border-radius: var(--radius);
        display: inline-block;
        font-size: 0.9rem;
        color: var(--text-color);
        font-weight: 500;
    }

    /* Estado Badge */
    .estado-badge {
        background-color: var(--primary-color); /* Usar color primario */
        color: white;
        padding: 0.5rem 1rem;
        border-radius: var(--radius);
        display: inline-block;
        font-weight: 500;
        font-size: 0.9rem;
        text-align: center;
    }

    /* Plataformas */
    .platform {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0.5rem 0;
    }

    .platform img {
        width: 24px;
        height: auto;
        border-radius: 4px;
    }

    /* Botones */
    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius);
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 1rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .btn-wishlist {
        background-color: #e74c3c;
        color: white;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-wishlist:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    /* Cantidad selector */
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .quantity-selector label {
        font-weight: 500;
        color: var(--text-color);
    }

    .quantity-selector input {
        width: 70px;
        padding: 0.5rem;
        border-radius: var(--radius);
        border: 1px solid #ddd;
        text-align: center;
        font-size: 1rem;
    }

    /* Secciones */
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        color: var(--text-color);
        position: relative;
    }

    .section-title:after {
        content: '';
        display: block;
        width: 50px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        margin: 0.5rem 0 0;
        border-radius: 2px;
    }

    .section-content {
        line-height: 1.7;
        color: var(--text-color);
        font-size: 1.05rem;
    }

    /* Juegos similares */
    .similar-games-grid {
        display: flex;
        gap: 1.25rem;
        overflow-x: auto;
        padding: 0.5rem 0 1.5rem;
        scrollbar-width: thin;
    }

    .similar-games-grid::-webkit-scrollbar {
        height: 8px;
    }

    .similar-games-grid::-webkit-scrollbar-thumb {
        background-color: #c1c1c1;
        border-radius: 4px;
    }

    .game-card {
        position: relative;
        border-radius: var(--radius);
        overflow: hidden;
        background: var(--bg-white);
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: var(--shadow);
        flex: 0 0 250px;
        max-width: 250px;
    }

    .game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .game-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .game-info {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .game-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: var(--text-color);
        line-height: 1.4;
    }

    .game-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .view-details {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: var(--radius);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: background-color 0.3s, transform 0.3s;
        margin-top: 0.5rem;
    }

    .view-details:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Alertas */
    .alert {
        padding: 1rem;
        border-radius: var(--radius);
        margin: 1rem auto;
        max-width: 1200px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-warning {
        background-color: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border-left: 4px solid #f59e0b;
    }

    /* Estilos responsivos */
    @media (max-width: 768px) {
        .product-header {
            flex-direction: column;
        }

        .product-image, .product-image-container {
            width: 100%;
            max-width: 350px;
            margin: 0 auto 1.5rem;
        }

        .button-group {
            flex-direction: column;
        }

        .game-card {
            flex: 0 0 200px;
            max-width: 200px;
        }
    }

    /* Botón scroll arriba */
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

    /* Estilos para administradores */
    body.admin .section-title:after {
        background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
    }

    body.admin .btn-primary {
        background-color: var(--admin-color);
    }

    body.admin .btn-primary:hover {
        background-color: var(--admin-dark);
    }

    body.admin .estado-badge {
        background-color: var(--admin-color);
    }

    body.admin .view-details {
        background-color: var(--admin-color);
    }

    body.admin .view-details:hover {
        background-color: var(--admin-dark);
    }

    body.admin #scrollToTopBtn {
        background-color: var(--admin-color);
    }

    body.admin #scrollToTopBtn:hover {
        background-color: var(--admin-dark);
    }

    .avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

    </style>
</head>
<body class="<?php echo $bodyClass; ?>">
    <div class="product-container">
        <!-- Tarjeta principal del producto -->
        <div class="card">
            <div class="product-header">
                <div class="product-image-container">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
                </div>
                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                    <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>

                    <!-- Mostrar estado y categoría -->
                    <div class="badges-container">
                        <div class="estado-badge">
                            <?php echo htmlspecialchars($producto['estado'] ?? 'Usado'); ?>
                        </div>
                        <div class="category">
                            <?php echo htmlspecialchars($producto['categoria_nombre']); ?>
                        </div>
                    </div>

                    <!-- Mostrar plataformas -->
                    <?php
                    // Mostrar las plataformas
                    $plataformas = [];
                    if (!empty($producto['plataforma1'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma1']);
                    }
                    if (!empty($producto['plataforma2'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma2']);
                    }
                    if (!empty($producto['plataforma3'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma3']);
                    }
                    if (!empty($producto['plataforma4'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma4']);
                    }

                    // Mostrar las imágenes de las plataformas solo si hay plataformas disponibles
                    if (!empty($plataformas)): ?>
                    <div class="platform">
                        <?php foreach ($plataformas as $plataforma): ?>
                            <img src="<?php echo $plataforma; ?>" alt="Plataforma" class="platform-img">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="agregar_al_carrito.php">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                        <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($producto['imagen']); ?>">
                        <input type="hidden" name="tipo_producto" value="segunda_mano">
                        <input type="hidden" name="cantidad" value="1">
                        <input type="hidden" name="redirect_to" value="carrito.php">
                        
                        <!-- Selector de cantidad -->
                        <div class="quantity-selector">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="10">
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Añadir al Carrito
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Acerca del juego (si existe) -->
        <?php if (isset($producto['acerca_de']) && !empty($producto['acerca_de'])): ?>
        <div class="card">
            <h2 class="section-title">Acerca del juego</h2>
            <div class="section-content">
                <?php echo htmlspecialchars($producto['acerca_de']); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Descripción (si existe) -->
        <?php if (!empty($producto['descripcion'])): ?>
        <div class="card">
            <h2 class="section-title">Descripción</h2>
            <div class="section-content">
                <?php echo htmlspecialchars($producto['descripcion']); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Descripción adicional (si existe) -->
        <?php if (!empty($producto['descripcion_adicional'])): ?>
        <div class="card">
            <h2 class="section-title">Descripción adicional</h2>
            <div class="section-content">
                <?php echo htmlspecialchars($producto['descripcion_adicional']); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Comentarios adicionales (si existe) -->
        <?php if (!empty($producto['comentario'])): ?>
        <div class="card">
            <h2 class="section-title">Comentarios adicionales</h2>
            <div class="section-content">
                <?php echo htmlspecialchars($producto['comentario']); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Juegos similares (si existen) -->
        <?php if (!empty($juegos_similares)): ?>
        <div class="card">
            <h2 class="section-title">Juegos similares</h2>
            <div class="similar-games-grid">
                <?php foreach ($juegos_similares as $juego): ?>
                    <div class="game-card">
                        <img 
                            src="<?php echo htmlspecialchars($juego['imagen']); ?>" 
                            alt="<?php echo htmlspecialchars($juego['nombre']); ?>"
                            class="game-image"
                        >
                        <div class="game-info">
                            <h3 class="game-title"><?php echo htmlspecialchars($juego['nombre']); ?></h3>
                            <p class="game-price">€<?php echo number_format($juego['precio'], 2); ?></p>
                            <a href="detalle_segunda_mano.php?id=<?php echo $juego['id']; ?>" class="view-details">
                                <i class="fas fa-eye"></i> Ver detalles
                            </a>
                        </div>
                    </div>
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

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
