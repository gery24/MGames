<?php
session_start();
require_once 'config/database.php';

// Verificar si se ha pasado un ID de producto
if (!isset($_GET['id'])) {
    die("Error: ID de producto no especificado.");
}

$id = $_GET['id'];

try {
    // Obtener información del producto
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM segunda_mano p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Error: Producto no encontrado.");
    }

} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - MGames</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/segunda_mano.css">
    <style>
        .product-details {
            display: flex;
            margin: 20px;
        }

        .product-details img {
            width: 400px; /* Aumentar el tamaño de la imagen */
            height: auto;
            margin-right: 20px;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-info h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: black; /* Cambiar el color del texto a negro */
            background: none; /* Quitar el fondo gris */
        }

        .product-info p {
            font-size: 1.2rem;
            margin: 5px 0;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px; /* Espacio entre los botones */
        }

        .btn:hover {
            background-color: #0056b3;
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

    <div class="content">
        <header class="hero">
            <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
        </header>

        <div class="product-details">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            </div>
            <div class="product-info">
                <h2>Detalles del Producto</h2>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <p><strong>Precio:</strong> €<?php echo number_format($producto['precio'], 2); ?></p>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($producto['estado']); ?></p>
                
                <!-- Formulario para añadir al carrito -->
                <form method="POST" action="agregar_al_carrito.php">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <button type="submit" class="btn">Añadir al Carrito</button>
                </form>
                
                <a href="segunda_mano.php" class="btn">Volver a Segunda Mano</a>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html> 