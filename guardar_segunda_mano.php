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
    $condicion = $_POST['condicion'];
    $comentario = $_POST['comentario'];
    $usuario_id = $_SESSION['usuario']['id']; // Obtener el ID del usuario

    // Manejar las plataformas seleccionadas
    $plataformas_seleccionadas = $_POST['plataformas'] ?? [];
    $rutas_plataformas = [];
    $base_path = "fotosWeb/";

    foreach ($plataformas_seleccionadas as $plataforma) {
        // Construir la ruta de la imagen de la plataforma
        $plataforma_nombre_archivo = $plataforma; // Usar el valor por defecto
        if ($plataforma === 'playstation') {
            $plataforma_nombre_archivo = 'ps'; // Usar 'ps' para el nombre del archivo si el valor es 'playstation'
        }
        $rutas_plataformas[] = $base_path . htmlspecialchars($plataforma_nombre_archivo) . ".png";
    }

    // Rellenar con NULL si hay menos de 4 plataformas seleccionadas
    while (count($rutas_plataformas) < 4) {
        $rutas_plataformas[] = NULL;
    }

    // Manejo de la imagen (subida de archivos)
    $imagen = $_FILES['imagen']['name'];
    $target_dir = "fotosWeb/"; // Carpeta donde se guardará la imagen
    $target_file = $target_dir . basename($imagen);
    
    // Mover la imagen a la carpeta
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
        // Guardar en la base de datos en la tabla 'segunda_mano'
        // Asegurarse de que las columnas y el orden coincidan con la tabla segunda_mano
        // Se incluye usuario_id y comentario, y se usa condicion para la columna estado
        $stmt = $pdo->prepare("INSERT INTO segunda_mano (usuario_id, nombre, descripcion, precio, categoria_id, estado, imagen, plataforma1, plataforma2, plataforma3, plataforma4, comentario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Pasar los valores en el orden correcto
        $stmt->execute([$usuario_id, $nombre, $descripcion, $precio, $categoria, $condicion, $target_file, $rutas_plataformas[0], $rutas_plataformas[1], $rutas_plataformas[2], $rutas_plataformas[3], $comentario]);

        // Redirigir a la página de segunda mano (si existe) o a donde corresponda
        header("Location: segunda_mano.php?success=1");
        exit();
    } else {
        die("Error al subir la imagen.");
    }
} 