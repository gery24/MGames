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

// Procesar formulario de añadir juego
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_game') {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? '';
        $categoria = $_POST['categoria'] ?? '';
        

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
                    echo 'Error al mover el archivo.';
                }
            } else {
                echo 'Formato de imagen no permitido.';
            }
        } else {
            echo 'Error en la subida del archivo: ' . $_FILES['imagen']['error'];
        }

        // Insertar en la base de datos
        try {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria_id, segunda_mano, estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $categoria, $segunda_mano, $estado, $imagen]);
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
            $id = $_POST['id'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? '';
            $categoria = $_POST['categoria'] ?? '';
            $segunda_mano = 0; // Valor por defecto, ya que eliminamos la opción
            
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
                        echo 'Error al mover el archivo.';
                    }
                } else {
                    echo 'Formato de imagen no permitido.';
                }
            }
            
            // Actualizar en la base de datos
            try {
                $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria_id = ?, segunda_mano = ?, estado = ?, imagen = ? WHERE id = ?");
                $stmt->execute([$nombre, $descripcion, $precio, $categoria, $segunda_mano, $estado, $imagen, $id]);
                $success = 'Juego actualizado correctamente';
                $editando = false;
            } catch(PDOException $e) {
                $error = 'Error al actualizar el juego: ' . $e->getMessage();
            }
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
    } /* Añadir lógica para editar eventos si es necesario */
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
        if (isset($_FILES['imagen_blog_url']) && $_FILES['imagen_blog_url']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['imagen_blog_url']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newname = 'blog_' . uniqid() . '.' . $ext;
                $destination = 'images/blogs/' . $newname; // Asegúrate de que esta carpeta exista
                
                if (move_uploaded_file($_FILES['imagen_blog_url']['tmp_name'], $destination)) {
                    $imagen_blog_url = $destination; // Usar la ruta relativa para guardar en DB
                } else {
                    // Manejar error de movimiento de archivo
                    $error = 'Error al mover el archivo de imagen del blog.';
                }
            }
        } else if ($_FILES['imagen_blog_url']['error'] != UPLOAD_ERR_NO_FILE) {
            // Manejar otros errores de subida
             $error = 'Error en la subida del archivo de imagen del blog: ' . $_FILES['imagen_blog_url']['error'];
        }

        // Insertar en la base de datos (tabla 'articulos_blog')
        // Usar los nombres de columnas de la base de datos: titulo, contenido, imagen_url, categoria, autor, fecha_publicacion, slug (opcional)
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
    } /* Añadir lógica para editar blogs si es necesario */
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

// Incluir el header compartido
$titulo = "Panel de Administración - MGames";
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="panel_admin.css">

