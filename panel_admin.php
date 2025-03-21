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

// Procesar formulario de añadir juego
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_game') {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? '';
        $categoria = $_POST['categoria'] ?? '';
        $segunda_mano = isset($_POST['segunda_mano']) ? 1 : 0;
        $estado = $_POST['estado'] ?? 'Nuevo';

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

// Incluir el header compartido
$titulo = "Panel de Administración - MGames";
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="css/panel_admin.css">

<div class="admin-panel">
    <h1>Panel de Administración</h1>

    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <h2>Añadir Nuevo Juego</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_game">
        
        <div class="form-grid">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="Nuevo">Nuevo</option>
                    <option value="Usado">Usado</option>
                    <option value="Seminuevo">Seminuevo</option>
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
                    <form method="POST">
                        <input type="hidden" name="action" value="delete_game">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" class="btn delete" onclick="return confirm('¿Estás seguro de querer eliminar este juego?')">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 