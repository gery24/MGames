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
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="profile-container">
        <div class="sidebar">
            <h2>Ajustes de la Cuenta</h2>
            <ul>
                <li><a href="#" class="menu-option" data-content="ajustes">Ajustes de Cuenta</a></li>
                <li><a href="#" class="menu-option" data-content="pedidos">Mis Pedidos</a></li>
                <li><a href="#" class="menu-option" data-content="wishlist">Lista de Deseos</a></li>
                <li><a href="#" class="menu-option" data-content="añadir_juego">Añadir Juegos</a></li>
                <li><a href="#" class="menu-option" data-content="segunda_mano">Segunda Mano</a></li>
                <li><a href="#" class="menu-option" data-content="billetera">Billetera</a></li>
                <li><a href="#" class="danger menu-option" id="logout">Cerrar Sesión</a></li>
                <li><a href="#" class="danger menu-option" id="delete-account">Eliminar Cuenta</a></li>
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

    <?php require_once 'includes/footer.php'; ?>

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
</body>
</html> 