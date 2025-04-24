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
    
    // Limitar a 20 productos
    $query .= " LIMIT 20";

    // Ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar el número de productos por categoría
    $stmt = $pdo->query("SELECT c.id, c.nombre, COUNT(p.id) as count 
                        FROM categorias c 
                        LEFT JOIN productos p ON c.id = p.categoria_id 
                        GROUP BY c.id");
    $categorias_count = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// Título de la página
$titulo = "MGames - Tu tienda de videojuegos";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header Mejorado -->
    <header class="site-header">
        <div class="container header-container">
            <a href="index.php" class="logo">
                <span>MGames</span>
            </a>
            
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="todos_productos.php">Tienda</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <div class="search-container">
                    <button class="search-button" id="search-toggle">
                        <i class="fas fa-search"></i>
                    </button>
                    <form class="search-form" method="GET" action="index.php" id="search-form" style="display: none;">
                        <select name="categoria" class="filter-select">
                            <option value="">Todas las categorías</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['id']); ?>">
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="buscar" placeholder="Buscar productos..." required>
                        <button type="submit" class="btn">Buscar</button>
                    </form>
                </div>
                <a href="lista_deseos.php" class="header-icon">
                    <i class="fas fa-heart"></i>
                    <?php if(isset($_SESSION['wishlist_count']) && $_SESSION['wishlist_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['wishlist_count']; ?></span>
                <?php endif; ?>
                </a>
                <a href="carrito.php" class="header-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['cart_count']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="cartera.php" class="header-icon">
                    <i class="fas fa-wallet"></i>
                    <?php if(isset($_SESSION['user_balance'])): ?>
                        <span class="balance-indicator"><?php echo number_format($_SESSION['user_balance'], 2); ?>€</span>
                    <?php endif; ?>
                </a>
                <div class="user-profile">
                    <?php if(isset($_SESSION['usuario'])): ?>
                        <div class="profile-dropdown">
                            <button class="profile-button">
                                <div class="avatar-circle">
                                    <?php 
                                    // Obtener la primera letra del nombre de usuario
                                    $initial = strtoupper(substr($_SESSION['usuario']['nombre'], 0, 1));
                                    echo $initial;
                                    ?>
                                </div>
                                <span class="username"><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-content">
                                <a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a>
                                <a href="pedidos.php"><i class="fas fa-box"></i> Mis Pedidos</a>
                                <a href="configuracion.php"><i class="fas fa-cog"></i> Configuración</a>
                                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                            </div>
                        </div>
            <?php else: ?>
                        <div class="auth-buttons">
                            <a href="login.php" class="btn btn-sm btn-outline">Iniciar Sesión</a>
                            <a href="register.php" class="btn btn-sm btn-primary">Registrarse</a>
                        </div>
            <?php endif; ?>
            </div>
                <button id="menuToggle" class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
        </div>
        </div>
    </header>

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
                    <a href="segunda_mano.php" class="btn btn-primary">Ver Productos de Segunda Mano</a>
                    <a href="lista_deseos.php" class="btn btn-transparent">Lista de Deseos</a>
                </div>
            </div>
        </div>
        </header>

    <!-- Filtros -->
    <section class="filters">
        <div class="container">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="categoria">Filtrar por Categoría</label>
                    <div class="select-wrapper">
                        <select name="categoria" id="categoria" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                                        <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-group">
                    <label for="precio">Filtrar por Precio</label>
                    <div class="select-wrapper">
                        <select name="precio" id="precio" class="filter-select" onchange="this.form.submit()">
                            <option value="">Todos los precios</option>
                            <option value="0-30" <?php echo $rango_precio == '0-30' ? 'selected' : ''; ?>>Menos de 30€</option>
                            <option value="30-40" <?php echo $rango_precio == '30-40' ? 'selected' : ''; ?>>30€ - 40€</option>
                            <option value="40-50" <?php echo $rango_precio == '40-50' ? 'selected' : ''; ?>>40€ - 50€</option>
                            <option value="50-60" <?php echo $rango_precio == '50-60' ? 'selected' : ''; ?>>50€ - 60€</option>
                            <option value="60+" <?php echo $rango_precio == '60+' ? 'selected' : ''; ?>>Más de 60€</option>
                        </select>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-group">
                    <label for="buscar">Buscar Juegos</label>
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="buscar" name="buscar" placeholder="Buscar juegos..." 
                               value="<?php echo htmlspecialchars($busqueda ?? ''); ?>" class="filter-input">
                        <button type="submit" class="search-button">Buscar</button>
                    </div>
                </div>
            </form>
            <?php if ($categoria_id || $rango_precio || $busqueda): ?>
                <a href="index.php" class="clear-filters">
                    <i class="fas fa-times-circle"></i> Limpiar filtros
                </a>
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
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>"
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="category-badge">
                            <?php echo htmlspecialchars($producto['categoria_nombre']); ?>
                        </div>
                    </div>
                    <div class="product-card-content">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                        <div class="product-actions">
                            <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn">Ver Detalles</a>
                        </div>
                    </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

<style>
/* Estilos generales */
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
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: var(--text-color);
    line-height: 1.6;
    background-color: var(--bg-light);
    margin: 0;
    padding: 0;
}

.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Botones */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius);
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
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

