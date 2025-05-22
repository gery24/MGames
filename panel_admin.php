<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'ADMIN') {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';
$editando = false;
$producto_editar = null;
$evento_editar = null;
$blog_editar = null;

// Procesar formulario de añadir juego
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_game') {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? '';
        $categoria = $_POST['categoria'] ?? '';
        $segunda_mano = isset($_POST['segunda_mano']) ? 1 : 0;
        $estado = $_POST['estado'] ?? 'Nuevo';
        
        // Procesar requisitos
        $reqmin = $_POST['reqmin'] ?? '';
        $reqmax = $_POST['reqmax'] ?? '';
        
        // Procesar plataformas
        $plataformas_seleccionadas = $_POST['plataformas'] ?? [];
        $plataforma_cols = ['plataforma1', 'plataforma2', 'plataforma3', 'plataforma4'];
        $plataformas_rutas = array_fill(0, 4, ''); // Inicializar con valores vacíos
        
        $plataformas_map = [
            'pc' => 'fotosWeb/pc.png',
            'ps' => 'fotosWeb/ps.png',
            'xbox' => 'fotosWeb/xbox.png',
            'switch' => 'fotosWeb/switch.png',
        ];
        
        $i = 0;
        foreach ($plataformas_seleccionadas as $plataforma) {
            if (isset($plataformas_map[$plataforma]) && $i < 4) {
                $plataformas_rutas[$i] = $plataformas_map[$plataforma];
                $i++;
            }
        }

        // Procesar imagen
        $imagen = 'images/default.jpg'; // Imagen por defecto
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagen']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newname = uniqid() . '.' . $ext;
                $destination = 'images/' . $newname;
                
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destination)) {
                    $imagen = $destination;
                } else {
                    $error = 'Error al mover el archivo.';
                }
            } else {
                $error = 'Formato de imagen no permitido.';
            }
        }

        // Insertar en la base de datos
        try {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria_id, segunda_mano, estado, imagen, plataforma1, plataforma2, plataforma3, plataforma4, reqmin, reqmax, descuento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $categoria, $segunda_mano, $estado, $imagen, $plataformas_rutas[0], $plataformas_rutas[1], $plataformas_rutas[2], $plataformas_rutas[3], $reqmin, $reqmax, $_POST['descuento'] ?? 0]);
            $success = 'Juego añadido correctamente';
        } catch(PDOException $e) {
            $error = 'Error al añadir el juego: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] == 'delete_game') {
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Juego eliminado correctamente';
        } catch(PDOException $e) {
            $error = 'Error al eliminar el juego: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] == 'edit_game') {
        $id = $_POST['id'] ?? '';
        
        // Si solo estamos cargando el formulario de edición
        if (!isset($_POST['submit_edit'])) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto_editar = $stmt->fetch();
                $editando = true;
            } catch(PDOException $e) {
                $error = 'Error al cargar el juego para editar: ' . $e->getMessage();
            }
        } else {
            // Estamos procesando el formulario de edición
            // Añadir depuración inicial
            error_log("Procesando formulario de edición para ID: " . ($_POST['id'] ?? 'N/A'));
            error_log("Reqmin recibido: " . ($_POST['reqmin'] ?? 'Vacio'));
            error_log("Reqmax recibido: " . ($_POST['reqmax'] ?? 'Vacio'));
            error_log("Descuento recibido: " . ($_POST['descuento'] ?? 'Vacio'));

            $id = $_POST['id'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? '';
            $categoria = $_POST['categoria'] ?? '';
            $segunda_mano = isset($_POST['segunda_mano']) ? 1 : 0;
            $estado = $_POST['estado'] ?? 'Nuevo';
            
            // Procesar requisitos para edición
            $reqmin = $_POST['reqmin'] ?? '';
            $reqmax = $_POST['reqmax'] ?? '';
            
            // Procesar plataformas para edición
            $plataformas_seleccionadas = $_POST['plataformas'] ?? [];
            $plataforma_cols = ['plataforma1', 'plataforma2', 'plataforma3', 'plataforma4'];
            $plataformas_rutas = array_fill(0, 4, ''); // Inicializar con valores vacíos
            
            $plataformas_map = [
                'pc' => 'fotosWeb/pc.png',
                'ps' => 'fotosWeb/ps.png',
                'xbox' => 'fotosWeb/xbox.png',
                'switch' => 'fotosWeb/switch.png',
            ];
            
            $i = 0;
            foreach ($plataformas_seleccionadas as $plataforma) {
                 if (isset($plataformas_map[$plataforma]) && $i < 4) {
                    $plataformas_rutas[$i] = $plataformas_map[$plataforma];
                    $i++;
                }
            }

            // Verificar si se ha subido una nueva imagen
            $imagen = $_POST['imagen_actual']; // Mantener la imagen actual por defecto
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['imagen']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $newname = uniqid() . '.' . $ext;
                    $destination = 'images/' . $newname;
                    
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destination)) {
                        $imagen = $destination;
                    } else {
                        $error = 'Error al mover el archivo.';
                    }
                } else {
                    $error = 'Formato de imagen no permitido.';
                }
            }
            
            // Actualizar en la base de datos
            try {
                $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria_id = ?, segunda_mano = ?, estado = ?, imagen = ?, plataforma1 = ?, plataforma2 = ?, plataforma3 = ?, plataforma4 = ?, reqmin = ?, reqmax = ?, descuento = ? WHERE id = ?");
                $stmt->execute([$nombre, $descripcion, $precio, $categoria, $segunda_mano, $estado, $imagen, $plataformas_rutas[0], $plataformas_rutas[1], $plataformas_rutas[2], $plataformas_rutas[3], $reqmin, $reqmax, $_POST['descuento'] ?? 0, $id]);
                $success = 'Juego actualizado correctamente';
                $editando = false;
            } catch(PDOException $e) {
                $error = 'Error al actualizar el juego: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] == 'delete_game_image') {
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            $producto = $stmt->fetch();
            
            if ($producto && $producto['imagen'] !== 'images/default.jpg') {
                // Eliminar archivo si existe y no es la imagen por defecto
                if (file_exists($producto['imagen'])) {
                    unlink($producto['imagen']);
                }
                
                // Actualizar la base de datos para usar la imagen por defecto
                $stmt = $pdo->prepare("UPDATE productos SET imagen = 'images/default.jpg' WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Imagen del juego eliminada correctamente';
            } else {
                $error = 'No se pudo eliminar la imagen o ya es la imagen por defecto.';
            }
        } catch(PDOException $e) {
            $error = 'Error al eliminar la imagen del juego: ' . $e->getMessage();
        }
    }
}

