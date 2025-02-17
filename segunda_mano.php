<?php
$titulo = "Segunda Mano - MGames";
require_once 'includes/header.php';
require_once 'config/database.php';

try {
    // Obtener productos de segunda mano
    $stmt = $pdo->query("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.segunda_mano = 1
        ORDER BY p.id DESC
    ");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<div class="content">
    <header class="hero segunda-mano-hero">
        <h1>Juegos de Segunda Mano</h1>
        <p>Encuentra grandes títulos a precios increíbles</p>
    </header>

    <section class="featured-products">
        <h2>Productos Disponibles</h2>
        <div class="products-grid">
            <?php foreach($productos as $producto): ?>
                <div class="product-card">
                    <img src="<?php echo $producto['imagen'] ?? 'images/default.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <div class="product-card-content">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                        <p class="estado"><?php echo htmlspecialchars($producto['estado'] ?? 'Usado'); ?></p>
                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?> 