.btn-transparent {
    background-color: transparent;
    color: white;
    border: 2px solid white;
}

.btn-transparent:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
}

.btn-outline {
    background-color: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-outline:hover {
    background-color: rgba(79, 70, 229, 0.1);
    transform: translateY(-2px);
}

/* Header */
.site-header {
    background-color: white;
    box-shadow: var(--shadow);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
}

.logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    text-decoration: none;
    display: flex;
    align-items: center;
}

.logo img {
    height: 40px;
    margin-right: 0.5rem;
}

.nav-links {
    display: flex;
    gap: 1.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-links li a {
    color: var(--text-color);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
    padding: 0.5rem 0;
    position: relative;
}

.nav-links li a:hover {
    color: var(--primary-color);
}

.nav-links li a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--primary-color);
    transition: width 0.3s;
}

.nav-links li a:hover::after {
    width: 100%;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    color: var(--text-color);
    font-size: 1.25rem;
    transition: color 0.3s;
    position: relative;
    text-decoration: none;
}

.header-icon:hover {
    color: var(--primary-color);
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: var(--primary-color);
    color: white;
    font-size: 0.75rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.balance-indicator {
    position: absolute;
    top: -8px;
    right: -20px;
    background-color: #10b981;
    color: white;
    font-size: 0.75rem;
    padding: 0.1rem 0.4rem;
    border-radius: 0.25rem;
    white-space: nowrap;
}

.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: var(--text-color);
    font-size: 1.5rem;
    cursor: pointer;
}

/* User Profile Dropdown */
.user-profile {
    position: relative;
}

.profile-dropdown {
    position: relative;
}

.profile-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: var(--radius);
    transition: background-color 0.3s;
}

.profile-button:hover {
    background-color: var(--bg-light);
}

.avatar-img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
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

.profile-button i.fa-user-circle {
    font-size: 1.75rem;
    color: var(--text-light);
}

.username {
    font-weight: 500;
    color: var(--text-color);
    max-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dropdown-content {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: var(--shadow);
    border-radius: var(--radius);
    padding: 0.5rem 0;
    z-index: 100;
    display: none;
}

.profile-dropdown:hover .dropdown-content {
    display: block;
}

.dropdown-content a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: var(--text-color);
    text-decoration: none;
    transition: background-color 0.3s;
}

.dropdown-content a:hover {
    background-color: var(--bg-light);
    color: var(--primary-color);
}

.dropdown-content a i {
    width: 20px;
    text-align: center;
}

/* Auth Buttons */
.auth-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Hero Section */
.hero {
    position: relative;
    height: 600px;
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
    /*background: linear-gradient(to right, rgba(79, 70, 229, 0.8), rgba(99, 102, 241, 0.7));*/
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
    font-size: 3.5rem;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    animation: fadeInDown 1s ease-out;
}

