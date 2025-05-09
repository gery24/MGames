<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    // Responder con JSON para solicitudes AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para realizar esta acción']);
        exit;
    }
    
    // Redirección normal para solicitudes no-AJAX
    header('Location: login.php');
    exit;
}

// Verificar si se enviaron los datos necesarios
if (!isset($_POST['monto']) || !isset($_POST['descripcion'])) {
    // Responder con JSON para solicitudes AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos']);
        exit;
    }
    
    // Redirección normal para solicitudes no-AJAX
    header('Location: perfil.php?error=' . urlencode('Faltan datos requeridos'));
    exit;
}

$monto = floatval($_POST['monto']);
$descripcion = trim($_POST['descripcion']);
$usuarioId = $_SESSION['usuario']['id'];
$origen = isset($_POST['origen']) ? $_POST['origen'] : 'cartera';

// Validar el monto
if ($monto <= 0) {
    // Responder con JSON para solicitudes AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'El monto debe ser mayor que cero']);
        exit;
    }
    
    // Redirección normal para solicitudes no-AJAX
    header('Location: perfil.php?error=' . urlencode('El monto debe ser mayor que cero'));
    exit;
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Registrar la transacción
    $stmt = $pdo->prepare("INSERT INTO transacciones (usuario_id, monto, descripcion, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$usuarioId, $monto, $descripcion]);
    
    // Actualizar el saldo del usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET cartera = cartera + ? WHERE id = ?");
    $stmt->execute([$monto, $usuarioId]);
    
    // Actualizar el saldo en la sesión
    $_SESSION['usuario']['cartera'] += $monto;
    
    // Confirmar transacción
    $pdo->commit();
    
    // Responder con JSON para solicitudes AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Fondos agregados correctamente', 'saldo' => $_SESSION['usuario']['cartera']]);
        exit;
    }
    
    // Redirección normal para solicitudes no-AJAX
    header('Location: ' . ($origen === 'perfil' ? 'perfil.php' : 'cartera.php') . '?success=true');
    exit;
    
} catch(PDOException $e) {
    // Revertir transacción en caso de error
    $pdo->rollBack();
    
    // Responder con JSON para solicitudes AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al procesar la transacción: ' . $e->getMessage()]);
        exit;
    }
    
    // Redirección normal para solicitudes no-AJAX
    header('Location: ' . ($origen === 'perfil' ? 'perfil.php' : 'cartera.php') . '?error=' . urlencode('Error al procesar la transacción'));
    exit;
}
?>
