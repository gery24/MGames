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
        <div class="logo"><a href="index.php">MGames</a></div>
        <div class="search-bar">
            <form action="index.php" method="GET" class="search-form">
                <?php if(isset($_GET['categoria'])): ?>
                    <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($_GET['categoria']); ?>">
                <?php endif; ?>
                <?php if(isset($_GET['precio'])): ?>
                    <input type="hidden" name="precio" value="<?php echo htmlspecialchars($_GET['precio']); ?>">
                <?php endif; ?>
                <input type="text" name="buscar" placeholder="Buscar juegos..." 
                       value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="nav-right">
            <a href="segunda_mano.php">Segunda Mano</a>
            <?php if(isset($_SESSION['usuario'])): ?>
                <a href="perfil.php">Mi Perfil</a>
                <?php if ($_SESSION['usuario']['rol'] === 'ADMIN'): ?>
                    <a href="panel_admin.php">Panel Admin</a>
                <?php endif; ?>
                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar Sesión</a>
                <a href="register.php">Registrarse</a>
            <?php endif; ?>
            <a href="carrito.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <span class="cart-count"><?php echo count($_SESSION['carrito']); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </nav>
</body>
</html> 