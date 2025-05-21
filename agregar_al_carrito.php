<?php
session_start();
require_once 'config/database.php';

// Verificar si se han enviado los datos necesarios
if (!isset($_POST['id']) || !isset($_POST['tipo_producto']) || !isset($_POST['cantidad'])) {
    $campos_faltantes = [];
    if (!isset($_POST['id'])) $campos_faltantes[] = 'id';
    if (!isset($_POST['tipo_producto'])) $campos_faltantes[] = 'tipo de producto';
    if (!isset($_POST['cantidad'])) $campos_faltantes[] = 'cantidad';
    
    $_SESSION['error'] = "Faltan campos necesarios: " . implode(', ', $campos_faltantes);
    header('Location: carrito.php');
    exit;
}

$producto_id = $_POST['id'];
$tipo_producto = $_POST['tipo_producto'];
$cantidad = intval($_POST['cantidad']);

// Validar que la cantidad sea positiva
if ($cantidad <= 0) {
    $cantidad = 1;
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verificar si el producto ya está en el carrito
if (isset($_SESSION['carrito'][$producto_id])) {
    // Si ya está, incrementar la cantidad
    $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
    $_SESSION['mensaje'] = "Se ha actualizado la cantidad del producto en el carrito.";
} else {
    // Si no está, añadirlo
    // Verificar que se han enviado todos los datos necesarios
    if (!isset($_POST['nombre']) || !isset($_POST['precio']) || !isset($_POST['imagen'])) {
        $_SESSION['error'] = "Faltan datos del producto para añadir al carrito.";
        header('Location: carrito.php');
        exit;
    }
    
    // Añadir el producto al carrito
    $_SESSION['carrito'][$producto_id] = [
        'nombre' => $_POST['nombre'],
        'precio' => floatval($_POST['precio']),
        'imagen' => $_POST['imagen'],
        'cantidad' => $cantidad,
        'tipo_producto' => $tipo_producto
    ];
    
    // Añadir descuento si existe
    if (isset($_POST['descuento'])) {
        $_SESSION['carrito'][$producto_id]['descuento'] = floatval($_POST['descuento']);
    }
    
    $_SESSION['mensaje'] = "Producto añadido al carrito correctamente.";
}

// Redireccionar de vuelta a la página especificada o a la página anterior/carrito
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Si es una solicitud AJAX, no redirigir aquí (la respuesta se maneja en JS)
    // Puedes enviar un JSON de éxito si lo necesitas
    echo json_encode(['success' => true, 'message' => $_SESSION['mensaje'] ?? '']);
    unset($_SESSION['mensaje']); // Limpiar mensaje de éxito después de enviarlo
    exit;
} else {
    // Si es un envío de formulario normal, redirigir
    $redirect_url = $_POST['redirect_to'] ?? ($_SERVER['HTTP_REFERER'] ?? 'carrito.php');
    header("Location: $redirect_url");
    exit;
}
