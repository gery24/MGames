<?php
session_start();

// Verificar si se han pasado el ID y el tipo de producto
if (isset($_POST['id']) && isset($_POST['tipo_producto'])) {
    $producto_id = $_POST['id'];
    $tipo_producto = $_POST['tipo_producto'];

    // Construir la clave del item en el carrito
    $cart_item_key = $tipo_producto . '_' . $producto_id;

    // Verificar si el carrito existe y si el item está en el carrito
    if (isset($_SESSION['carrito']) && isset($_SESSION['carrito'][$cart_item_key])) {
        // Eliminar el producto del carrito usando la clave correcta
        unset($_SESSION['carrito'][$cart_item_key]);
        
        // Opcional: Reindexar el array si es necesario, aunque unset no deja "huecos" numéricos.
        // Si las claves no son numéricas (como ahora), reindexar no es típicamente necesario/deseado.

        $_SESSION['mensaje'] = 'Producto eliminado del carrito correctamente';

    } else {
         $_SESSION['error'] = 'El producto no se encontró en el carrito.';
    }

    // Redirigir de vuelta al carrito
    header('Location: carrito.php');
    exit;

} else {
    // Si falta ID o tipo_producto, redirigir con un mensaje de error
    $_SESSION['error'] = 'Error: ID de producto o tipo de producto no especificado para eliminar.';
    header('Location: carrito.php');
    exit;
}
?> 