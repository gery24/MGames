<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Obtener información del producto
    $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // Verificar si el producto ya está en el carrito
        $producto_en_carrito = false;
        foreach ($_SESSION['carrito'] as $item) {
            if ($item['id'] === $producto['id']) {
                $producto_en_carrito = true;
                break;
            }
        }

        if (!$producto_en_carrito) {
            // Añadir el producto al carrito
            $_SESSION['carrito'][] = $producto;
            header('Location: producto.php?id=' . $id . '&added=1'); // Redirigir de vuelta a la página del producto
        } else {
            // Redirigir con un mensaje de que el producto ya está en el carrito
            header('Location: producto.php?id=' . $id . '&already_in_cart=1');
        }
    } else {
        die("Error: Producto no encontrado.");
    }
} else {
    die("Error: ID de producto no especificado.");
} 