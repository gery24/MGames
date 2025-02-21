<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario tiene el rol "CLIENTE"
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'CLIENTE') {
    die("Acceso denegado. Solo los usuarios con rol 'CLIENTE' pueden añadir juegos de segunda mano.");
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/segunda_mano.css">
    <style>
        /* Estilos para el formulario */
        #add-game-form {
            margin: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #add-game-form input, #add-game-form select, #add-game-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #add-game-form button {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #add-game-form button:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <div class="logo">
                <a href="index.php">MGames</a>
            </div>
        </div>
        <div class="nav-right">
            <!-- Aquí va el contenido del navbar -->
        </div>
    </nav>

    <div id="add-game-form">
        <h2>Añadir Juego de Segunda Mano</h2>
        <form method="POST" action="guardar_segunda_mano.php" enctype="multipart/form-data">
            <input type="text" name="nombre" placeholder="Nombre del Juego" required>
            <input type="text" name="descripcion" placeholder="Descripción" required>
            <input type="number" name="precio" placeholder="Precio" required>
            <select name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo htmlspecialchars($categoria['id']); ?>">
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="estado" placeholder="Estado (Nuevo/Usado)" required>
            <input type="file" name="imagen" accept="image/*" required>
            <button type="submit">Añadir Juego</button>
        </form>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html> 