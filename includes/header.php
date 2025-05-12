<?php
// Verificar si el usuario está logueado
$isLoggedIn = isset($_SESSION['usuario']);
// Verificar si el usuario es admin
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGames</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body class="<?php echo $isAdmin ? 'admin' : ''; ?>">
    <header class="site-header">
        <div class="container">
            <a href="index.php" class="logo">
                <span>MGames</span>
            </a>
            
            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="tienda.php">Tienda</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <a href="lista_deseos.php" class="header-icon" aria-label="Lista de deseos">
                    <i class="fas fa-heart"></i>
                    <?php if(isset($_SESSION['wishlist_count']) && $_SESSION['wishlist_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['wishlist_count']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="carrito.php" class="header-icon" aria-label="Carrito de compras">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['cart_count']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="cartera.php" class="header-icon" aria-label="Mi cartera">
                    <i class="fas fa-wallet"></i>
                    <?php if(isset($_SESSION['user_balance'])): ?>
                        <span class="balance-indicator"><?php echo number_format($_SESSION['user_balance'], 2); ?>€</span>
                    <?php endif; ?>
                </a>
                
                <?php if($isLoggedIn): ?>
                    <div class="profile-dropdown">
                        <button class="profile-button">
                            <div class="avatar-circle <?php echo $isAdmin ? 'admin-avatar' : ''; ?>">
                                <?php 
                                // Obtener la primera letra del nombre de usuario
                                $initial = strtoupper(substr($_SESSION['usuario']['nombre'], 0, 1));
                                echo $initial;
                                ?>
                            </div>
                        </button>
                        <div class="dropdown-content <?php echo $isAdmin ? 'admin-dropdown' : ''; ?>">
                            <a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a>
                            <a href="pedidos.php"><i class="fas fa-box"></i> Mis Pedidos</a>
                            <a href="configuracion.php"><i class="fas fa-cog"></i> Configuración</a>
                            <?php if($isAdmin): ?>
                                <a href="panel_admin.php" class="admin-link"><i class="fas fa-shield-alt"></i> Panel Admin</a>
                            <?php endif; ?>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-outline">INICIAR SESIÓN</a>
                        <a href="register.php" class="btn btn-primary">REGISTRARSE</a>
                    </div>
                <?php endif; ?>
                
                <button id="menuToggle" class="mobile-menu-toggle" aria-label="Menú móvil">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
</body>
</html>
