<?php
session_start();
require_once 'config/database.php';

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Preparar la respuesta
$response = [
    'success' => false,
    'message' => ''
];

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    $response['message'] = 'Debes iniciar sesión';
    echo json_encode($response);
    exit;
}

// Obtener el ID del producto (soporta tanto POST normal como JSON)
$producto_id = null;

// Verificar si es una solicitud JSON
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
if (strpos($contentType, 'application/json') !== false) {
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
    $response['message'] = 'ID de producto no válido';
    echo json_encode($response);
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];

try {
    // Verificar si el producto ya está en la lista de deseos del usuario
    $stmt = $pdo->prepare("SELECT id FROM lista_deseos WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$usuario_id, $producto_id]);
    
    if ($stmt->rowCount() > 0) {
        $response['message'] = 'Este producto ya está en tu lista de deseos';
        echo json_encode($response);
        exit;
    }

    // Insertar el producto en la lista de deseos
    $stmt = $pdo->prepare("INSERT INTO lista_deseos (usuario_id, producto_id, fecha_agregado) VALUES (?, ?, NOW())");
    $stmt->execute([$usuario_id, $producto_id]);

    $response['success'] = true;
    $response['message'] = 'Producto añadido a la lista de deseos';
    echo json_encode($response);

} catch(PDOException $e) {
    $response['message'] = 'Error al añadir el producto a la lista de deseos: ' . $e->getMessage();
    echo json_encode($response);
}
?>

