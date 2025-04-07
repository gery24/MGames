<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['usuario']['id'];
$monto = floatval($_POST['monto']);
$descripcion = $_POST['descripcion'];
$origen = isset($_POST['origen']) ? $_POST['origen'] : 'cartera';

try {
    // Verificar que el usuario tenga saldo suficiente
    $stmt = $pdo->prepare("SELECT cartera FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception("Usuario no encontrado.");
    }
    
    $saldoActual = floatval($usuario['cartera']);
    
    if ($saldoActual < $monto) {
        throw new Exception("Saldo insuficiente para realizar el retiro.");
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Restar el monto de la cartera del usuario (el monto se guarda como negativo para retiros)
    $montoNegativo = -1 * abs($monto); // Aseguramos que sea negativo
    $stmt = $pdo->prepare("UPDATE usuarios SET cartera = cartera + ? WHERE id = ?");
    $stmt->execute([$montoNegativo, $userId]);
    
    // Registrar la transacción con monto negativo
    $stmt_transaccion = $pdo->prepare("INSERT INTO transacciones (usuario_id, monto, descripcion) VALUES (?, ?, ?)");
    $stmt_transaccion->execute([$userId, $montoNegativo, $descripcion]);
    
    // Confirmar la transacción
    $pdo->commit();
    
    // Actualizar el saldo en la sesión
    $stmt = $pdo->prepare("SELECT cartera FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['usuario']['cartera'] = $usuario['cartera'];
    
    // Redirigir según el origen
    if ($origen === 'perfil') {
        header('Location: perfil.php');
    } else {
        header('Location: cartera.php?success=true');
    }
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $errorMsg = urlencode($e->getMessage());
    
    // Redirigir con mensaje de error
    if ($origen === 'perfil') {
        header("Location: perfil.php?error=$errorMsg");
    } else {
        header("Location: cartera.php?error=$errorMsg");
    }
}