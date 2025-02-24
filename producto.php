<?php
session_start();
require_once 'config/database.php';

// Verificar si se ha pasado un ID de producto
if (!isset($_GET['id'])) {
    die("Error: ID de producto no especificado.");
}

$id = $_GET['id'];

try {
    // Obtener información del producto
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Error: Producto no encontrado.");
    }

    // Obtener comentarios del producto
    $stmt_comments = $pdo->prepare("SELECT * FROM comentarios WHERE producto_id = ?");
    $stmt_comments->execute([$id]);
    $comentarios = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

    // Obtener productos relacionados
    $stmt_related = $pdo->prepare("
        SELECT * FROM productos 
        WHERE categoria_id = ? AND id != ? 
        LIMIT 4
    ");
    $stmt_related->execute([$producto['categoria_id'], $id]);
    $productos_relacionados = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - MGames</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/segunda_mano.css">
    <style>
        .product-details {
            display: flex;
            align-items: flex-start;
            margin-top: 20px;
        }

        .product-details img {
            width: 400px;
            height: auto;
            margin-right: 20px;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-info h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .product-info p {
            font-size: 1.3rem;
            margin: 5px 0;
        }

        .add-to-cart-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            margin-top: 15px;
            transition: background-color 0.3s;
        }

        .add-to-cart-btn:hover {
            background-color: var(--secondary-color);
        }

        .reviews {
            margin-top: 30px;
        }

        .review {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .related-products {
            margin-top: 30px;
        }

        .related-products h2 {
            margin-bottom: 15px;
        }

        .related-products .product-card {
            display: inline-block;
            width: 200px;
            margin-right: 10px;
        }

        .related-products .product-card img {
            width: 100%;
            height: auto;
        }

        .related-products .product-card p {
            color: black;
            margin: 5px 0;
            background: none;
        }

        header.hero h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: black;
            background-color: transparent;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <div class="logo">
                <a href="index.php">MGames</a>
            </div>
        </div>
        <div class="nav-right">
            <div class="menu-container">
                <button class="menu-button">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="menu-dropdown">
                    <a href="index.php" class="menu-item">Inicio</a>
                    <a href="segunda_mano.php" class="menu-item">Segunda Mano</a>
                    <a href="soporte.php" class="menu-item">Soporte</a>
                    <a href="contacto.php" class="menu-item">Contacto</a>
                    <?php if(!isset($_SESSION['usuario'])): ?>
                        <a href="login.php" class="menu-item">Iniciar Sesión</a>
                        <a href="register.php" class="menu-item">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="carrito.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <span class="cart-count"><?php echo count($_SESSION['carrito']); ?></span>
                <?php endif; ?>
            </a>
            <?php if(isset($_SESSION['usuario'])): ?>
                <div class="user-menu">
                    <a class="user-avatar" id="user-logo">
                        <?php echo strtoupper(substr($_SESSION['usuario']['nombre'], 0, 1)); ?>
                    </a>
                    <div class="dropdown-menu">
                        <a href="perfil.php" class="menu-item">Ajustes</a>
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="content">
        <header class="hero">
            <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
        </header>

        <div class="product-details">
            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <div class="product-info">
                <h2>Detalles del Producto</h2>
                <p><strong>Precio:</strong> €<?php echo number_format($producto['precio'], 2); ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <p><strong>Requisitos para jugar:</strong> <?php echo htmlspecialchars($producto['requisitos'] ?? 'No especificados'); ?></p>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($producto['estado'] ?? 'Nuevo'); ?></p>
                <form method="POST" action="agregar_al_carrito.php">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <button type="submit" class="add-to-cart-btn">Añadir al Carrito</button>
                </form>
            </div>
        </div>

        <div class="reviews">
            <h2>Reseñas de Clientes</h2>
            <?php foreach ($comentarios as $comentario): ?>
                <div class="review">
                    <p><strong><?php echo htmlspecialchars($comentario['usuario']); ?></strong> hace <?php echo htmlspecialchars($comentario['fecha']); ?></p>
                    <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="related-products">
            <h2>Productos Relacionados</h2>
            <div class="products-grid">
                <?php foreach ($productos_relacionados as $producto_relacionado): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($producto_relacionado['imagen']); ?>" alt="<?php echo htmlspecialchars($producto_relacionado['nombre']); ?>">
                        <p><?php echo htmlspecialchars($producto_relacionado['nombre']); ?></p>
                        <p>€<?php echo number_format($producto_relacionado['precio'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html> 