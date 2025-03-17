<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'MGames - Tu tienda de videojuegos'; ?></title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="css/segunda_mano.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .menu-button {
            background: none;
            border: none;
            color: white !important;
            cursor: pointer;
            font-size: 1.2em;
        }
        .menu-button i {
            color: white !important;
        }
        .menu-button:hover {
            opacity: 0.8;
        }
        .menu-dropdown {
            display: none;
            position: absolute;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 15px;
            border-radius: 8px;
            min-width: 220px;
            border: 2px solid red;
        }
        
        body:not(.admin) .menu-dropdown {
            border: 2px solid var(--navbar-color);
        }

        .menu-dropdown .menu-item {
            color: red;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: background-color 0.3s;
            font-size: 1.1em;
            white-space: nowrap;
        }

        body:not(.admin) .menu-dropdown .menu-item {
            color: var(--navbar-color);
        }

        .menu-dropdown .menu-item:hover {
            background-color: rgba(255, 0, 0, 0.1);
        }

        body:not(.admin) .menu-dropdown .menu-item:hover {
            background-color: rgba(30, 58, 138, 0.1);
        }
        .user-menu .dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 15px;
            border-radius: 8px;
            min-width: 160px;
            right: 0;
            top: 100%;
            border: 2px solid red;
        }

        .user-menu .dropdown-menu a {
            color: red;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            white-space: nowrap;
            transition: background-color 0.3s;
        }

        .user-menu .dropdown-menu a:hover {
            background-color: rgba(255, 0, 0, 0.1);
        }

        body:not(.admin) .user-menu .dropdown-menu {
            border: 2px solid var(--navbar-color);
        }

        body:not(.admin) .user-menu .dropdown-menu a {
            color: var(--navbar-color);
        }

        body:not(.admin) .user-menu .dropdown-menu a:hover {
            background-color: rgba(30, 58, 138, 0.1);
        }
        .user-avatar {
            width: 35px;
            height: 35px;
            background-color: red;
            color: white !important;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .user-avatar:hover {
            opacity: 0.9;
        }
        .logo a {
            color: white !important;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="<?php echo (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN') ? 'admin' : ''; ?>">
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
                </div>
            </div>
            <a href="carrito.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <span class="cart-count"><?php echo count($_SESSION['carrito']); ?></span>
                <?php endif; ?>
            </a>
            <?php if(!isset($_SESSION['usuario'])): ?>
                <a href="login.php" class="login-icon">
                    <img src="fotosWeb/Iniciate.png" alt="Iniciar Sesión" style="width: 35px; height: 35px;">
                </a>
            <?php else: ?>
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