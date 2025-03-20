<?php
session_start();
require_once 'config/database.php';

// Verificar si hay productos en el carrito
$productos_en_carrito = $_SESSION['carrito'] ?? [];
if (empty($productos_en_carrito)) {
    header('Location: carrito.php');
    exit;
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pago</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="content">
        <h1>Formulario de Pago</h1>
        <form method="POST" action="procesar_pago.php">
            <div class="form-group">
                <label for="numero_tarjeta">Número de Tarjeta:</label>
                <input type="text" id="numero_tarjeta" name="numero_tarjeta" required>
            </div>
            <div class="form-group">
                <label for="fecha_expiracion">Fecha de Caducidad (MM/AA):</label>
                <input type="text" id="fecha_expiracion" name="fecha_expiracion" required>
            </div>
            <div class="form-group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" required>
            </div>
            <button type="submit" class="btn">Pagar</button>
        </form>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html> 