// --- Lógica para Eventos ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_event') {
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $hora = $_POST['hora'] ?? '';
        $ubicacion = $_POST['ubicacion'] ?? '';

        // Procesar imagen del evento
        $imagen_evento = ''; // Puede ser opcional, dependiendo del diseño
        if (isset($_FILES['imagen_evento']) && $_FILES['imagen_evento']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagen_evento']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newname = 'evento_' . uniqid() . '.' . $ext;
                $destination = 'images/eventos/' . $newname; // Asegúrate de que esta carpeta exista
                
                if (move_uploaded_file($_FILES['imagen_evento']['tmp_name'], $destination)) {
                    $imagen_evento = $destination;
                } else {
                    echo 'Error al mover el archivo de imagen del evento.';
                }
            } else {
                echo 'Formato de imagen de evento no permitido.';
            }
        } else if ($_FILES['imagen_evento']['error'] != UPLOAD_ERR_NO_FILE) {
            // Manejar otros errores de subida
             $error = 'Error en la subida del archivo de imagen del evento: ' . $_FILES['imagen_evento']['error'];
        }

        // Insertar en la base de datos (tabla 'eventos' asumida)
        if (empty($error)) { // Solo insertar si no hubo errores en la subida de imagen
            try {
                $stmt = $pdo->prepare("INSERT INTO eventos (nombre, descripcion, fecha_evento, hora_evento, lugar, imagen_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $descripcion, $fecha, $hora, $ubicacion, $imagen_evento]);
                $success = 'Evento añadido correctamente';
            } catch(PDOException $e) {
                $error = 'Error al añadir el evento: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] == 'delete_event') {
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Evento eliminado correctamente';
        } catch(PDOException $e) {
            $error = 'Error al eliminar el evento: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] == 'edit_event') {
        $id = $_POST['id'] ?? '';
        
        // Si solo estamos cargando el formulario de edición
        if (!isset($_POST['submit_edit'])) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
                $stmt->execute([$id]);
                $evento_editar = $stmt->fetch();
                $editando = true;
            } catch(PDOException $e) {
                $error = 'Error al cargar el evento para editar: ' . $e->getMessage();
            }
        } else {
            // Procesar edición de evento
            $id = $_POST['id'] ?? '';
            $titulo = $_POST['titulo'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $fecha = $_POST['fecha'] ?? '';
            $hora = $_POST['hora'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';
            
            // Verificar si se ha subido una nueva imagen
            $imagen_evento = $_POST['imagen_actual'] ?? ''; // Mantener la imagen actual por defecto
            
            if (isset($_FILES['imagen_evento']) && $_FILES['imagen_evento']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['imagen_evento']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $newname = 'evento_' . uniqid() . '.' . $ext;
                    $destination = 'images/eventos/' . $newname;
                    
                    if (move_uploaded_file($_FILES['imagen_evento']['tmp_name'], $destination)) {
                        $imagen_evento = $destination;
                    } else {
                        $error = 'Error al mover el archivo.';
                    }
                } else {
                    $error = 'Formato de imagen no permitido.';
                }
            }
            
            // Actualizar en la base de datos
            try {
                $stmt = $pdo->prepare("UPDATE eventos SET nombre = ?, descripcion = ?, fecha_evento = ?, hora_evento = ?, lugar = ?, imagen_url = ? WHERE id = ?");
                $stmt->execute([$titulo, $descripcion, $fecha, $hora, $ubicacion, $imagen_evento, $id]);
                $success = 'Evento actualizado correctamente';
                $editando = false;
            } catch(PDOException $e) {
                $error = 'Error al actualizar el evento: ' . $e->getMessage();
            }
        }
    }
}

// --- Lógica para Blogs ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_blog') {
        $titulo = $_POST['titulo'] ?? '';
        $contenido = $_POST['contenido'] ?? '';
        $categoria = $_POST['categoria'] ?? '';
        $autor = $_POST['autor'] ?? '';
        $fecha_publicacion = date('Y-m-d H:i:s'); // Fecha y hora actual

        // Procesar imagen del blog
        $imagen_blog_url = ''; // Puede ser opcional
        if (isset($_FILES['imagen_blog']) && $_FILES['imagen_blog']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagen_blog']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newname = 'blog_' . uniqid() . '.' . $ext;
                $destination = 'images/blogs/' . $newname; // Asegúrate de que esta carpeta exista
                
                if (move_uploaded_file($_FILES['imagen_blog']['tmp_name'], $destination)) {
                    $imagen_blog_url = $destination; // Usar la ruta relativa para guardar en DB
                } else {
                    // Manejar error de movimiento de archivo
                    $error = 'Error al mover el archivo de imagen del blog.';
                }
            }
        } else if ($_FILES['imagen_blog']['error'] != UPLOAD_ERR_NO_FILE) {
            // Manejar otros errores de subida
             $error = 'Error en la subida del archivo de imagen del blog: ' . $_FILES['imagen_blog']['error'];
        }

        // Insertar en la base de datos (tabla 'articulos_blog')
        if (empty($error)) { // Solo insertar si no hubo errores en la subida de imagen
            try {
                // Slug se puede generar automáticamente si es necesario, por ahora no lo incluimos en el insert
                $stmt = $pdo->prepare("INSERT INTO articulos_blog (titulo, contenido, imagen_url, categoria, autor, fecha_publicacion) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $contenido, $imagen_blog_url, $categoria, $autor, $fecha_publicacion]);
                $success = 'Entrada de blog añadida correctamente';
            } catch(PDOException $e) {
                $error = 'Error al añadir la entrada de blog: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] == 'delete_blog') {
        $id = $_POST['id'] ?? '';
        try {
            $stmt = $pdo->prepare("DELETE FROM articulos_blog WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Entrada de blog eliminada correctamente';
        } catch(PDOException $e) {
            $error = 'Error al eliminar la entrada de blog: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] == 'edit_blog') {
        $id = $_POST['id'] ?? '';
        
        // Si solo estamos cargando el formulario de edición
        if (!isset($_POST['submit_edit'])) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM articulos_blog WHERE id = ?");
                $stmt->execute([$id]);
                $blog_editar = $stmt->fetch();
                $editando = true;
            } catch(PDOException $e) {
                $error = 'Error al cargar el blog para editar: ' . $e->getMessage();
            }
        } else {
            // Procesar edición de blog
            $id = $_POST['id'] ?? '';
            $titulo = $_POST['titulo'] ?? '';
            $contenido = $_POST['contenido'] ?? '';
            $categoria = $_POST['categoria'] ?? '';
            $autor = $_POST['autor'] ?? '';
            
            // Verificar si se ha subido una nueva imagen
            $imagen_blog_url = $_POST['imagen_actual'] ?? ''; // Mantener la imagen actual por defecto
            
            if (isset($_FILES['imagen_blog']) && $_FILES['imagen_blog']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['imagen_blog']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $newname = 'blog_' . uniqid() . '.' . $ext;
                    $destination = 'images/blogs/' . $newname;
                    
                    if (move_uploaded_file($_FILES['imagen_blog']['tmp_name'], $destination)) {
                        $imagen_blog_url = $destination;
                    } else {
                        $error = 'Error al mover el archivo.';
                    }
                } else {
                    $error = 'Formato de imagen no permitido.';
                }
            }
            
            // Actualizar en la base de datos
            try {
                $stmt = $pdo->prepare("UPDATE articulos_blog SET titulo = ?, contenido = ?, imagen_url = ?, categoria = ?, autor = ? WHERE id = ?");
                $stmt->execute([$titulo, $contenido, $imagen_blog_url, $categoria, $autor, $id]);
                $success = 'Artículo actualizado correctamente';
                $editando = false;
            } catch(PDOException $e) {
                $error = 'Error al actualizar el artículo: ' . $e->getMessage();
            }
        }
    }
}

// Obtener categorías para el formulario
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();

