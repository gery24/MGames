<?php
session_start();

// Verificar si se ha enviado un ID de producto
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['error'] = "Error: ID de producto no especificado para eliminar.";
    header('Location: carrito.php');
    exit;
}

$producto_id = $_POST['id'];

// Verificar si el producto existe en el carrito
if (isset($_SESSION['carrito'][$producto_id])) {
    // Eliminar el producto del carrito
    unset($_SESSION['carrito'][$producto_id]);
    
    // Mensaje de éxito
    $_SESSION['mensaje'] = "Producto eliminado del carrito correctamente.";
} else {
    // Mensaje de error
    $_SESSION['error'] = "El producto no se encuentra en el carrito.";
}

// Redireccionar de vuelta al carrito
header('Location: carrito.php');
exit;
