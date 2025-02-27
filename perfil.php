<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$usuario = $_SESSION['usuario'];

// Obtener información adicional del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario['id']]);
    $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($datosUsuario) {
        // Actualizar solo si encontramos datos
        foreach ($datosUsuario as $key => $value) {
            $usuario[$key] = $value;
        }
        $_SESSION['usuario'] = $usuario;
    }
} catch(PDOException $e) {
    $error = 'Error al obtener información del usuario';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - MGames</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="<?php echo ($usuario['rol'] === 'ADMIN') ? 'admin' : ''; ?>">
    <header class="header">
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
    </header>

    <div class="content">
        <div class="profile-container">
            <div class="sidebar">
                <h2>Ajustes de la Cuenta</h2>
                <ul>
                    <?php if ($usuario['rol'] === 'ADMIN'): ?>
                        <li><a href="#" class="menu-option" data-content="ajustes">Ajustes de Cuenta</a></li>
                        <li><a href="#" class="menu-option" data-content="pedidos">Pedidos</a></li>
                        <li><a href="panel_admin.php">Añadir Juegos</a></li>
                    <?php else: ?>
                        <li><a href="#" class="menu-option" data-content="ajustes">Ajustes de Cuenta</a></li>
                        <li><a href="#" class="menu-option" data-content="wishlist">Lista de Deseos</a></li>
                        <li><a href="#" class="menu-option" data-content="segunda_mano">Segunda Mano</a></li>
                        <li><a href="#" class="menu-option" data-content="billetera">Billetera</a></li>
                        <li><a href="#" class="danger menu-option" id="logout">Cerrar Sesión</a></li>
                        <li><a href="#" class="danger menu-option" id="delete-account">Eliminar Cuenta</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="profile-info" id="dynamic-content">
                <h1 id="section-title">Ajustes de Cuenta</h1>
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="update_profile.php" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <div class="input-container">
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido:</label>
                        <div class="input-container">
                            <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <div class="input-container">
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rol">Rol:</label>
                        <div class="input-container">
                            <input type="text" id="rol" name="rol" value="<?php echo htmlspecialchars($usuario['rol']); ?>" readonly>
                        </div>
                    </div>
                    <button type="submit">Actualizar Información</button>
                    
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Cargar automáticamente el contenido de "Ajustes de Cuenta" al entrar
            loadContent('ajustes');

            $('.menu-option').on('click', function(e) {
                e.preventDefault();
                const content = $(this).data('content');

                // Cambiar el título según la opción seleccionada
                $('#section-title').text(content.charAt(0).toUpperCase() + content.slice(1).replace(/_/g, ' '));

                // Cargar el contenido correspondiente
                loadContent(content);
            });

            function loadContent(content) {
                $('#dynamic-content').html('<h1>Cargando...</h1>'); // Mensaje de carga

                // Simulación de carga de contenido
                setTimeout(() => {
                    if (content === 'ajustes') {
                        $('#dynamic-content').html(`
                            <form action="update_profile.php" method="POST">
                                <div class="form-group">
                                    <label for="nombre">Nombre:</label>
                                    <div class="input-container">
                                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="apellido">Apellido:</label>
                                    <div class="input-container">
                                        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <div class="input-container">
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="rol">Rol:</label>
                                    <div class="input-container">
                                        <input type="text" id="rol" name="rol" value="<?php echo htmlspecialchars($usuario['rol']); ?>" readonly>
                                    </div>
                                </div>
                                <button type="submit">Actualizar Información</button>
                            </form>
                        `);
                    } else {
                        $('#dynamic-content').html('<h1>' + content.charAt(0).toUpperCase() + content.slice(1) + '</h1><p>Contenido de ' + content + ' cargado.</p>');
                    }
                }, 1000);
            }

            $('#logout').on('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                    window.location.href = 'logout.php';
                }
            });

            $('#delete-account').on('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')) {
                    window.location.href = 'eliminar_cuenta.php';
                }
            });
        });
        
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>