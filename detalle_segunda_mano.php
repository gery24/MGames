<?php
session_start();
require_once 'config/database.php';

// Verificar si se ha pasado un ID de producto
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Obtener información del producto de segunda mano
try {
    $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre FROM segunda_mano p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        header('Location: index.php');
        exit;
    }

    // Obtener juegos similares en la misma categoría
    $stmt_similares = $pdo->prepare("SELECT * FROM segunda_mano WHERE categoria_id = ? AND id != ? ORDER BY RAND()");
    $stmt_similares->execute([$producto['categoria_id'], $id]);
    $juegos_similares = $stmt_similares->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

$titulo = htmlspecialchars($producto['nombre']);
require_once 'includes/header.php';
?>

<div class="product-container">
    <div class="product-header">
        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
        <div class="product-info">
            <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
            <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
            
            <!-- Formulario para añadir al carrito -->
            <form method="POST" action="agregar_al_carrito.php">
                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                <button type="submit" class="btn">Añadir al carrito</button>
            </form>
            
            <p class="platform">Plataforma: <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
        </div>
    </div>

    <div class='product-section'>
        <h2 class='section-title'>Acerca del juego</h2>
        <?php if (isset($producto['acerca_de'])): ?>
            <p><?php echo htmlspecialchars($producto['acerca_de']); ?></p>
        <?php endif; ?>
    </div>

    <div class='product-section'>
        <h2 class='section-title'>Descripción</h2>
        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
    </div>

    <?php if (!empty($producto['descripcion_adicional'])): ?>
        <div class='product-section'>
            <h2 class='section-title'>Descripción adicional</h2>
            <p><?php echo htmlspecialchars($producto['descripcion_adicional']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($producto['comentario'])): ?>
        <div class='product-section'>
            <h2 class='section-title'>Comentarios adicionales</h2>
            <p><?php echo htmlspecialchars($producto['comentario']); ?></p>
        </div>
    <?php endif; ?>

<section class="similar-games-section">
    <h2 class="similar-games-title">Juegos similares</h2>
    <div class="similar-games-grid">
        <?php foreach ($juegos_similares as $juego): ?>
            <div class="game-card">
                <img src="<?php echo htmlspecialchars($juego['imagen']); ?>" alt="<?php echo htmlspecialchars($juego['nombre']); ?>" class="game-image">
                <div class="game-info">
                    <h3 class="game-title"><?php echo htmlspecialchars($juego['nombre']); ?></h3>
                    <p class="game-price">€<?php echo number_format($juego['precio'], 2); ?></p>
                    <a href="detalle_segunda_mano.php?id=<?php echo $juego['id']; ?>" class="view-details">Ver detalles</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?> 