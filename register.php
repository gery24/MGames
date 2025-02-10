<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $contraseña = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($apellido) || empty($email) || empty($contraseña) || empty($confirm_password)) {
        $error = 'Por favor, rellena todos los campos';
    } elseif ($contraseña !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($contraseña) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Este email ya está registrado';
        } else {
            // Crear nuevo usuario
            try {
                $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, contraseña, rol) VALUES (?, ?, ?, ?, 'CLIENTE')");
                $stmt->execute([$nombre, $apellido, $email, $hashed_password]);
                
                $success = 'Registro completado con éxito. Ya puedes iniciar sesión.';
            } catch(PDOException $e) {
                $error = 'Error al crear la cuenta. Por favor, inténtalo de nuevo.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MGames</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">MGames</div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="login.php">Iniciar Sesión</a>
            <a href="register.php" class="active">Registrarse</a>
        </div>
    </nav>

    <!-- Register Form -->
    <div class="form-container">
        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
                <p><a href="login.php">Ir a iniciar sesión</a></p>
            </div>
        <?php else: ?>
            <form class="register-form" method="POST" action="register.php">
                <h2>Crear Cuenta</h2>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm-password" name="confirm_password" required>
                </div>
                <button type="submit">Registrarse</button>
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </form>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Sobre MGames</h4>
                <p>Tu tienda de confianza para videojuegos nuevos y de segunda mano.</p>
            </div>
            <div class="footer-section">
                <h4>Enlaces Útiles</h4>
                <ul>
                    <li><a href="soporte.php">Soporte</a></li>
                    <li><a href="vender.php">Vender Juegos</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Síguenos</h4>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 MGames. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html> 