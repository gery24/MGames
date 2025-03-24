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

if (!isset($data['productId'])) {
    echo json_encode(['success' => false, 'message' => 'ID de producto no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM lista_deseos WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$_SESSION['usuario']['id'], $data['productId']]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
} 