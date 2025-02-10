<?php
session_start();
require_once 'config/database.php';

try {
    // Obtener categorías
    $stmt = $pdo->prepare("SELECT id_categoria, nombre FROM categorias ORDER BY nombre");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todos los productos
    $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre 
                          FROM productos p 
                          INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
                          ORDER BY p.id_producto DESC");
    $stmt->execute();
    $productos_destacados = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = 'Error al cargar los datos';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGames - Tu tienda de videojuegos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
        }

        .nav-links a:hover {
            background-color: #555;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><a href="index.php">MGames</a></div>
        <div class="nav-links">
            <a href="index.php" class="active">Inicio</a>
            <?php if (!empty($categorias)): ?>
                <div class="dropdown">
                    <button class="dropbtn">Categorías</button>
                    <div class="dropdown-content">
                        <?php foreach($categorias as $cat): ?>
                            <a href="categoria.php?id=<?php echo htmlspecialchars($cat['id_categoria']); ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <a href="segunda_mano.php">Segunda Mano</a>
            <?php if(isset($_SESSION['usuario'])): ?>
                <a href="perfil.php">Mi Perfil</a>
                <?php if ($_SESSION['usuario']['rol'] === 'ADMIN'): ?>
                    <a href="panel_admin.php" class="btn">Panel Admin</a>
                <?php endif; ?>
                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar Sesión</a>
                <a href="register.php">Registrarse</a>
            <?php endif; ?>
        </div>
        <div class="cart-icon">
            <a href="carrito.php"><i class="fas fa-shopping-cart" style="color: white;"></i></a>
        </div>
    </nav>

    <div class="content">
        <!-- Hero Section -->
        <header class="hero">
            <h1>Bienvenido a MGames</h1>
            <p>Tu destino para los mejores videojuegos</p>
        </header>

        <?php if (!empty($categorias)): ?>
            <!-- Categorías -->
            <section class="categories">
                <h2>Categorías</h2>
                <div class="categories-grid">
                    <?php foreach($categorias as $cat): ?>
                        <div class="category-card">
                            <h3><?php echo htmlspecialchars($cat['nombre']); ?></h3>
                            <a href="categoria.php?id=<?php echo htmlspecialchars($cat['id_categoria']); ?>" 
                               class="btn">Ver Juegos</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($productos_destacados)): ?>
            <!-- Productos Destacados -->
            <section class="featured-products">
                <h2>Juegos Destacados</h2>
                <div class="products-grid">
                    <?php foreach($productos_destacados as $producto): ?>
                        <div class="product-card">
                            <img src="<?php echo $producto['imagen'] ?? 'images/default.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                            <a href="producto.php?id=<?php echo $producto['id_producto']; ?>" class="btn">
                                Ver Detalles
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Sobre MGames</h4>
                <p>Tu tienda de confianza para videojuegos.</p>
            </div>
            <div class="footer-section">
                <h4>Enlaces Útiles</h4>
                <ul>
                    <li><a href="soporte.php">Soporte</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 MGames. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html> 