<div class="admin-panel">
    <h1>Panel de Administración</h1>

    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($editando && $producto_editar): ?>
        <h2>Editar Juego</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_game">
            <input type="hidden" name="id" value="<?php echo $producto_editar['id']; ?>">
            <input type="hidden" name="submit_edit" value="1">
            <input type="hidden" name="imagen_actual" value="<?php echo $producto_editar['imagen']; ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto_editar['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto_editar['precio']); ?>" required>
                </div>

                <div class="form-group" align="center">
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required align="center">
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo ($producto_editar['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                                <?php echo $categoria['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto_editar['descripcion']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="imagen">Imagen actual:</label>
                    <img src="<?php echo htmlspecialchars($producto_editar['imagen']); ?>" alt="Imagen actual" style="max-width: 200px; margin-bottom: 10px;">
                    <label for="imagen">Cambiar imagen (opcional):</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button">Guardar Cambios</button>
                    <a href="panel_admin.php" class="cancel-button">Cancelar</a>
                </div>
            </div>
        </form>
    <?php else: ?>
        <h2>Añadir Nuevo Juego</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_game">
            
            <div class="form-grid">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="precio">Precio:</label>
                        <input type="number" id="precio" name="precio" step="0.01" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" required>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="imagen">Imagen:</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>

                <button type="submit" class="submit-button">Añadir Juego</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<!-- Lista de juegos fuera del contenedor -->
<div class="admin-panel">
    <h2>Juegos Existentes</h2>
    <div class="products-grid">
        <?php foreach($productos as $producto): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                <div class="product-card-content">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                    <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                    <div class="button-group">
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_game">
                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" class="btn delete" onclick="return confirm('¿Estás seguro de querer eliminar este juego?')">Eliminar</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="action" value="edit_game">
                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" class="btn edit">Editar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Sección de Eventos -->
<div class="admin-panel">
    <h2>Añadir Nuevo Evento</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_event">
        
        <div class="form-grid">
            <div class="form-group">
                <label for="evento_titulo">Título:</label>
                <input type="text" id="evento_titulo" name="titulo" required>
            </div>

            <div class="form-group">
                <label for="evento_fecha">Fecha:</label>
                <input type="date" id="evento_fecha" name="fecha" required>
            </div>

            <div class="form-group">
                <label for="evento_hora">Hora:</label>
                <input type="time" id="evento_hora" name="hora">
            </div>

            <div class="form-group">
                <label for="evento_ubicacion">Ubicación:</label>
                <input type="text" id="evento_ubicacion" name="ubicacion">
            </div>

            <div class="form-group full-width">
                <label for="evento_descripcion">Descripción:</label>
                <textarea id="evento_descripcion" name="descripcion" required></textarea>
            </div>

            <div class="form-group full-width">
                <label for="imagen_evento">Imagen (opcional):</label>
                <input type="file" id="imagen_evento" name="imagen_evento" accept="image/*">
            </div>

            <button type="submit" class="submit-button">Añadir Evento</button>
        </div>
    </form>
</div>

<div class="admin-panel">
    <h2>Eventos Existentes</h2>
    <div class="products-grid"> <!-- Reutilizamos la clase products-grid por simplicidad, puedes crear una nueva si quieres estilos diferentes -->
        <?php
        // Obtener todos los eventos usando los nombres de columna correctos y ordenar
        $eventos = $pdo->query("SELECT id, nombre, imagen_url, fecha_evento, hora_evento, lugar, descripcion FROM eventos ORDER BY fecha_evento DESC, hora_evento DESC")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($eventos)) {
            echo "<p>No hay eventos disponibles.</p>";
        } else {
            foreach($eventos as $evento): ?>
                <div class="product-card"> <!-- Reutilizamos la clase product-card -->
                    <?php if (!empty($evento['imagen_url'])): // Usar imagen_url ?>
                        <img src="<?php echo htmlspecialchars($evento['imagen_url']); ?>" 
                             alt="<?php echo htmlspecialchars($evento['nombre']); // Usar nombre ?>">
                    <?php endif; ?>
                    <div class="product-card-content">
                        <h3><?php echo htmlspecialchars($evento['nombre']); // Usar nombre ?></h3>
                        <p>Fecha: <?php echo htmlspecialchars($evento['fecha_evento']); // Usar fecha_evento ?></p>
                        <?php if (!empty($evento['hora_evento'])): // Usar hora_evento ?><p>Hora: <?php echo htmlspecialchars($evento['hora_evento']); // Usar hora_evento ?></p><?php endif; ?>
                        <?php if (!empty($evento['lugar'])): // Usar lugar ?><p>Ubicación: <?php echo htmlspecialchars($evento['lugar']); // Usar lugar ?></p><?php endif; ?>
                        <p>Descripción: <?php echo htmlspecialchars(substr($evento['descripcion'], 0, 100)) . (strlen($evento['descripcion']) > 100 ? '...' : ''); ?></p>
                        <div class="button-group">
                            <form method="POST">
                                <input type="hidden" name="action" value="delete_event">
                                <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                                <button type="submit" class="btn delete" onclick="return confirm('¿Estás seguro de querer eliminar este evento?')">Eliminar</button>
                            </form>
                            <!-- Formulario de edición (opcional, puedes añadirlo después si lo necesitas) -->
                            <?php /*
                            <form method="POST">
                                <input type="hidden" name="action" value="edit_event">
                                <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
                                <button type="submit" class="btn edit">Editar</button>
                            </form>
                            */ ?>
                        </div>
                    </div>
                </div>
            <?php endforeach;
        }
        ?>
    </div>
</div>

<!-- Sección de Blogs -->
<div class="admin-panel">
    <h2>Añadir Nueva Entrada de Blog</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_blog">
        
        <div class="form-grid">
            <div class="form-group">
                <label for="blog_titulo">Título:</label>
                <input type="text" id="blog_titulo" name="titulo" required>
            </div>

            <div class="form-group">
                <label for="blog_categoria">Categoría:</label>
                <input type="text" id="blog_categoria" name="categoria" required>
            </div>

            <div class="form-group">
                <label for="blog_autor">Autor:</label>
                <input type="text" id="blog_autor" name="autor" required>
            </div>

            <div class="form-group full-width">
                <label for="blog_contenido">Contenido:</label>
                <textarea id="blog_contenido" name="contenido" required rows="10"></textarea>
            </div>

            <div class="form-group full-width">
                <label for="imagen_blog_url">Imagen (opcional):</label>
                <input type="file" id="imagen_blog_url" name="imagen_blog_url" accept="image/*">
            </div>

            <button type="submit" class="submit-button">Añadir Entrada de Blog</button>
        </div>
    </form>
</div>

<div class="admin-panel">
    <h2>Entradas de Blog Existentes</h2>
    <div class="products-grid"> <!-- Reutilizamos la clase products-grid -->
        <?php
        // Obtener todas las entradas de blog usando los nombres de columna y tabla correctos
        $blogs = $pdo->query("SELECT id, titulo, contenido, imagen_url, categoria, autor, fecha_publicacion FROM articulos_blog ORDER BY fecha_publicacion DESC")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($blogs)) {
            echo "<p>No hay entradas de blog disponibles.</p>";
        } else {
            foreach($blogs as $blog): ?>
                <div class="product-card"> <!-- Reutilizamos la clase product-card -->
                     <?php if (!empty($blog['imagen_url'])): // Usar imagen_url ?>
                        <img src="<?php echo htmlspecialchars($blog['imagen_url']); ?>" 
                             alt="<?php echo htmlspecialchars($blog['titulo']); ?>">
                    <?php endif; ?>
                    <div class="product-card-content">
                        <h3><?php echo htmlspecialchars($blog['titulo']); ?></h3>
                        <p>Categoría: <?php echo htmlspecialchars($blog['categoria']); ?></p>
                        <p>Autor: <?php echo htmlspecialchars($blog['autor']); ?></p>
                        <p>Publicado el: <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($blog['fecha_publicacion']))); ?></p>
                        <p>Contenido: <?php echo htmlspecialchars(substr($blog['contenido'], 0, 100)) . (strlen($blog['contenido']) > 100 ? '...' : ''); ?></p>
                        <div class="button-group">
                            <form method="POST">
                                <input type="hidden" name="action" value="delete_blog">
                                <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                <button type="submit" class="btn delete" onclick="return confirm('¿Estás seguro de querer eliminar esta entrada de blog?')">Eliminar</button>
                            </form>
                            <!-- Formulario de edición (opcional) -->
                             <?php /*
                            <form method="POST">
                                <input type="hidden" name="action" value="edit_blog">
                                <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                                <button type="submit" class="btn edit">Editar</button>
                            </form>
                            */ ?>
                        </div>
                    </div>
                </div>
            <?php endforeach;
        }
        ?>
    </div>
</div>

<!-- Botón -->
<!-- Botón scroll arriba -->
<button id="scrollToTopBtn" aria-label="Volver arriba">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
       stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
    <polyline points="18 15 12 9 6 15"></polyline>
  </svg>
</button>

<!-- Estilos CSS -->
<style>
 #scrollToTopBtn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 50px;
  height: 50px;
  background-color: #0d6efd; /* Azul Bootstrap */
  color: white;
  border: none;
  border-radius: 50%;
  display: none;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: background-color 0.3s, transform 0.3s;
  z-index: 1000;
}

#scrollToTopBtn:hover {
  background-color: #0b5ed7;
  transform: scale(1.1);
}

#scrollToTopBtn svg {
  width: 24px;
  height: 24px;
}
</style>

<!-- Script JS -->
<script>
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
</script>
<?php require_once 'includes/footer.php'; ?> 
<?php require_once 'includes/footer.php'; ?>
