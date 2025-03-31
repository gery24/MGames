<?php
session_start();
require_once 'config/database.php';

// Preparar la respuesta
$response = [
    'success' => false,
    'message' => ''
];

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Si es una solicitud AJAX, devolver JSON
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $response['message'] = 'Debes iniciar sesión';
        echo json_encode($response);
    } else {
        // Si es una solicitud normal, redirigir al login
        header('Location: login.php?redirect=' . urlencode($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    }
    exit;
}

// Obtener el ID del producto (soporta tanto POST normal como JSON)
$producto_id = null;

// Verificar si es una solicitud JSON
if ($_SERVER['CONTENT_TYPE'] === 'application/json' || strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $producto_id = isset($data['productId']) ? (int)$data['productId'] : null;
} else {
    // Verificar si es un formulario POST normal
    $producto_id = isset($_POST['id']) ? (int)$_POST['id'] : null;
}

// Si no hay ID de producto, verificar GET (para enlaces directos)
if (!$producto_id) {
    $producto_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
}

// Verificar si se recibió el ID del producto
if (!$producto_id) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $response['message'] = 'ID de producto no válido';
        echo json_encode($response);
    } else {
        header('Location: index.php?error=producto_no_valido');
    }
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];

try {
    // Verificar si el producto ya está en la lista de deseos del usuario
    $stmt = $pdo->prepare("SELECT id FROM lista_deseos WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$usuario_id, $producto_id]);
    
    if ($stmt->rowCount() > 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $response['message'] = 'Este producto ya está en tu lista de deseos';
            echo json_encode($response);
        } else {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?wishlist_error=already_exists');
        }
        exit;
    }

    // Insertar el producto en la lista de deseos
    $stmt = $pdo->prepare("INSERT INTO lista_deseos (usuario_id, producto_id, fecha_agregado) VALUES (?, ?, NOW())");
    $stmt->execute([$usuario_id, $producto_id]);

    $response['success'] = true;
    $response['message'] = 'Producto añadido a la lista de deseos';

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode($response);
    } else {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?wishlist_success=true');
    }

} catch(PDOException $e) {
    $response['message'] = 'Error al añadir el producto a la lista de deseos';
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode($response);
    } else {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?wishlist_error=' . urlencode($e->getMessage()));
    }
}
?>

