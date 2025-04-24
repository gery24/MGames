<?php

require_once 'config/database.php';

try {
    // Verificar la conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener categorías
    $stmt = $pdo->query("SELECT id, nombre FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
    die();
}

// Título de la página - puede ser sobrescrito en las páginas individuales
$titulo = isset($titulo) ? $titulo : "MGames - Tu tienda de videojuegos";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
    /* Variables y estilos básicos necesarios para el header */
    :root {
        --primary-color: #4f46e5;
        --primary-dark: #4338ca;
        --secondary-color: #6366f1;
        --text-color: #1f2937;
        --text-light: #6b7280;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius: 0.5rem;
    }

    /* Estilos de botones usados en el header */
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
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

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
    }

    /* Estilos del header */
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
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
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

    /* Navegación */
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

    /* Acciones del header */
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

    /* Botón de menú móvil */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        color: var(--text-color);
        font-size: 1.5rem;
        cursor: pointer;
    }

    /* Perfil de usuario */
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

    /* Botones de autenticación */
    .auth-buttons {
        display: flex;
        gap: 0.5rem;
    }

    /* Contenedor de búsqueda */
    .search-container {
        position: relative;
    }

    .search-button {
        background: none;
        border: none;
        color: var(--text-color);
        font-size: 1.25rem;
        cursor: pointer;
        transition: color 0.3s;
    }

    .search-button:hover {
        color: var(--primary-color);
    }

    .search-form {
        position: absolute;
        top: 100%;
        right: 0;
        background-color: white;
        padding: 1rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        z-index: 100;
        min-width: 300px;
        display: flex;
        gap: 0.5rem;
    }

    .search-form input, .search-form select {
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: var(--radius);
    }

    .search-form button {
        padding: 0.5rem 1rem;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
    }

    /* Menú móvil */
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

    .product-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .product-card-content {
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .product-actions {
        margin-top: auto;
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .product-actions .btn {
        width: auto;
        padding: 10px 15px;
        text-align: center;
    }

    </style>
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
                    <li><a href="tienda.php">Tienda</a></li>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search toggle functionality
        const searchToggle = document.getElementById('search-toggle');
        const searchForm = document.getElementById('search-form');
        
        if (searchToggle && searchForm) {
            searchToggle.addEventListener('click', function() {
                if (searchForm.style.display === 'none') {
                    searchForm.style.display = 'flex';
                } else {
                    searchForm.style.display = 'none';
                }
            });
            
            // Close search form when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchToggle.contains(e.target) && !searchForm.contains(e.target)) {
                    searchForm.style.display = 'none';
                }
            });
        }
        
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
    </script>

