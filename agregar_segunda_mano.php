<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario tiene el rol "CLIENTE"
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['CLIENTE', 'ADMIN'])) {
    die("Acceso denegado. Solo los usuarios con una cuenta creada pueden agregar juegos de segunda mano.");
}
// Obtener categorías de la base de datos
$stmt = $pdo->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Juego de Segunda Mano</title>
    <link rel="stylesheet" href="css/agregar_segundamano.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

    <div class='form-container'>
        <div id="add-game-form">
            <h2>Añadir Juego de Segunda Mano</h2>
            <form method="POST" action="guardar_segunda_mano.php" enctype="multipart/form-data">
                <input type="text" name="nombre" placeholder="Nombre del Juego" required>
                <input type="text" name="descripcion" placeholder="Descripción" required>
                <div class='form-group'>
                    <label for='comentario'>Comentario adicional:</label>
                    <textarea id='comentario' name='comentario' rows='4' placeholder='Añade cualquier comentario que quieras compartir sobre el juego...'></textarea>
                </div>
                <input type="number" name="precio" placeholder="Precio" required>
                <select name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['id']); ?>">
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class='form-group'>
                    <label>Condición del juego:</label>
                    <div class='radio-group'>
                        <label>
                            <input type='radio' name='condicion' value='Nuevo' required>
                            Nuevo
                        </label>
                        <label>
                            <input type='radio' name='condicion' value='Seminuevo'>
                            Seminuevo
                        </label>
                        <label>
                            <input type='radio' name='condicion' value='Usado'>
                            Usado
                        </label>
                    </div>
                </div>
                <input type="file" name="imagen" accept="image/*" required>
                <button type="submit">Añadir Juego</button>
            </form>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html> 