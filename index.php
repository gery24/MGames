<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGames - Tu tienda de videojuegos</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo"><a href="index.php">MGames</a></div>
        <div class="nav-right">
            <a href="segunda_mano.php" class="btn">Segunda Mano</a>
            <?php if(isset($_SESSION['usuario'])): ?>
                <a href="perfil.php">Mi Perfil</a>
                <?php if ($_SESSION['usuario']['rol'] === 'ADMIN'): ?>
                    <a href="panel_admin.php" class="btn">Panel Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="btn">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="btn">Iniciar Sesión</a>
                <a href="register.php" class="btn">Registrarse</a>
            <?php endif; ?>
            <div class="cart-icon">
                <a href="carrito.php"><i class="fas fa-shopping-cart" style="color: white;"></i></a>
            </div>
        </div>
    </nav>

    <div class="content">
        <!-- Hero Section -->
        <header class="hero">
            <h1>Bienvenido a MGames</h1>
            <p>Tu destino para los mejores videojuegos</p>
        </header>

        <!-- Filtro de Categorías -->
        <section class="filter">
            <h2>Filtrar por Categoría</h2>
            <form method="GET" action="index.php">
                <select name="categoria" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['id_categoria']); ?>" 
                            <?php echo (isset($categoria_id) && $categoria_id == $cat['id_categoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </section>

        <!-- Productos -->
        <section class="featured-products">
            <h2>Productos</h2>
            <div class="products-grid">
                <?php foreach($productos as $producto): ?>
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