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
    // Leer la condición del juego enviada por el formulario (campo 'condicion')
    $estado = $_POST['condicion']; // Guardamos el valor de 'condicion' en la variable '$estado' para usarlo en la columna 'estado'
    $comentario = $_POST['comentario'];
    $usuario_id = $_SESSION['usuario']['id']; // Obtener el ID del usuario

    // --- Manejo de las plataformas seleccionadas ---
    $plataformas_seleccionadas = $_POST['plataformas'] ?? [];
    
    // Preparar un array con espacio para 4 rutas de plataforma, inicializado a NULL
    $plataforma_cols = array_fill(0, 4, NULL);
    
    // Llenar el array con las rutas seleccionadas (hasta 4)
    foreach ($plataformas_seleccionadas as $index => $ruta_imagen_plataforma) {
        if ($index < 4) {
            $plataforma_cols[$index] = htmlspecialchars(trim($ruta_imagen_plataforma));
        }
    }
    // ----------------------------------------------

    // Manejo de la imagen principal del juego (subida de archivos)
    $imagen = $_FILES['imagen']['name'];
    $target_dir = "fotosWeb/"; // Carpeta donde se guardará la imagen
    $target_file = $target_dir . basename($imagen);
    
    // Mover la imagen a la carpeta
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
        // Guardar en la base de datos
        // Insertar en la tabla segunda_mano, incluyendo las columnas de plataforma
        $stmt = $pdo->prepare("INSERT INTO segunda_mano (nombre, descripcion, precio, categoria_id, estado, comentario, imagen, usuario_id, plataforma1, plataforma2, plataforma3, plataforma4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Ejecutar la sentencia, incluyendo los valores de las plataformas
        $stmt->execute([$nombre, $descripcion, $precio, $categoria, $estado, $comentario, $target_file, $usuario_id, $plataforma_cols[0], $plataforma_cols[1], $plataforma_cols[2], $plataforma_cols[3]]);

        header("Location: segunda_mano.php?success=1");
        exit();
    } else {
        die("Error al subir la imagen.");
    }
} 