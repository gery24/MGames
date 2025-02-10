<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $contraseña = $_POST['password'] ?? '';

    if (empty($email) || empty($contraseña)) {
        $error = 'Por favor, rellena todos los campos';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'apellido' => $usuario['apellido'],
                'email' => $usuario['email'],
                'rol' => $usuario['rol']
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Email o contraseña incorrectos';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MGames</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">MGames</div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="login.php" class="active">Iniciar Sesión</a>
            <a href="register.php">Registrarse</a>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="form-container">
        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="login.php">
            <h2>Iniciar Sesión</h2>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Iniciar Sesión</button>
            <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
        </form>
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