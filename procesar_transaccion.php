<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['usuario']['id'];
$monto = $_POST['monto'];
$descripcion = $_POST['descripcion'];
$origen = isset($_POST['origen']) ? $_POST['origen'] : 'cartera';

try {
    // Agregar el monto a la cartera del usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET cartera = cartera + ? WHERE id = ?");
    $stmt->execute([$monto, $userId]);

    // Registrar la transacción
    $stmt_transaccion = $pdo->prepare("INSERT INTO transacciones (usuario_id, monto, descripcion) VALUES (?, ?, ?)");
    $stmt_transaccion->execute([$userId, $monto, $descripcion]);

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
    
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}