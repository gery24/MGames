<?php
session_start();
require_once 'config/database.php';

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_tarjeta = $_POST['numero_tarjeta'];
    $fecha_expiracion = $_POST['fecha_expiracion'];
    $cvv = $_POST['cvv'];

    // Aquí puedes agregar la lógica para procesar el pago
    // Por ejemplo, verificar los datos de la tarjeta y realizar la transacción

    // Simulación de éxito en el pago
    $_SESSION['mensaje'] = 'Pago realizado con éxito. Gracias por tu compra!';
    // Limpiar el carrito después del pago
    unset($_SESSION['carrito']);
    header('Location: index.php'); // Redirigir a la página principal o a una página de confirmación
    exit;
} else {
    die("Método de solicitud no válido.");
}
?> 