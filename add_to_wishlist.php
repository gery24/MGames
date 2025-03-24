<?php
session_start();
require_once 'config/database.php';

// Verificar si la solicitud es POST y si los datos son JSON
$data = json_decode(file_get_contents('php://input'), true);

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

// Verificar si se recibió el ID del producto
if (!isset($data['productId']) || empty($data['productId'])) {
    $response['message'] = 'ID de producto no válido';
    echo json_encode($response);
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];
$producto_id = (int)$data['productId'];

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
    $stmt = $pdo->prepare("INSERT INTO lista_deseos (usuario_id, producto_id) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $producto_id]);

    $response['success'] = true;
    $response['message'] = 'Producto añadido a la lista de deseos';

} catch(PDOException $e) {
    $response['message'] = 'Error al añadir el producto a la lista de deseos';
    // Para depuración, puedes descomentar la siguiente línea:
    // $response['debug'] = $e->getMessage();
}

// Devolver la respuesta en formato JSON
echo json_encode($response); 