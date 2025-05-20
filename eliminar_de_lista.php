<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}

// Obtener datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Ahora esperamos 'wishlistId' en lugar de 'productId'
if (!isset($data['wishlistId'])) {
    echo json_encode(['success' => false, 'message' => 'ID de lista de deseos no válido']);
    exit;
}

$wishlistId = intval($data['wishlistId']);

try {
    // Eliminar la entrada de la lista de deseos por su ID
    $stmt = $pdo->prepare("DELETE FROM lista_deseos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$wishlistId, $_SESSION['usuario']['id']]); // Añadimos usuario_id para seguridad
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar de la lista de deseos']);
} 