<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'ADMIN') {
    echo 'No tienes permiso para realizar esta acción.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $usuario_id = $_SESSION['usuario']['id']; // Obtener el ID del usuario
    $puntuacion = 0; // Puntuación inicial
    $estado = $_POST['estado'] ?? 'Nuevo';

    // Procesar imagen
    $imagen = 'images/default.jpg';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = uniqid() . '.' . $ext;
            $destination = 'images/' . $newname;
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destination)) {
                $imagen = $destination;
            }
        }
    }

    try {
        // Insertar en la tabla productos
        $stmt = $pdo->prepare("INSERT INTO productos (usuario_id, nombre, descripcion, precio, categoria_id, puntuacion, estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $nombre, $descripcion, $precio, $categoria, $puntuacion, $estado, $imagen]);
        echo 'Juego añadido correctamente';
    } catch(PDOException $e) {
        echo 'Error al añadir el juego: ' . $e->getMessage();
    }
}
?> 