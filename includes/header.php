<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'MGames - Tu tienda de videojuegos'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('.menu-button');
            const menuDropdown = document.querySelector('.menu-dropdown');
            const userLogo = document.getElementById('user-logo');
            const userDropdown = document.querySelector('.user-menu .dropdown-menu');

            // Mostrar/ocultar el menú al hacer clic en el botón del menú
            menuButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Evitar que el clic se propague
                menuDropdown.classList.toggle('show');
            });

            // Mostrar/ocultar el menú al hacer clic en el logo del usuario
            userLogo.addEventListener('click', function(event) {
                event.stopPropagation(); // Evitar que el clic se propague
                userDropdown.classList.toggle('show');
            });

            // Cerrar el menú si se hace clic fuera de él
            document.addEventListener('click', function(event) {
                if (!menuDropdown.contains(event.target) && !menuButton.contains(event.target)) {
                    menuDropdown.classList.remove('show');
                }
                if (!userDropdown.contains(event.target) && !userLogo.contains(event.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html> 