<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay un título de página definido
if (!isset($titulo)) {
    $titulo = "MGames - Tu tienda de videojuegos";
}

// Obtener categorías para el menú desplegable (si está disponible)
$categorias = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT id, nombre FROM categorias");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Silenciar error, el menú se mostrará vacío
    }
}
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
    /* Estilos específicos para el header moderno */
    :root {
        --logo-color: #5a4af4; /* Color exacto del logo en la imagen */
        --primary-color: #5a4af4; /* Actualizado para coincidir con el logo */
        --primary-hover: #4a3ad4;
        --text-color: #1f2937;
        --light-text: #6b7280;
        --border-color: #e5e7eb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    /* Reset básico */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--text-color);
    }

    /* Estilos del header */
    .site-header {
        background-color: var(--bg-white);
        border-bottom: 1px solid var(--border-color);
        padding: 0.85rem 1rem; /* Ajustado para coincidir con la altura original */
        position: sticky;
        top: 0;
        z-index: 100;
        height: 80px; /* Altura fija para coincidir con el original */
    }

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        height: 100%;
    }

    /* Logo */
    .logo {
        font-size: 1.5rem; /* Ajustado al tamaño original */
        font-weight: 700;
        color: var(--logo-color); /* Usando el color exacto del logo */
        text-decoration: none;
    }

    /* Navegación */
    .nav-center {
        display: flex;
        gap: 2rem;
    }

    .nav-links {
        display: flex;
        gap: 2rem;
        list-style: none;
    }

    .nav-links a {
        color: var(--text-color);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 0;
        transition: color 0.2s;
    }

    .nav-links a:hover {
        color: var(--primary-color);
    }

    /* Acciones del header (derecha) */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem; /* Reducido para coincidir con el original */
    }

    /* Botón de búsqueda */
    .search-button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 0.375rem;
        width: 2.25rem; /* Ajustado al tamaño original */
        height: 2.25rem; /* Ajustado al tamaño original */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .search-button:hover {
        background-color: var(--primary-hover);
    }

    /* Iconos de acción */
    .header-icon {
        color: var(--text-color);
        font-size: 1.25rem;
        position: relative;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem; /* Ajustado al tamaño original */
        height: 2.25rem; /* Ajustado al tamaño original */
    }

    .header-icon:hover {
        color: var(--primary-color);
    }

    /* Badge para contador */
    .badge {
        position: absolute;
        top: -5px;
        right: -5px;
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

    /* Perfil de usuario */
    .user-profile {
        display: flex;
        align-items: center;
    }

    .avatar-circle {
        width: 2.25rem; /* Ajustado al tamaño original */
        height: 2.25rem; /* Ajustado al tamaño original */
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 0.5rem;
    }

    .username {
        margin-right: 0.5rem;
        font-weight: 500;
    }

    /* Menú móvil */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-color);
    }

    /* Estilos para el menú móvil */
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

    /* Responsive */
    @media (max-width: 768px) {
        .nav-center {
            display: none;
        }
        
        .mobile-menu-toggle {
            display: block;
        }
        
        .header-actions {
            gap: 0.5rem;
        }
        
        .username {
            display: none;
        }
    }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <!-- Logo - Ahora con el color exacto de la imagen -->
            <a href="index.php" class="logo" style="color: #5a4af4;">
                MGames
            </a>
            
            <!-- Navegación central -->
            <nav class="nav-center">
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="tienda.php">Tienda</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
            
            <!-- Acciones del header (derecha) -->
            <div class="header-actions">
                <!-- Botón de búsqueda -->
                <button class="search-button" id="search-toggle" aria-label="Buscar">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- Favoritos -->
                <a href="lista_deseos.php" class="header-icon" aria-label="Lista de deseos">
                    <i class="fas fa-heart"></i>
                    <?php if(isset($_SESSION['wishlist_count']) && $_SESSION['wishlist_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['wishlist_count']; ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- Carrito -->
                <a href="carrito.php" class="header-icon" aria-label="Carrito de compras">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['cart_count']; ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- Cartera/Saldo -->
                <a href="cartera.php" class="header-icon" aria-label="Mi cartera">
                    <i class="fas fa-wallet"></i>
                </a>
                
                <!-- Perfil de usuario -->
                <?php if(isset($_SESSION['usuario'])): ?>
                    <div class="user-profile">
                        <div class="avatar-circle">
                            <?php 
                            // Obtener la primera letra del nombre de usuario
                            $initial = strtoupper(substr($_SESSION['usuario']['nombre'], 0, 1));
                            echo $initial;
                            ?>
                        </div>
                        <span class="username"><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="header-icon" aria-label="Iniciar sesión">
                        <i class="fas fa-user"></i>
                    </a>
                <?php endif; ?>
                
                <!-- Botón de menú móvil -->
                <button id="menuToggle" class="mobile-menu-toggle" aria-label="Menú">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle del menú de búsqueda
        const searchToggle = document.getElementById('search-toggle');
        if (searchToggle) {
            searchToggle.addEventListener('click', function() {
                // Aquí puedes implementar la funcionalidad de búsqueda
                // Por ejemplo, redirigir a la página de búsqueda o mostrar un modal
                window.location.href = 'busqueda.php';
            });
        }
        
        // Toggle del menú móvil
        const menuToggle = document.getElementById('menuToggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                // Crear menú móvil si no existe
                if (!document.querySelector('.mobile-menu')) {
                    createMobileMenu();
                }
                
                const mobileMenu = document.querySelector('.mobile-menu');
                const overlay = document.querySelector('.overlay');
                
                mobileMenu.classList.toggle('active');
                overlay.classList.toggle('active');
            });
        }
        
        // Función para crear el menú móvil
        function createMobileMenu() {
            // Crear overlay
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            document.body.appendChild(overlay);
            
            // Crear menú móvil
            const mobileMenu = document.createElement('div');
            mobileMenu.className = 'mobile-menu';
            
            // Crear botón de cierre
            const closeButton = document.createElement('button');
            closeButton.className = 'mobile-menu-close';
            closeButton.innerHTML = '<i class="fas fa-times"></i>';
            mobileMenu.appendChild(closeButton);
            
            // Clonar enlaces de navegación
            const navLinks = document.querySelector('.nav-links');
            if (navLinks) {
                const mobileNav = navLinks.cloneNode(true);
                mobileMenu.appendChild(mobileNav);
            }
            
            document.body.appendChild(mobileMenu);
            
            // Añadir event listeners
            closeButton.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                overlay.classList.remove('active');
            });
            
            overlay.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
    });
    </script>