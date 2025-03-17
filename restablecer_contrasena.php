<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $pdo->prepare("SELECT id, reset_expira FROM usuarios WHERE reset_token = ?");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        $error = 'Token invalido';
    } else if (strtotime($usuario['reset_expira']) < time()) {
        $error = 'El enlace ha expirado';
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nueva_password = $_POST['nueva_password'] ?? '';
        
        if (strlen($nueva_password) < 6) {
            $error = 'La password debe tener al menos 6 caracteres';
        } else {
            $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET contraseña = ?, reset_token = NULL, reset_expira = NULL WHERE id = ?");
            $stmt->execute([$hashed_password, $usuario['id']]);
            $success = 'Password actualizada correctamente';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Password - MGames</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="form-container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
                <p><a href="login.php">Ir a iniciar sesion</a></p>
            </div>
        <?php elseif (isset($_GET['token'])): ?>
            <form class="reset-form" method="POST">
                <h2>Restablecer Password</h2>
                <div class="form-group">
                    <label for="nueva_password">Nueva Password:</label>
                    <input type="password" id="nueva_password" name="nueva_password" required>
                </div>
                <button type="submit">Cambiar Password</button>
            </form>
        <?php endif; ?>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>