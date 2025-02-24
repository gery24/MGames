<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Obtener el producto de la base de datos
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // Agregar el producto al carrito
        $_SESSION['carrito'][] = $producto;
        header('Location: carrito.php'); // Redirigir al carrito
        exit;
    }
} else {
    // Redirigir a la página principal si no se envió el ID
    header("Location: index.php");
    exit;
} 