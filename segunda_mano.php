<?php
session_start();
require_once 'config/database.php';

try {
    // Obtener productos de segunda mano
    $stmt = $pdo->query("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM segunda_mano p 
        LEFT JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.id DESC
    ");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segunda Mano - MGames</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="css/segunda_mano.css"> <!-- Estilos específicos -->
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
            <h1>Bienvenido a MGames de Segunda Mano</h1>
            <p>Encuentra grandes títulos a precios increíbles</p>
        </header>

        <section class="featured-products">
            <h2>Productos Disponibles</h2>
            <div class="products-grid">
                <?php foreach($productos as $producto): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                            <p class="estado"><?php echo htmlspecialchars($producto['estado'] ?? 'Usado'); ?></p>
                            <a href="detalle_segunda_mano.php?id=<?php echo $producto['id']; ?>" class="btn">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="agregar_segunda_mano.php" class="btn">Añadir Juego de Segunda Mano</a>
        </section>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html> 