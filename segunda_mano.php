<?php
session_start();
require_once 'config/database.php';

try {
    // Obtener productos de segunda mano
    $stmt = $pdo->query("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM segunda_mano p 
        LEFT JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.id DESC
    ");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segunda Mano - MGames</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="css/segunda_mano.css"> <!-- Estilos específicos -->
</head>
<body>
    </nav>

    <div class="content">
        <header class="hero">
            <h1>Bienvenido a MGames de Segunda Mano</h1>
            <p>Encuentra grandes títulos a precios increíbles</p>
        </header>

        <section class="featured-products">
            <h2>Productos Disponibles</h2>
            <div class="products-grid">
                <?php foreach($productos as $producto): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                            <p class="estado"><?php echo htmlspecialchars($producto['estado'] ?? 'Usado'); ?></p>
                            <a href="detalle_segunda_mano.php?id=<?php echo $producto['id']; ?>" class="btn">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="agregar_segunda_mano.php" class="btn">Añadir Juego de Segunda Mano</a>
        </section>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html> 