.hero-content p {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    max-width: 600px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    animation: fadeInUp 1s ease-out 0.3s;
    animation-fill-mode: both;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
    animation: fadeInUp 1s ease-out 0.6s;
    animation-fill-mode: both;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Filters Section */
.filters {
    background-color: var(--bg-white);
    padding: 2rem 0;
    box-shadow: var(--shadow);
    position: relative;
    z-index: 20;
}

.filters-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-color);
    font-size: 0.9rem;
}

.select-wrapper {
    position: relative;
}

.select-wrapper i {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
    pointer-events: none;
}

.filter-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: var(--radius);
    background-color: white;
    appearance: none;
    font-size: 1rem;
    color: var(--text-color);
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

.search-input-group {
    position: relative;
    display: flex;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
}

.filter-input {
    flex: 1;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: var(--radius) 0 0 var(--radius);
    font-size: 1rem;
    color: var(--text-color);
}

.filter-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

.search-button {
    padding: 0.75rem 1.5rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0 var(--radius) var(--radius) 0;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-button:hover {
    background-color: var(--primary-dark);
}

.clear-filters {
    display: inline-flex;
    align-items: center;
    margin-top: 1rem;
    color: var(--text-light);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.clear-filters i {
    margin-right: 0.5rem;
}

.clear-filters:hover {
    color: var(--primary-color);
}

/* Products Section */
.featured-products {
    padding: 4rem 0;
    max-width: 1200px;
    margin: 0 auto;
}

.featured-products h2, .categories-section h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
}

.featured-products h2:after, .categories-section h2:after {
    content: '';
    display: block;
    width: 50px;
    height: 4px;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    margin: 0.5rem auto 0;
    border-radius: 2px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

/* Estilos para las tarjetas de productos */
.product-card {
    position: relative;
    background-color: white;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Contenedor de la imagen con altura fija */
.product-image-container {
    position: relative;
    height: 220px; /* Altura fija para todas las imágenes */
    overflow: hidden;
}

.product-card img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Asegura que la imagen cubra todo el espacio sin distorsionarse */
    display: block;
    transition: transform 0.3s ease;
}

.product-card:hover img {
    transform: scale(1.05);
}

/* Estilo para la categoría */
.category-badge {
    position: absolute;
    bottom: 0;
    left: 0;
    background: linear-gradient(to right, #4f46e5, #6366f1);
    color: white;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0 0.5rem 0 0;
    z-index: 5;
}

/* Estilo para el icono de favoritos */
.wishlist-icon {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background-color: white;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    z-index: 10;
    transition: all 0.3s ease;
}

.wishlist-icon i {
    color: #d1d5db;
    transition: color 0.3s ease;
    font-size: 1.25rem;
}

.wishlist-icon:hover {
    transform: scale(1.1);
}

.wishlist-icon:hover i {
    color: #ef4444;
}

/* Contenido de la tarjeta */
.product-card-content {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1; /* Hace que el contenido ocupe todo el espacio disponible */
}

.product-card-content h3 {
    font-size: 1rem;
    margin: 0 0 0.75rem;
    line-height: 1.4;
    height: 2.8rem; /* Altura fija para los títulos */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #4f46e5;
    margin: 0.5rem 0;
}

/* Acciones del producto */
.product-actions {
    margin-top: auto; /* Empuja el botón hacia abajo */
    display: flex;
    justify-content: center; /* Centra el botón horizontalmente */
    width: 100%; /* Asegúrate de que el contenedor ocupe todo el ancho */
}

.product-actions .btn {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    background-color: #4f46e5;
    color: white;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.3s ease, transform 0.3s ease;
    border: none;
}

.product-actions .btn:hover {
    background-color: #4338ca;
    transform: translateY(-2px);
}

/* Categories Section */
.categories-section {
    padding: 4rem 0;
    background-color: var(--bg-white);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.category-card {
    height: 180px;
    border-radius: var(--radius);
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: flex-end;
    text-decoration: none;
    color: white;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
    z-index: 1;
}

.category-content {
    position: relative;
    z-index: 2;
    padding: 1.5rem;
    width: 100%;
}

.category-card h3 {
    font-size: 1.25rem;
    margin: 0 0 0.5rem;
    font-weight: 600;
}

.category-card p {
    font-size: 0.875rem;
    margin: 0;
    opacity: 0.9;
}

/* Colores para las categorías */
.from-red-500.to-orange-500 {
    background: linear-gradient(135deg, #ef4444, #f97316);
}

.from-blue-500.to-indigo-500 {
    background: linear-gradient(135deg, #3b82f6, #6366f1);
}

.from-green-500.to-emerald-500 {
    background: linear-gradient(135deg, #22c55e, #10b981);
}

.from-yellow-500.to-amber-500 {
    background: linear-gradient(135deg, #eab308, #f59e0b);
}

.from-purple-500.to-pink-500 {
    background: linear-gradient(135deg, #a855f7, #ec4899);
}

.from-indigo-500.to-purple-500 {
    background: linear-gradient(135deg, #6366f1, #a855f7);
}

/* Newsletter Section */
.newsletter {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.newsletter h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.newsletter p {
    max-width: 600px;
    margin: 0 auto 2rem;
    opacity: 0.9;
}

.newsletter-form {
    max-width: 500px;
    margin: 0 auto;
}

.newsletter .form-group {
    display: flex;
    gap: 0.5rem;
}

.newsletter input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: var(--radius);
    font-size: 1rem;
}

.newsletter input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .hero-content p {
        font-size: 1.2rem;
    }
    
    .newsletter .form-group {
        flex-direction: column;
    }
    
    .newsletter input {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .newsletter button {
        width: 100%;
    }
    
    .mobile-menu-toggle {
        display: block;
    }
    
    .nav-links {
        display: none;
    }
    
    .header-actions {
        gap: 0.5rem;
    }
    
    .auth-buttons {
        display: none;
    }
    
    .user-profile {
        display: none;
    }
}

@media (max-width: 576px) {
    .hero {
        height: 500px;
    }
    
    .hero-content h1 {
        font-size: 2rem;
    }
    
    .product-details {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .product-card .btn {
        width: 100%;
    }
    
    .header-icon {
        font-size: 1.1rem;
    }
    
    .balance-indicator {
        display: none;
    }
}

/* Video Controls */
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
    background-color: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(5px);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}

.video-controls button:hover {
    background-color: rgba(255, 255, 255, 0.3);
}

/* Mobile Menu */
.mobile-menu {
    position: fixed;
    top: 0;
    right: -300px;
    width: 280px;
    height: 100vh;
    background-color: white;
    z-index: 1000;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
    transition: right 0.3s ease;
    padding: 2rem 1rem;
    overflow-y: auto;
}

.mobile-menu.active {
    right: 0;
}

.mobile-menu-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-color);
    cursor: pointer;
}

.mobile-menu .nav-links {
    display: flex;
    flex-direction: column;
    margin-top: 2rem;
}

.mobile-menu .nav-links li {
    margin-bottom: 1rem;
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s;
}

.overlay.active {
    opacity: 1;
    visibility: visible;
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
        if (playPauseBtn) {
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
        
        // Función para alternar silencio
        if (muteBtn) {
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
            // Mostrar el icono de reproducción
            if (playIcon) {
                playIcon.style.display = 'block';
            }
            if (pauseIcon) {
                pauseIcon.style.display = 'none';
            }
        });
        
        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const body = document.body;

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                // Create mobile menu if it doesn't exist
                if (!document.querySelector('.mobile-menu')) {
                    createMobileMenu();
                }
                
                const mobileMenu = document.querySelector('.mobile-menu');
                const overlay = document.querySelector('.overlay');
                
                mobileMenu.classList.toggle('active');
                overlay.classList.toggle('active');
            });
        }

        // Create mobile menu and overlay
        function createMobileMenu() {
            // Create overlay
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            body.appendChild(overlay);
            
            // Create mobile menu
            const mobileMenu = document.createElement('div');
            mobileMenu.className = 'mobile-menu';
            
            // Create close button
            const closeButton = document.createElement('button');
            closeButton.className = 'mobile-menu-close';
            closeButton.innerHTML = '<i class="fas fa-times"></i>';
            mobileMenu.appendChild(closeButton);
            
            // Clone navigation links
            const navLinks = document.querySelector('.nav-links');
            if (navLinks) {
                const mobileNav = navLinks.cloneNode(true);
                mobileMenu.appendChild(mobileNav);
            }
            
            body.appendChild(mobileMenu);
            
            // Add event listeners
            closeButton.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                overlay.classList.remove('active');
            });
            
            overlay.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
        
        // Profile dropdown functionality
        const profileButton = document.querySelector('.profile-button');
        if (profileButton) {
            profileButton.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdownContent = document.querySelector('.dropdown-content');
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const profileDropdown = document.querySelector('.profile-dropdown');
                if (profileDropdown && !profileDropdown.contains(e.target)) {
                    const dropdownContent = document.querySelector('.dropdown-content');
                    if (dropdownContent) {
                        dropdownContent.style.display = 'none';
                    }
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const wishlistIcons = document.querySelectorAll('.wishlist-icon');
        
        wishlistIcons.forEach(icon => {
            icon.addEventListener('click', async function(e) {
                e.preventDefault(); // Prevenir cualquier comportamiento por defecto
                
                const productId = this.getAttribute('data-product-id');
                
                try {
                    const response = await fetch('add_to_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ productId: productId })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Si la operación fue exitosa, redirigir a lista_deseos.php
                        window.location.href = 'lista_deseos.php';
                    } else {
                        // Si hay un error, mostrar el mensaje
                        if (data.message === 'Debes iniciar sesión') {
                            window.location.href = 'login.php';
                        } else {
                            alert(data.message);
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Ha ocurrido un error al procesar tu solicitud');
                }
            });
        });

        // Añadir efecto de corazón activo para elementos en la lista de deseos
        // Esto requeriría una verificación del lado del servidor para saber qué productos están en la lista
        // Por ahora, es solo un ejemplo visual
        const wishlistItems = []; // Aquí deberías cargar los IDs de productos en la lista de deseos
        wishlistIcons.forEach(icon => {
            const productId = icon.getAttribute('data-product-id');
            if (wishlistItems.includes(productId)) {
                icon.querySelector('i').style.color = '#ef4444';
            }
        });
    });
</script>

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="FotosWeb/logo.png" alt="MGames Logo">
                <h3>MGames</h3>
            </div>
            <div class="footer-links">
                <h4>Enlaces rápidos</h4>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="todos_productos.php.php">Tienda</a></li>
                    <li><a href="novedades.php">Novedades</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Contacto</h4>
                <p><i class="fas fa-map-marker-alt"></i> Calle Principal 123, Ciudad</p>
                <p><i class="fas fa-phone"></i> +34 123 456 789</p>
                <p><i class="fas fa-envelope"></i> info@mgames.com</p>
            </div>
            <div class="footer-social">
                <h4>Síguenos</h4>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MGames. Todos los derechos reservados.</p>
        </div>
        </div>
    </footer>

<style>
/* Estilos para el footer */
.site-footer {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 3rem 0 1rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-logo {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.footer-logo img {
    height: 50px;
    margin-bottom: 1rem;
}

.footer-logo h3 {
    font-size: 1.5rem;
    margin: 0;
}

.footer-links h4, .footer-contact h4, .footer-social h4 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    position: relative;
}

.footer-links h4:after, .footer-contact h4:after, .footer-social h4:after {
    content: '';
    display: block;
    width: 40px;
    height: 3px;
    background: var(--primary-color);
    margin-top: 0.5rem;
}

.footer-links ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links ul li {
    margin-bottom: 0.5rem;
}

.footer-links ul li a {
    color: #d1d5db;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links ul li a:hover {
    color: var(--primary-color);
}

.footer-contact p {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.footer-contact p i {
    margin-right: 0.5rem;
    color: var(--primary-color);
}

.social-icons {
    display: flex;
    gap: 1rem;
}

.social-icons a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    transition: all 0.3s ease;
}

.social-icons a:hover {
    background-color: var(--primary-color);
    transform: translateY(-3px);
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 1rem;
    text-align: center;
    font-size: 0.9rem;
    color: #9ca3af;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
}
</style>

</body>
</html> 