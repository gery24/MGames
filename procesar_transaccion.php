<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['usuario']['id'];
$monto = $_POST['monto'];
$descripcion = $_POST['descripcion'];

try {
    // Actualizar el saldo de la cartera
    $stmt = $pdo->prepare("UPDATE usuarios SET cartera = cartera + ? WHERE id = ?");
    $stmt->execute([$monto, $userId]);

    // Registrar la transacción
    $stmt_transaccion = $pdo->prepare("INSERT INTO transacciones (usuario_id, monto, descripcion) VALUES (?, ?, ?)");
    $stmt_transaccion->execute([$userId, $monto, $descripcion]);

    header('Location: cartera.php');
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
} 