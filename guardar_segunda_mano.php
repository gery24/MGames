<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario tiene el rol "CLIENTE"
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'CLIENTE') {
    die("Acceso denegado. Solo los usuarios con rol 'CLIENTE' pueden añadir juegos de segunda mano.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $estado_form = $_POST['condicion'];
    $comentario = $_POST['comentario'];
    $usuario_id = $_SESSION['usuario']['id']; // Obtener el ID del usuario

    // Manejo de la imagen (subida de archivos)
    $imagen = $_FILES['imagen']['name'];
    $target_dir = "fotosWeb/"; // Carpeta donde se guardará la imagen
    $target_file = $target_dir . basename($imagen);
    
    // Mover la imagen a la carpeta
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
        // Guardar en la base de datos
        $stmt = $pdo->prepare("INSERT INTO segunda_mano (nombre, descripcion, precio, categoria_id, comentario, estado, imagen, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $categoria, $comentario, $estado_form, $target_file, $usuario_id]);

        header("Location: segunda_mano.php?success=1");
        exit();
    } else {
        die("Error al subir la imagen.");
    }
} 