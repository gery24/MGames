<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['cantidad'])) {
    $producto_id = intval($_POST['id']);
    $cantidad = intval($_POST['cantidad']);
    
    // Validar que la cantidad sea positiva
    if ($cantidad > 0 && $cantidad <= 10) {
        // Verificar que el producto existe en el carrito
        if (isset($_SESSION['carrito'][$producto_id])) {
            // Actualizar la cantidad
            $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
            
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false]);
?>