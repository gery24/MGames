<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario']['id']; // Asumiendo que el ID del usuario está en la sesión
    $puntuacion = $_POST['puntuacion'];
    $comentario = $_POST['comentario'];

    try {
        $stmt = $pdo->prepare("INSERT INTO reseñas (producto_id, usuario_id, puntuacion, comentario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$producto_id, $usuario_id, $puntuacion, $comentario]);

        $_SESSION['mensaje'] = 'Reseña guardada con éxito.';
        header("Location: producto.php?id=$producto_id");
        exit;
    } catch (PDOException $e) {
        die("Error al guardar la reseña: " . $e->getMessage());
    }
} else {
    die("Método de solicitud no válido.");
}
?> 