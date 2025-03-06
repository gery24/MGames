<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error = 'Por favor, ingresa tu email';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Guardar token en la base de datos
            $stmt = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_expira = ? WHERE id = ?");
            $stmt->execute([$token, $expira, $usuario['id']]);

            // En un entorno real, aquí enviarías un email con el enlace para restablecer la contraseña
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/restablecer_contrasena.php?token=$token";
            
            $success = "Se ha enviado un enlace a tu email para restablecer tu contraseña. (Simulado: $reset_link)";
        } else {
            $error = 'No se encontró ninguna cuenta con ese email';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - MGames</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="form-container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php else: ?>
            <form class="recover-form" method="POST" action="recuperar_contrasena.php">
                <h2>Recuperar Contraseña</h2>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">Enviar enlace de recuperación</button>
            </form>
        <?php endif; ?>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>