// Obtener todos los juegos
$productos = $pdo->query("
    SELECT p.*, c.nombre as categoria_nombre 
    FROM productos p 
    LEFT JOIN categorias c ON p.categoria_id = c.id 
    ORDER BY p.id DESC
")->fetchAll();

// Obtener todos los eventos
try {
    $eventos = $pdo->query("SELECT * FROM eventos ORDER BY fecha_evento DESC")->fetchAll();
} catch(PDOException $e) {
    $eventos = [];
}

// Obtener todos los blogs
try {
    $blogs = $pdo->query("SELECT * FROM articulos_blog ORDER BY fecha_publicacion DESC")->fetchAll();
} catch(PDOException $e) {
    $blogs = [];
}

// Incluir el header compartido
$titulo = "Panel de Administración - MGames";
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/panel_admin.css">
    <style>
        /* Estilos adicionales para el nuevo diseño */
        .admin-modules {
            display: flex; /* Cambiado a flexbox para disposición horizontal */
            overflow-x: auto; /* Añadir scroll horizontal si es necesario */
            gap: 2rem; /* Espacio entre módulos */
            margin-bottom: 2rem;
            padding-bottom: 1rem; /* Espacio inferior para el scrollbar */
        }

        .admin-module {
            flex: 0 0 300px; /* Ancho fijo para los módulos y evitar que se encojan */
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .admin-module:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }

        .admin-module::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        .admin-module-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .admin-module-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .admin-module-description {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .module-content {
            display: none;
            margin-top: 2rem;
            animation: fadeIn 0.5s ease-in-out;
        }

        .module-content.active {
            display: block;
        }

        /* Estilos para el formulario de eventos */
        .event-form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .event-form-group {
            flex: 1;
        }

        /* Estilos para el formulario de blogs */
        .blog-form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .blog-form-group {
            flex: 1;
        }

        /* Estilos para las tarjetas de eventos */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }

        .event-image {
            height: 180px;
            overflow: hidden;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .event-card:hover .event-image img {
            transform: scale(1.05);
        }

        .event-content {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .event-date {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .event-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-color);
        }

        .event-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        /* Estilos mejorados para los bloques de texto de eventos */
        .event-description {
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            line-height: 1.5;
        }

        .event-description p {
            margin: 0;
            color: var(--text-color);
            font-size: 0.9rem;
            background: linear-gradient(to bottom, var(--text-color) 60%, transparent);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-fill-color: transparent;
        }

        .event-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: auto;
        }

        /* Estilos para las tarjetas de blogs */
        .blogs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .blog-card {
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }

        .blog-image {
            height: 180px;
            overflow: hidden;
        }

        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .blog-card:hover .blog-image img {
            transform: scale(1.05);
        }

        .blog-content {
            padding: 1.25rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .blog-category {
            display: inline-block;
            background-color: var(--page-bg);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 0.75rem;
        }

        .blog-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-color);
        }

        .blog-author {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .blog-date {
            color: var(--text-light);
            font-size: 0.75rem;
            margin-bottom: 1rem;
        }

        /* Estilos mejorados para los bloques de texto de blogs */
        .blog-excerpt {
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            line-height: 1.5;
        }

        .blog-excerpt p {
            margin: 0;
            color: var(--text-color);
            font-size: 0.9rem;
            font-style: italic;
            border-left: 3px solid var(--primary-color);
            padding-left: 0.75rem;
        }

        .blog-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: auto;
        }

        /* Animación para mostrar/ocultar contenido */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para el módulo activo */
        .admin-module.active {
            background-color: rgba(255, 0, 0, 0.05);
            border: 2px solid var(--primary-color);
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            .admin-modules {
                grid-template-columns: 1fr;
            }

            .event-form-row, .blog-form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        .empty-message {
            text-align: center;
            padding: 3rem 1rem;
            background-color: var(--page-bg);
            border-radius: var(--border-radius);
            margin: 1rem 0;
            grid-column: 1 / -1;
        }

        .empty-message i {
            font-size: 3rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .empty-message p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        /* Mejoras visuales para las tarjetas */
        .event-card, .blog-card {
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .event-card:hover, .blog-card:hover {
            border-color: var(--primary-color);
        }

        .event-title, .blog-title {
            position: relative;
            padding-bottom: 0.75rem;
        }

        .event-title::after, .blog-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, var(--primary-color), transparent);
        }

        /* Mejoras para los formularios de texto */
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        textarea {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: var(--input-bg, #fafafa); /* Variable para color de fondo */
            color: var(--text-color);
            width: 100%; /* Asegura que ocupen todo el ancho del contenedor */
            box-sizing: border-box; /* Incluye padding y borde en el ancho */
            max-width: 500px; /* Reducir el ancho máximo para textareas */
        }

        textarea {
            resize: vertical;
            min-height: 130px; /* Aumentar altura mínima para textareas (aprox +30%) */
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.1);
            background-color: white;
        }

        /* Estilo para checkboxes (segunda mano y plataformas) */
        .checkbox-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap; /* Permite que los checkboxes salten de línea */
            gap: 1rem; /* Espacio entre checkboxes */
        }

        /* Ocultar el checkbox nativo */
        .checkbox-group input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Crear un checkbox personalizado */
         .checkbox-group label {
            margin-bottom: 0; /* Ajustar margen inferior */
            font-weight: normal; /* Ajustar peso de fuente */
            cursor: pointer;
            color: var(--text-color);
            font-size: 1rem;
             display: flex; /* Para alinear correctamente */
             align-items: center;
             position: relative;
             padding-left: 1.8rem; /* Espacio para el cuadrado personalizado */
             user-select: none; /* Evitar selección de texto */
        }

        /* Diseño del cuadrado del checkbox personalizado */
        .checkbox-group label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1.2rem; /* Tamaño del cuadrado */
            height: 1.2rem; /* Tamaño del cuadrado */
            border: 1px solid var(--border-color);
            border-radius: 4px; /* Bordes ligeramente redondeados */
            background-color: var(--input-bg, #fafafa);
            transition: all 0.3s ease;
        }

        /* Estilo del checkmark cuando está marcado */
        .checkbox-group label::after {
            content: '\f00c'; /* Icono de Font Awesome (check) */
            font-family: 'Font Awesome 6 Pro', 'Font Awesome 6 Free'; /* Asegúrate de tener esta fuente */
            font-weight: 900; /* Para el icono sólido */
            position: absolute;
            left: 0.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color); /* Color del checkmark */
            font-size: 0.9rem;
            opacity: 0; /* Ocultar por defecto */
            transition: all 0.3s ease;
        }

        /* Mostrar el checkmark y cambiar fondo cuando el input está marcado */
        .checkbox-group input[type="checkbox"]:checked + label::before {
             background-color: var(--primary-color-light); /* Fondo ligero al marcar */
             border-color: var(--primary-color);
        }

         .checkbox-group input[type="checkbox"]:checked + label::after {
            opacity: 1; /* Mostrar el checkmark */
        }

        /* Estilo al pasar el ratón sobre el label del checkbox */
        .checkbox-group label:hover::before {
            border-color: var(--primary-color-dark);
            box-shadow: 0 0 0 3px var(--primary-color-light);
        }

        /* Estilo al enfocar el checkbox (accesibilidad) */
        .checkbox-group input[type="checkbox"]:focus + label::before {
             border-color: var(--primary-color-dark);
             box-shadow: 0 0 0 3px var(--primary-color-light);
        }


        /* Estilo para la subida de archivos */
        .file-upload-label {
            margin-bottom: 1rem; /* Espacio debajo de los iconos */
            display: flex;
            gap: 5px; /* Espacio reducido entre iconos */
            align-items: center;
        }

        .platform-icon {
            width: 18px; /* Tamaño muy pequeño para los iconos */
            height: 18px; /* Tamaño muy pequeño para los iconos */
            object-fit: contain;
            filter: grayscale(50%); /* Iconos ligeramente atenuados */
            transition: filter 0.3s ease;
        }

        .product-card:hover .platform-icon {
            filter: grayscale(0%); /* Iconos a color al pasar el ratón */
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: auto;
        }

        .product-actions .btn {
            /* ... existing code ... */
        }


        /* Estilos específicos para el botón de eliminar imagen en el formulario de edición */
        .delete-image-form {
            display: inline-block; /* Importante para que no ocupe todo el ancho */
            margin-left: 1rem; /* Espacio a la izquierda para separarlo de la imagen */
            vertical-align: middle; /* Alinear verticalmente con la imagen si es posible */
            margin-bottom: 1rem; /* Espacio debajo del botón */
        }

        .btn-sm {
            padding: 0.4rem 0.8rem; /* Padding más pequeño */
            font-size: 0.85rem; /* Tamaño de fuente más pequeño */
        }

        .btn-danger {
            background-color: var(--delete-color, #e74c3c); /* Color rojo para eliminar */
            color: white;
            border: none; /* Sin borde por defecto */
        }

        .btn-danger:hover {
            background-color: var(--delete-color-dark, #c0392b); /* Rojo más oscuro al pasar el ratón */
        }

        /* Estilos para las tarjetas de juegos */
        .products-grid {
            display: flex; /* Cambiado a flexbox para disposición horizontal */
            overflow-x: auto; /* Añadir scroll horizontal si es necesario */
            gap: 1.5rem; /* Espacio entre tarjetas */
            padding-bottom: 1rem; /* Espacio inferior para el scrollbar */
        }

        .product-card {
            flex: 0 0 280px; /* Ancho fijo para las tarjetas y evitar que se encojan */
            /* ... existing code ... */
        }

        .product-card:hover {
            /* ... existing code ... */
        }

        /* Estilos para el buscador de juegos */
        .card-actions .search-container {
            margin-left: auto; /* Empujar el buscador a la derecha */
             display: flex;
             align-items: center;
             gap: 0.75rem; /* Aumentar espacio entre icono y input */
        }

        .search-container input[type="text"] {
            padding: 0.5rem 0.75rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            font-size: 0.9rem;
             width: 150px; /* Reducir ancho del campo de búsqueda */
             transition: width 0.3s ease;
        }

         .search-container input[type="text"]:focus {
             width: 200px; /* Expandir al enfocar */
             border-color: var(--primary-color);
             box-shadow: 0 0 0 2px var(--primary-color-light);
         }

        .search-container i.fas {
            color: var(--text-light);
        }


        /* Estilos para el formulario de eventos */
        .event-form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: var(--input-bg, #fafafa); /* Variable para color de fondo */
            color: var(--text-color);
            width: 100%; /* Asegura que ocupen todo el ancho del contenedor */
            box-sizing: border-box; /* Incluye padding y borde en el ancho */
            max-width: 600px; /* Establecer ancho máximo para textareas */
        }

        textarea {
            resize: vertical;
            min-height: 130px; /* Aumentar altura mínima para textareas (aprox +30%) */
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.1);
            background-color: white;
        }
    </style>
</head>
<body class="admin">
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-cogs"></i> Panel de Administración</h1>
            <p class="admin-subtitle">Gestiona todos los aspectos de tu tienda MGames</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div class="stat-value"><?php echo count($productos); ?></div>
                <div class="stat-label">Juegos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">
                    <?php 
                        $users_count = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
                        echo $users_count;
                    ?>
                </div>
                <div class="stat-label">Usuarios</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value">
                    <?php 
                        $orders_count = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
                        echo $orders_count;
                    ?>
                </div>
                <div class="stat-label">Pedidos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-value">
                    <?php 
                        try {
                            // Intentar obtener el total de ventas - ajustar el nombre de la columna según tu base de datos
                            // Por ejemplo, podría ser 'precio', 'importe', 'monto', etc.
                            $total_sales = $pdo->query("SELECT SUM(precio) FROM pedidos")->fetchColumn();
                            echo number_format($total_sales ?? 0, 2);
                        } catch (PDOException $e) {
                            // Si hay un error, mostrar 0
                            echo "0.00";
                        }
                    ?>
                </div>
                <div class="stat-label">Ventas (€)</div>
            </div>
        </div>

        <!-- Módulos de administración -->
        <div class="admin-modules">
            <!-- Módulo de Juegos -->
            <div class="admin-module" id="games-module">
                <div class="admin-module-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <h2 class="admin-module-title">Juegos</h2>
                <p class="admin-module-description">Gestiona el catálogo de juegos</p>
            </div>

            <!-- Módulo de Eventos -->
            <div class="admin-module" id="events-module">
                <div class="admin-module-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h2 class="admin-module-title">Eventos</h2>
                <p class="admin-module-description">Administra eventos y lanzamientos</p>
            </div>

            <!-- Módulo de Blogs -->
            <div class="admin-module" id="blogs-module">
                <div class="admin-module-icon">
                    <i class="fas fa-blog"></i>
                </div>
                <h2 class="admin-module-title">Blog</h2>
                <p class="admin-module-description">Publica y edita artículos del blog</p>
            </div>
        </div>

        <!-- Contenido del módulo de Juegos -->
        <div class="module-content" id="games-content">
            <?php if ($editando && $producto_editar): ?>
                <!-- Formulario de edición de juego -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-edit"></i> Editar Juego</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="admin-form">
                            <input type="hidden" name="action" value="edit_game">
                            <input type="hidden" name="id" value="<?php echo $producto_editar['id']; ?>">
                            <input type="hidden" name="submit_edit" value="1">
                            <input type="hidden" name="imagen_actual" value="<?php echo $producto_editar['imagen']; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nombre">
                                        <i class="fas fa-tag"></i> Nombre del juego
                                    </label>
                                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto_editar['nombre']); ?>" required style="width: 70%;"> <!-- Ajuste de ancho aquí -->
                                </div>

                                <div class="form-group">
                                    <label for="precio">
                                        <i class="fas fa-euro-sign"></i> Precio
                                    </label>
                                    <div class="input-with-icon">
                                        <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto_editar['precio']); ?>" required style="width: 80px;"> <!-- Ajuste de ancho aquí -->
                                        <span class="input-icon">€</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="categoria">
                                        <i class="fas fa-folder"></i> Categoría
                                    </label>
                                    <div class="select-wrapper">
                                        <select id="categoria" name="categoria" required>
                                            <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?php echo $categoria['id']; ?>" <?php echo ($producto_editar['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="estado">
                                        <i class="fas fa-info-circle"></i> Estado
                                    </label>
                                    <div class="select-wrapper">
                                        <select id="estado" name="estado" required>
                                            <option value="Nuevo" <?php echo ($producto_editar['estado'] == 'Nuevo') ? 'selected' : ''; ?>>Nuevo</option>
                                            <option value="Usado" <?php echo ($producto_editar['estado'] == 'Usado') ? 'selected' : ''; ?>>Usado</option>
                                            <option value="Reacondicionado" <?php echo ($producto_editar['estado'] == 'Reacondicionado') ? 'selected' : ''; ?>>Reacondicionado</option>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion">
                                    <i class="fas fa-align-left"></i> Descripción
                                </label>
                                <textarea id="descripcion" name="descripcion" rows="5" required><?php echo htmlspecialchars($producto_editar['descripcion']); ?></textarea>
                            </div>

                            <!-- Campos de requisitos para edición -->
                             <div class="form-group">
                                <label for="reqmin">
                                    <i class="fas fa-list"></i> Requisitos Mínimos
                                </label>
                                 <textarea id="reqmin" name="reqmin" rows="4" placeholder="Introduce los requisitos mínimos del juego..."><?php echo htmlspecialchars($producto_editar['reqmin'] ?? ''); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="reqmax">
                                    <i class="fas fa-list-alt"></i> Requisitos Máximos
                                </label>
                                 <textarea id="reqmax" name="reqmax" rows="4" placeholder="Introduce los requisitos máximos del juego..."><?php echo htmlspecialchars($producto_editar['reqmax'] ?? ''); ?></textarea>
                            </div>

                            <!-- Campo de descuento para edición -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-percent"></i> Descuento
                                </label>
                                <div class="form-row align-items-center">
                                    <div class="form-group" style="flex: 0 0 auto; margin-right: 1rem;">
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="en_descuento_edit" name="en_descuento" value="1" <?php echo ($producto_editar['descuento'] > 0) ? 'checked' : ''; ?>>
                                            <label for="en_descuento_edit">Aplicar descuento</label>
                                        </div>
                                    </div>
                                     <div class="form-group" style="flex: 1;">
                                        <div class="input-with-icon">
                                             <input type="number" id="descuento_valor_edit" name="descuento" step="0.01" placeholder="Ej: 15.50" value="<?php echo htmlspecialchars($producto_editar['descuento'] ?? ''); ?>" style="width: 80px;">
                                             <span class="input-icon">%</span>
                                         </div>
                                     </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="segunda_mano" name="segunda_mano" <?php echo ($producto_editar['segunda_mano'] == 1) ? 'checked' : ''; ?>>
                                    <label for="segunda_mano">Producto de segunda mano</label>
                                </div>
                            </div>

                            <!-- Selección de plataformas para edición -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-gamepad"></i> Plataformas
                                </label>
                                <div class="checkbox-group">
                                    <?php 
                                        $plataformas_juego = [
                                            $producto_editar['plataforma1'],
                                            $producto_editar['plataforma2'],
                                            $producto_editar['plataforma3'],
                                            $producto_editar['plataforma4']
                                        ];
                                        $plataformas_disponibles = [
                                            'pc' => 'PC',
                                            'ps' => 'PlayStation',
                                            'xbox' => 'Xbox',
                                            'switch' => 'Switch',
                                        ];
                                        foreach ($plataformas_disponibles as $value => $label): 
                                            $checked = in_array('fotosWeb/' . $value . '.png', $plataformas_juego) ? 'checked' : '';
                                    ?>
                                        <input type="checkbox" id="plataforma_<?php echo $value; ?>" name="plataformas[]" value="<?php echo $value; ?>" <?php echo $checked; ?>>
                                        <label for="plataforma_<?php echo $value; ?>"><?php echo $label; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <!-- Campos de requisitos -->
                            <div class="form-group">
                                <label for="reqmin">
                                    <i class="fas fa-list"></i> Requisitos Mínimos
                                </label>
                                <textarea id="reqmin" name="reqmin" rows="4" placeholder="Introduce los requisitos mínimos del juego..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="reqmax">
                                    <i class="fas fa-list-alt"></i> Requisitos Máximos
                                </label>
                                <textarea id="reqmax" name="reqmax" rows="4" placeholder="Introduce los requisitos máximos del juego..."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-image"></i> Imagen actual
                                </label>
                                <div class="current-image">
                                    <img src="<?php echo htmlspecialchars($producto_editar['imagen']); ?>" alt="Imagen actual">
                                </div>
                                <?php if ($producto_editar['imagen'] !== 'images/default.jpg'): ?>
                                    <form method="POST" class="delete-image-form" onsubmit="return confirm('¿Estás seguro de que deseas eliminar la imagen actual?');">
                                        <input type="hidden" name="action" value="delete_game_image">
                                        <input type="hidden" name="id" value="<?php echo $producto_editar['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Borrar Imagen 
                                        </button>
                                       
                                    </form>
                                    <br> <br> 
                                <?php endif; ?>
                                <label for="imagen" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Cambiar imagen (opcional)
                                </label>
                                <input type="file" id="imagen" name="imagen" accept="image/*" class="file-upload">
                                <div class="file-name" id="file-name-display">Ningún archivo seleccionado</div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <a href="panel_admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulario para añadir juego -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-plus-circle"></i> Añadir Nuevo Juego</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="admin-form">
                            <input type="hidden" name="action" value="add_game">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nombre">
                                        <i class="fas fa-tag"></i> Nombre del juego
                                    </label>
                                    <input type="text" id="nombre" name="nombre" placeholder="Ej: God of War" required style="width: 70%;"> <!-- Ajuste de ancho aquí -->
                                </div>

                                <div class="form-group">
                                    <label for="precio">
                                        <i class="fas fa-euro-sign"></i> Precio
                                    </label>
                                    <div class="input-with-icon">
                                        <input type="number" id="precio" name="precio" step="0.01" placeholder="49.99" required style="width: 80px;"> <!-- Ajuste de ancho aquí -->
                                        <span class="input-icon">€</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="categoria">
                                        <i class="fas fa-folder"></i> Categoría
                                    </label>
                                    <div class="select-wrapper">
                                        <select id="categoria" name="categoria" required>
                                            <option value="" disabled selected>Selecciona una categoría</option>
                                            <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="estado">
                                        <i class="fas fa-info-circle"></i> Estado
                                    </label>
                                    <div class="select-wrapper">
                                        <select id="estado" name="estado" required>
                                            <option value="Nuevo" selected>Nuevo</option>
                                            <option value="Usado">Usado</option>
                                            <option value="Reacondicionado">Reacondicionado</option>
                                        </select>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion">
                                    <i class="fas fa-align-left"></i> Descripción
                                </label>
                                <textarea id="descripcion" name="descripcion" rows="5" placeholder="Describe el juego aquí..." required></textarea>
                            </div>

                            <!-- Campos de requisitos -->
                            <div class="form-group">
                                <label for="reqmin">
                                    <i class="fas fa-list"></i> Requisitos Mínimos
                                </label>
                                <textarea id="reqmin" name="reqmin" rows="4" placeholder="Introduce los requisitos mínimos del juego..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="reqmax">
                                    <i class="fas fa-list-alt"></i> Requisitos Máximos
                                </label>
                                <textarea id="reqmax" name="reqmax" rows="4" placeholder="Introduce los requisitos máximos del juego..."></textarea>
                            </div>

                            <!-- Campo de descuento -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-percent"></i> Descuento
                                </label>
                                <div class="form-row align-items-center">
                                    <div class="form-group" style="flex: 0 0 auto; margin-right: 1rem;">
                                        <div class="checkbox-group">
                                            <input type="checkbox" id="en_descuento" name="en_descuento" value="1">
                                            <label for="en_descuento">Aplicar descuento</label>
                                        </div>
                                    </div>
                                     <div class="form-group" style="flex: 1;">
                                        <div class="input-with-icon">
                                             <input type="number" id="descuento_valor" name="descuento" step="0.01" placeholder="Ej: 15.50" style="width: 80px;">
                                             <span class="input-icon">%</span>
                                         </div>
                                     </div>
                                </div>
                            </div>

                            <!-- Selección de plataformas -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-gamepad"></i> Plataformas
                                </label>
                                <div class="checkbox-group">
                                    <?php 
                                        $plataformas_disponibles = [
                                            'pc' => 'PC',
                                            'ps' => 'PlayStation',
                                            'xbox' => 'Xbox',
                                            'switch' => 'Switch',
                                        ];
                                        foreach ($plataformas_disponibles as $value => $label): 
                                    ?>
                                        <input type="checkbox" id="plataforma_<?php echo $value; ?>" name="plataformas[]" value="<?php echo $value; ?>">
                                        <label for="plataforma_<?php echo $value; ?>"><?php echo $label; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="imagen" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Subir imagen
                                </label>
                                <input type="file" id="imagen" name="imagen" accept="image/*" class="file-upload">
                                <div class="file-name" id="file-name-display">Ningún archivo seleccionado</div>
                                <div class="image-preview-container">
                                    <img id="image-preview" class="image-preview" src="#" alt="Vista previa">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Añadir Juego
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de juegos existentes -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Juegos Existentes</h2>
                        <div class="card-actions">
                            <div class="search-container">
                                <input type="text" id="search-games" placeholder="Buscar juegos...">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="products-grid">
                            <?php foreach($productos as $producto): ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                            alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        <?php if ($producto['segunda_mano'] == 1): ?>
                                            <div class="product-badge">Segunda mano</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-content">
                                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                        <div class="product-meta">
                                            <span class="product-price"><?php echo number_format($producto['precio'], 2); ?> €</span>
                                            <span class="product-category">
                                                <i class="fas fa-folder"></i> <?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría'); ?>
                                            </span>
                                            <?php if ($producto['descuento'] > 0): ?>
                                                 <span class="product-discount"><?php echo htmlspecialchars($producto['descuento']); ?>% Dto.</span>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Mostrar plataformas -->
                                        <div class="product-platforms">
                                            <?php 
                                                $plataformas_iconos = [
                                                    $producto['plataforma1'],
                                                    $producto['plataforma2'],
                                                    $producto['plataforma3'],
                                                    $producto['plataforma4']
                                                ];
                                                foreach ($plataformas_iconos as $icono_url): 
                                                    if (!empty($icono_url)):
                                            ?>
                                                <img src="<?php echo htmlspecialchars($icono_url); ?>" alt="Plataforma" class="platform-icon">
                                            <?php 
                                                    endif;
                                                endforeach; 
                                            ?>
                                        </div>
                                        <!-- Mostrar requisitos si existen -->
                                        <?php if (!empty($producto['reqmin']) || !empty($producto['reqmax'])): ?>
                                            <div class="product-requirements">
                                                <?php if (!empty($producto['reqmin'])): ?>
                                                    <p><strong>Mínimos:</strong> <?php echo nl2br(htmlspecialchars($producto['reqmin'])); ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($producto['reqmax'])): ?>
                                                    <p><strong>Máximos:</strong> <?php echo nl2br(htmlspecialchars($producto['reqmax'])); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="product-actions">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="edit_game">
                                                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                                <button type="submit" class="btn btn-edit">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                            </form>
                                            <form method="POST" class="delete-form">
                                                <input type="hidden" name="action" value="delete_game">
                                                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                                <button type="button" class="btn btn-delete" onclick="confirmDelete(this.parentElement)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($productos)): ?>
                                <div class="empty-message">
                                    <i class="fas fa-gamepad"></i>
                                    <p>No hay juegos en el catálogo. ¡Añade uno nuevo!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contenido del módulo de Eventos -->
        <div class="module-content" id="events-content">
            <?php if ($editando && $evento_editar): ?>
                <!-- Formulario de edición de evento -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-edit"></i> Editar Evento</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="admin-form">
                            <input type="hidden" name="action" value="edit_event">
                            <input type="hidden" name="id" value="<?php echo $evento_editar['id']; ?>">
                            <input type="hidden" name="submit_edit" value="1">
                            <input type="hidden" name="imagen_actual" value="<?php echo $evento_editar['imagen_url']; ?>">
                            
                            <div class="event-form-row">
                                <div class="event-form-group">
                                    <label for="titulo">
                                        <i class="fas fa-heading"></i> Título del evento
                                    </label>
                                    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($evento_editar['nombre']); ?>" required>
                                </div>

                                <div class="event-form-group">
                                    <label for="ubicacion">
                                        <i class="fas fa-map-marker-alt"></i> Ubicación
                                    </label>
                                    <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($evento_editar['lugar']); ?>" required>
                                </div>
                            </div>

                            <div class="event-form-row">
                                <div class="event-form-group">
                                    <label for="fecha">
                                        <i class="fas fa-calendar"></i> Fecha
                                    </label>
                                    <input type="date" id="fecha" name="fecha" value="<?php echo $evento_editar['fecha_evento']; ?>" required>
                                </div>

                                <div class="event-form-group">
                                    <label for="hora">
                                        <i class="fas fa-clock"></i> Hora
                                    </label>
                                    <input type="time" id="hora" name="hora" value="<?php echo $evento_editar['hora_evento']; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion_evento">
                                    <i class="fas fa-align-left"></i> Descripción
                                </label>
                                <textarea id="descripcion_evento" name="descripcion" rows="5" required><?php echo htmlspecialchars($evento_editar['descripcion']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-image"></i> Imagen actual
                                </label>
                                <?php if (!empty($evento_editar['imagen_url'])): ?>
                                <div class="current-image">
                                    <img src="<?php echo htmlspecialchars($evento_editar['imagen_url']); ?>" alt="Imagen actual">
                                        </div>
                                <?php else: ?>
                                <p>No hay imagen actual</p>
                                <?php endif; ?>
                                <label for="imagen_evento" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Cambiar imagen (opcional)
                                </label>
                                <input type="file" id="imagen_evento" name="imagen_evento" accept="image/*" class="file-upload">
                                <div class="file-name" id="event-file-name-display">Ningún archivo seleccionado</div>
                                    </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <a href="panel_admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                         </div>
                        </form>
                                     </div>
                                </div>
            <?php else: ?>
                <!-- Formulario para añadir evento -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-plus-circle"></i> Añadir Nuevo Evento</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="admin-form">
                            <input type="hidden" name="action" value="add_event">
                            
                            <div class="event-form-row">
                                <div class="event-form-group">
                                    <label for="titulo">
                                        <i class="fas fa-heading"></i> Título del evento
                                    </label>
                                    <input type="text" id="titulo" name="titulo" placeholder="Ej: Lanzamiento de God of War" required>
                                </div>

                                <div class="event-form-group">
                                    <label for="ubicacion">
                                        <i class="fas fa-map-marker-alt"></i> Ubicación
                                    </label>
                                    <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej: Centro Comercial Gran Vía" required>
                                </div>
                            </div>

                            <div class="event-form-row">
                                <div class="event-form-group">
                                    <label for="fecha">
                                        <i class="fas fa-calendar"></i> Fecha
                                    </label>
                                    <input type="date" id="fecha" name="fecha" required>
                                </div>

                                <div class="event-form-group">
                                    <label for="hora">
                                        <i class="fas fa-clock"></i> Hora
                                    </label>
                                    <input type="time" id="hora" name="hora" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion_evento">
                                    <i class="fas fa-align-left"></i> Descripción
                                </label>
                                <textarea id="descripcion_evento" name="descripcion" rows="5" placeholder="Describe el evento aquí..." required></textarea>
                            </div>

                            

                            <div class="form-group">
                                <label for="imagen_evento" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Subir imagen
                                </label>
                                <input type="file" id="imagen_evento" name="imagen_evento" accept="image/*" class="file-upload">
                                <div class="file-name" id="event-file-name-display">Ningún archivo seleccionado</div>
                                <div class="image-preview-container">
                                    <img id="event-image-preview" class="image-preview" src="#" alt="Vista previa">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Añadir Evento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de eventos existentes -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Eventos Programados</h2>
                        <div class="card-actions">
                            <div class="search-container">
                                <input type="text" id="search-events" placeholder="Buscar eventos...">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="events-grid">
                            <?php foreach($eventos as $evento): ?>
                                <div class="event-card">
                                    <div class="event-image">
                                        <img src="<?php echo !empty($evento['imagen_url']) ? htmlspecialchars($evento['imagen_url']) : 'images/default-event.jpg'; ?>" 
                                            alt="<?php echo htmlspecialchars($evento['nombre']); ?>">
                                    </div>
                                    <div class="event-content">
                                        <span class="event-date">
                                            <i class="fas fa-calendar"></i> 
                                            <?php 
                                                $fecha = new DateTime($evento['fecha_evento']);
                                                echo $fecha->format('d/m/Y'); 
                                            ?>
                                            <?php if (!empty($evento['hora_evento'])): ?>
                                                - <?php echo $evento['hora_evento']; ?>
                                            <?php endif; ?>
                                        </span>
                                        <h3 class="event-title"><?php echo htmlspecialchars($evento['nombre']); ?></h3>
                                        <div class="event-location">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <?php echo htmlspecialchars($evento['lugar'] ?? 'Ubicación no especificada'); ?>
                                        </div>
                                        <div class="event-description">
                                            <p><?php echo mb_substr(htmlspecialchars($evento['descripcion']), 0, 100) . (strlen($evento['descripcion']) > 100 ? '...' : ''); ?></p>
                                        </div>
                                        <div class="event-actions">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="edit_event">
                                                <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                                                <button type="submit" class="btn btn-edit">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                            </form>
                                            <form method="POST" class="delete-form">
                                                <input type="hidden" name="action" value="delete_event">
                                                <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                                                <button type="button" class="btn btn-delete" onclick="confirmDelete(this.parentElement)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($eventos)): ?>
                                <div class="empty-message">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>No hay eventos programados. ¡Añade uno nuevo!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contenido del módulo de Blogs -->
        <div class="module-content" id="blogs-content">
            <?php if ($editando && $blog_editar): ?>
                <!-- Formulario de edición de blog -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-edit"></i> Editar Artículo</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="admin-form">
                            <input type="hidden" name="action" value="edit_blog">
                            <input type="hidden" name="id" value="<?php echo $blog_editar['id']; ?>">
                            <input type="hidden" name="submit_edit" value="1">
                            <input type="hidden" name="imagen_actual" value="<?php echo $blog_editar['imagen_url']; ?>">
                            
                            <div class="blog-form-row">
                                <div class="blog-form-group">
                                    <label for="titulo_blog">
                                        <i class="fas fa-heading"></i> Título del artículo
                                    </label>
                                    <input type="text" id="titulo_blog" name="titulo" value="<?php echo htmlspecialchars($blog_editar['titulo']); ?>" required>
                                </div>

                                <div class="blog-form-group">
                                    <label for="categoria_blog">
                                        <i class="fas fa-folder"></i> Categoría
                                    </label>
                                    <input type="text" id="categoria_blog" name="categoria" value="<?php echo htmlspecialchars($blog_editar['categoria']); ?>" required>
                                </div>
                            </div>

                            <div class="blog-form-row">
                                <div class="blog-form-group">
                                    <label for="autor">
                                        <i class="fas fa-user"></i> Autor
                                    </label>
                                    <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($blog_editar['autor']); ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="contenido_blog">
                                    <i class="fas fa-align-left"></i> Contenido
                                </label>
                                <textarea id="contenido_blog" name="contenido" rows="10" required><?php echo htmlspecialchars($blog_editar['contenido']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>
                                    <i class="fas fa-image"></i> Imagen actual
                                </label>
                                <?php if (!empty($blog_editar['imagen_url'])): ?>
                                <div class="current-image">
                                    <img src="<?php echo htmlspecialchars($blog_editar['imagen_url']); ?>" alt="Imagen actual">
                                </div>
                                <?php else: ?>
                                <p>No hay imagen actual</p>
                                <?php endif; ?>
                                <label for="imagen_blog" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Cambiar imagen (opcional)
                                </label>
                                <input type="file" id="imagen_blog" name="imagen_blog" accept="image/*" class="file-upload">
                                <div class="file-name" id="blog-file-name-display">Ningún archivo seleccionado</div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <a href="panel_admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulario para añadir blog -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-plus-circle"></i> Añadir Nuevo Artículo</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="admin-form">
                            <input type="hidden" name="action" value="add_blog">
                            
                            <div class="blog-form-row">
                                <div class="blog-form-group">
                                    <label for="titulo_blog">
                                        <i class="fas fa-heading"></i> Título del artículo
                                    </label>
                                    <input type="text" id="titulo_blog" name="titulo" placeholder="Ej: Las 10 mejores novedades de 2023" required>
                                </div>

                                <div class="blog-form-group">
                                    <label for="categoria_blog">
                                        <i class="fas fa-folder"></i> Categoría
                                    </label>
                                    <input type="text" id="categoria_blog" name="categoria" placeholder="Ej: Noticias, Análisis, Guías..." required>
                                </div>
                            </div>

                            <div class="blog-form-row">
                                <div class="blog-form-group">
                                    <label for="autor">
                                        <i class="fas fa-user"></i> Autor
                                    </label>
                                    <input type="text" id="autor" name="autor" placeholder="Nombre del autor" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="contenido_blog">
                                    <i class="fas fa-align-left"></i> Contenido
                                </label>
                                <textarea id="contenido_blog" name="contenido" rows="10" placeholder="Escribe el contenido del artículo aquí..." required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="imagen_blog" class="file-upload-label">
                                    <i class="fas fa-upload"></i> Subir imagen destacada
                                </label>
                                <input type="file" id="imagen_blog" name="imagen_blog" accept="image/*" class="file-upload">
                                <div class="file-name" id="blog-file-name-display">Ningún archivo seleccionado</div>
                                <div class="image-preview-container">
                                    <img id="blog-image-preview" class="image-preview" src="#" alt="Vista previa">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Publicar Artículo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de blogs existentes -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Artículos Publicados</h2>
                        <div class="card-actions">
                            <div class="search-container">
                                <input type="text" id="search-blogs" placeholder="Buscar artículos...">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="blogs-grid">
                            <?php foreach($blogs as $blog): ?>
                                <div class="blog-card">
                                    <div class="blog-image">
                                        <img src="<?php echo !empty($blog['imagen_url']) ? htmlspecialchars($blog['imagen_url']) : 'images/default-blog.jpg'; ?>" 
                                            alt="<?php echo htmlspecialchars($blog['titulo']); ?>">
                                    </div>
                                    <div class="blog-content">
                                        <span class="blog-category">
                                            <i class="fas fa-folder"></i> 
                                            <?php echo htmlspecialchars($blog['categoria'] ?? 'Sin categoría'); ?>
                                        </span>
                                        <h3 class="blog-title"><?php echo htmlspecialchars($blog['titulo']); ?></h3>
                                        <div class="blog-author">
                                            <i class="fas fa-user"></i> 
                                            <?php echo htmlspecialchars($blog['autor'] ?? 'Anónimo'); ?>
                                        </div>
                                        <div class="blog-date">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?php 
                                                $fecha = new DateTime($blog['fecha_publicacion']);
                                                echo $fecha->format('d/m/Y H:i'); 
                                            ?>
                                        </div>
                                        <div class="blog-excerpt">
                                            <p><?php echo mb_substr(htmlspecialchars($blog['contenido']), 0, 120) . (strlen($blog['contenido']) > 120 ? '...' : ''); ?></p>
                                        </div>
                                        <div class="blog-actions">
                                            <form method="POST">
                                                <input type="hidden" name="action" value="edit_blog">
                                                <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                                <button type="submit" class="btn btn-edit">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                            </form>
                                            <form method="POST" class="delete-form">
                                                <input type="hidden" name="action" value="delete_blog">
                                                <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                                <button type="button" class="btn btn-delete" onclick="confirmDelete(this.parentElement)">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($blogs)): ?>
                                <div class="empty-message">
                                    <i class="fas fa-newspaper"></i>
                                    <p>No hay artículos publicados. ¡Escribe uno nuevo!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmar eliminación</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button id="cancel-delete" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button id="confirm-delete" class="btn btn-delete">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Botón scroll arriba -->
    <button id="scrollToTopBtn" aria-label="Volver arriba">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a los módulos
    const gamesModule = document.getElementById('games-module');
    const eventsModule = document.getElementById('events-module');
    const blogsModule = document.getElementById('blogs-module');
    
    // Obtener referencias a los contenidos
    const gamesContent = document.getElementById('games-content');
    const eventsContent = document.getElementById('events-content');
    const blogsContent = document.getElementById('blogs-content');
    
    // Ocultar todos los contenidos inicialmente
    gamesContent.style.display = 'none';
    eventsContent.style.display = 'none';
    blogsContent.style.display = 'none';
    
    // Función para mostrar un módulo específico
    function showModule(moduleElement, contentElement) {
        // Ocultar todos los contenidos
        gamesContent.style.display = 'none';
        eventsContent.style.display = 'none';
        blogsContent.style.display = 'none';
        
        // Quitar la clase active de todos los módulos
        gamesModule.classList.remove('active');
        eventsModule.classList.remove('active');
        blogsModule.classList.remove('active');
        
        // Mostrar el contenido seleccionado
        contentElement.style.display = 'block';
        
        // Activar el módulo correspondiente
        moduleElement.classList.add('active');
        
        // Hacer scroll suave hasta el contenido
        contentElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Asignar eventos de clic a los módulos
    gamesModule.addEventListener('click', function() {
        showModule(gamesModule, gamesContent);
    });
    
    eventsModule.addEventListener('click', function() {
        showModule(eventsModule, eventsContent);
    });
    
    blogsModule.addEventListener('click', function() {
        showModule(blogsModule, blogsContent);
    });
    
    // Mostrar el módulo correspondiente si hay un mensaje de éxito o error
    <?php if ($editando && $producto_editar): ?>
        showModule(gamesModule, gamesContent);
    <?php elseif ($editando && $evento_editar): ?>
        showModule(eventsModule, eventsContent);
    <?php elseif ($editando && $blog_editar): ?>
        showModule(blogsModule, blogsContent);
    <?php endif; ?>
    
    // Vista previa de imágenes para juegos
    const imageInput = document.getElementById('imagen');
    const imagePreview = document.getElementById('image-preview');
    const fileNameDisplay = document.getElementById('file-name-display');
    
    if (imageInput && imagePreview && fileNameDisplay) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                imagePreview.style.display = 'none';
                fileNameDisplay.textContent = 'Ningún archivo seleccionado';
            }
        });
    }
    
    // Vista previa de imágenes para eventos
    const eventImageInput = document.getElementById('imagen_evento');
    const eventImagePreview = document.getElementById('event-image-preview');
    const eventFileNameDisplay = document.getElementById('event-file-name-display');
    
    if (eventImageInput && eventImagePreview && eventFileNameDisplay) {
        eventImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    eventImagePreview.src = e.target.result;
                    eventImagePreview.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
                eventFileNameDisplay.textContent = this.files[0].name;
            } else {
                eventImagePreview.style.display = 'none';
                eventFileNameDisplay.textContent = 'Ningún archivo seleccionado';
            }
        });
    }
    
    // Vista previa de imágenes para blogs
    const blogImageInput = document.getElementById('imagen_blog');
    const blogImagePreview = document.getElementById('blog-image-preview');
    const blogFileNameDisplay = document.getElementById('blog-file-name-display');
    
    if (blogImageInput && blogImagePreview && blogFileNameDisplay) {
        blogImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    blogImagePreview.src = e.target.result;
                    blogImagePreview.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
                blogFileNameDisplay.textContent = this.files[0].name;
            } else {
                blogImagePreview.style.display = 'none';
                blogFileNameDisplay.textContent = 'Ningún archivo seleccionado';
            }
        });
    }
    
    // Botón de scroll arriba
    const scrollBtn = document.getElementById('scrollToTopBtn');
    
    window.addEventListener('scroll', () => {
        scrollBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
    });
    
    scrollBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Búsqueda de juegos
    const searchGamesInput = document.getElementById('search-games');
    if (searchGamesInput) {
        searchGamesInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const gameCards = document.querySelectorAll('.products-grid .product-card');
            
            gameCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const category = card.querySelector('.product-category').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || category.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // Búsqueda de eventos
    const searchEventsInput = document.getElementById('search-events');
    if (searchEventsInput) {
        searchEventsInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const eventCards = document.querySelectorAll('.events-grid .event-card');
            
            eventCards.forEach(card => {
                const title = card.querySelector('.event-title').textContent.toLowerCase();
                const location = card.querySelector('.event-location').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || location.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // Búsqueda de blogs
    const searchBlogsInput = document.getElementById('search-blogs');
    if (searchBlogsInput) {
        searchBlogsInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const blogCards = document.querySelectorAll('.blogs-grid .blog-card');
            
            blogCards.forEach(card => {
                const title = card.querySelector('.blog-title').textContent.toLowerCase();
                const category = card.querySelector('.blog-category').textContent.toLowerCase();
                const author = card.querySelector('.blog-author').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || category.includes(searchTerm) || author.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // Modal de confirmación para eliminar
    let formToSubmit = null;
    
    window.confirmDelete = function(form) {
        formToSubmit = form;
        document.getElementById('confirm-modal').style.display = 'flex';
    }
    
    const modal = document.getElementById('confirm-modal');
    const closeModal = document.querySelector('.close-modal');
    const cancelDelete = document.getElementById('cancel-delete');
    const confirmDelete = document.getElementById('confirm-delete');
    
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }
    
    if (cancelDelete) {
        cancelDelete.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }
    
    if (confirmDelete) {
        confirmDelete.addEventListener('click', () => {
            if (formToSubmit) {
                formToSubmit.submit();
            }
            modal.style.display = 'none';
        });
    }
    
    if (modal) {
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
</script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
