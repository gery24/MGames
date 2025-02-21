<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Obtener información del producto
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // Añadir el producto al carrito
        $_SESSION['carrito'][] = $producto;
        header('Location: producto.php?id=' . $id . '&added=1'); // Redirigir de vuelta a la página del producto
    } else {
        die("Error: Producto no encontrado.");
    }
} else {
    die("Error: ID de producto no especificado.");
} 