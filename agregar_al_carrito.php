<?php
session_start();

// Verificar si se ha enviado el ID del producto
if (isset($_POST['producto_id'])) {
    $producto_id = $_POST['producto_id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    // Inicializar el carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Añadir el producto al carrito
    $_SESSION['carrito'][$producto_id] = [
        'nombre' => $nombre,
        'precio' => $precio,
        'cantidad' => 1 // Puedes ajustar la cantidad según sea necesario
    ];

    // Redirigir de vuelta a la página del producto
    header("Location: producto.php?id=$producto_id");
    exit;
} else {
    // Redirigir a la página principal si no se envió el ID
    header("Location: index.php");
    exit;
} 