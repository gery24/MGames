<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    header('Location: login.php?redirect=producto.php?id=' . $_POST['producto_id']);
    exit;
}

// Obtener datos del formulario
$usuario_id = $_SESSION['usuario']['id'];
$producto_id = $_POST['producto_id'] ?? null;
$puntuacion = $_POST['puntuacion'] ?? null;
$comentario = $_POST['comentario'] ?? '';

// Validar datos
if (!$usuario_id || !$producto_id || !$puntuacion) {
    header('Location: producto.php?id=' . $producto_id . '&review_error=Faltan datos obligatorios');
    exit;
}

try {
    // Verificar si el usuario ya ha dejado una reseña para este producto
    $stmt_check = $pdo->prepare("SELECT id FROM reseñas WHERE usuario_id = ? AND producto_id = ?");
    $stmt_check->execute([$usuario_id, $producto_id]);
    
    if ($stmt_check->rowCount() > 0) {
        // Actualizar reseña existente
        $stmt = $pdo->prepare("UPDATE reseñas SET puntuacion = ?, comentario = ?, fecha = NOW() WHERE usuario_id = ? AND producto_id = ?");
        $stmt->execute([$puntuacion, $comentario, $usuario_id, $producto_id]);
        $mensaje = 'Reseña actualizada correctamente';
    } else {
        // Insertar nueva reseña
        $stmt = $pdo->prepare("INSERT INTO reseñas (usuario_id, producto_id, puntuacion, comentario, fecha) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$usuario_id, $producto_id, $puntuacion, $comentario]);
        $mensaje = 'Reseña guardada correctamente';
    }
    
    header('Location: producto.php?id=' . $producto_id . '&review_success=' . $mensaje);
    exit;
} catch (PDOException $e) {
    header('Location: producto.php?id=' . $producto_id . '&review_error=' . urlencode('Error al guardar la reseña: ' . $e->getMessage()));
    exit;
}
?>

