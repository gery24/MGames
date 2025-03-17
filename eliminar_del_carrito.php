<?php
session_start();

// Verificar si se ha pasado un ID de producto
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Buscar el producto en el carrito
    foreach ($_SESSION['carrito'] as $key => $producto) {
        if ($producto['id'] == $id) {
            // Eliminar el producto del carrito
            unset($_SESSION['carrito'][$key]);
            break;
        }
    }

    // Redirigir de vuelta al carrito
    header('Location: carrito.php');
    exit;
} else {
    die("Error: ID de producto no especificado.");
} 