<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        header('Location: index.php');
        exit;
    }

    $stmt_similares = $pdo->prepare("SELECT * FROM productos WHERE categoria_id = ? AND id != ? LIMIT 4");
    $stmt_similares->execute([$producto['categoria_id'], $id]);
    $juegos_similares = $stmt_similares->fetchAll(PDO::FETCH_ASSOC);

    // Obtener la media de las reseñas
    $stmt_reseñas = $pdo->prepare("SELECT AVG(puntuacion) as media, COUNT(*) as total FROM reseñas WHERE producto_id = ?");
    $stmt_reseñas->execute([$id]);
    $reseñas = $stmt_reseñas->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

$titulo = htmlspecialchars($producto['nombre']);
require_once 'includes/header.php';

<div class="content">
    <div class="product-details">
        <div class="product-header">
            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                 class="product-image">
            <div class="product-info">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                
                <div class="product-actions">
                    <button class="btn add-to-cart">Añadir al Carrito</button>
                    <button class="btn add-to-wishlist" data-product-id="<?php echo $producto['id']; ?>">
                        <i class="fas fa-heart"></i> Añadir a Lista de Deseos
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de Detalles -->
        <div class="product-sections">
            <!-- Acerca del Juego -->
            <section class="product-section">
                <h2>Acerca del Juego</h2>
                <div class="section-content">
                    <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?>
                </div>
            </section>

            <!-- Requisitos del Sistema -->
            <section class="product-section">
                <h2>Requisitos del Sistema</h2>
                <div class="section-content">
                    <div class="requirements">
                        <div class="min-requirements">
                            <h3>Requisitos Mínimos</h3>
                            <?php if (!empty($producto['reqmin'])): ?>
                                <?php echo nl2br(htmlspecialchars($producto['reqmin'])); ?>
                            <?php else: ?>
                                <p>No se han especificado los requisitos mínimos para este juego.</p>
                            <?php endif; ?>
                        </div>
                        <div class="rec-requirements">
                            <h3>Requisitos Recomendados</h3>
                            <?php if (!empty($producto['reqmax'])): ?>
                                <?php echo nl2br(htmlspecialchars($producto['reqmax'])); ?>
                            <?php else: ?>
                                <p>No se han especificado los requisitos recomendados para este juego.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
.product-details {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
// Mostrar mensaje si el producto ya está en el carrito
if (isset($_GET['already_in_cart'])) {
    echo '<p style="color: red;">Este producto ya está en tu carrito.</p>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            color: #333333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Estilos de tarjeta principal */
        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 20px;
            overflow: hidden;
        }

        /* Estilos del producto */
        .product-header {
            display: flex;
            align-items: flex-start;
            gap: 30px;
        }

        .product-image {
            width: 300px;
            height: auto;
            border-radius: 5px;
            object-fit: cover;
        }

        .product-info {
            flex: 1;
        }

        .product-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 15px 0;
            color: #333;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            color: #4a4af4;
            margin: 15px 0;
        }

        .category {
            background-color: #f8f9fa;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 15px 0;
            display: inline-block;
        }

        /* Botones */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #4a4af4;
            color: white;
        }

        .btn-primary:hover {
            background-color: #3939d0;
        }

        .btn-wishlist {
            background-color: #e74c3c;
            color: white;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-wishlist:hover {
            background-color: #c0392b;
        }

        /* Secciones */
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .requirements-title {
            font-size: 16px;
            font-weight: bold;
            color: #4a4af4;
            margin-bottom: 10px;
        }

        /* Valoración y estrellas */
        .rating-container {
            margin-bottom: 15px;
        }

        .rating-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .rating-stars {
            display: inline-flex;
            margin-left: 5px;
        }

        .star {
            font-size: 24px;
            color: #ddd;
            cursor: default;
        }

        .star.filled {
            color: #ff6600;
        }

        .star.half-filled {
            position: relative;
            color: #ddd;
        }

        .star.half-filled:before {
            content: "★";
            position: absolute;
            color: #ff6600;
            width: 50%;
            overflow: hidden;
        }

        .star.empty {
            color: #ddd;
        }

        /* Estrellas para reseñas */
        .review-stars .star {
            font-size: 18px;
        }

        /* Formulario de reseña */
        .review-form {
            margin-top: 20px;
        }

        .user-star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .user-star-rating .star {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
            margin-right: 5px;
        }

        .user-star-rating .star:hover,
        .user-star-rating .star:hover ~ .star {
            color: #ff6600;
        }

        .user-star-rating input[type="radio"]:checked ~ .star {
            color: #ff6600;
        }

        input[type="radio"] {
            display: none;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
            resize: vertical;
        }

        /* Reseñas */
        .review {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid #4a4af4;
        }

        .review p {
            margin: 5px 0;
        }

        /* Juegos similares */
        .similar-games-grid {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 15px;
            scrollbar-width: thin;
        }

        .similar-games-grid::-webkit-scrollbar {
            height: 6px;
        }

        .similar-games-grid::-webkit-scrollbar-thumb {
            background-color: #c1c1c1;
            border-radius: 6px;
        }

        .game-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            transition: transform 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            flex: 0 0 250px;
            max-width: 250px;
        }

        .game-card:hover {
            transform: translateY(-4px);
        }

        .game-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .game-info {
            padding: 15px;
        }

        .game-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .game-price {
            font-size: 18px;
            font-weight: bold;
            color: #4a4af4;
            margin-top: 10px;
        }

        .view-details {
            display: inline-block;
            background-color: #4a4af4;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .view-details:hover {
            background-color: #3939d0;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #ff4444;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .product-header {
                flex-direction: column;
            }

            .product-image {
                width: 100%;
                max-width: 300px;
                margin: 0 auto 20px;
            }

            .requirements-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .game-card {
                flex: 0 0 200px;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Tarjeta principal del producto -->
        <div class="card">
            <div class="product-header">
                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                    <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                    <div class="category">
                        <?php echo htmlspecialchars($producto['categoria_nombre']); ?>
                    </div>
                    <div class="button-group">
                        <form method="POST" action="agregar_al_carrito.php">
                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                            <button type="submit" class="btn btn-primary">Añadir al Carrito</button>
                        </form>
                        <form method="POST" action="agregar_lista_deseos.php">
                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" class="btn btn-wishlist" data-product-id="<?php echo $producto['id']; ?>">
                                <i class="fas fa-heart"></i> Añadir a Lista de Deseos
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acerca del juego -->
        <div class="card">
            <h2 class="section-title">Acerca del juego</h2>
            <p><?php echo htmlspecialchars($producto['acerca_de']); ?></p>
        </div>

        <!-- Requisitos del sistema -->
        <div class="card">
            <h2 class="section-title">Requisitos del Sistema</h2>
            <div class="requirements-grid">
                <div>
                    <h3 class="requirements-title">Requisitos Mínimos</h3>
                    <p><?php echo htmlspecialchars($producto['reqmin']); ?></p>
                </div>
                <div>
                    <h3 class="requirements-title">Requisitos Recomendados</h3>
                    <p><?php echo htmlspecialchars($producto['reqmax']); ?></p>
                </div>
            </div>
        </div>

        <!-- Valoración -->
        <?php if ($reseñas['total'] > 0): ?>
        <div class="card">
            <h2 class="section-title">Valoración</h2>
            <p>Media de Reseñas: 
                <span class="rating-stars">
                    <?php 
                    $media = $reseñas['media'];
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($media)) {
                            // Estrella completa
                            echo "<span class='star filled'>&#9733;</span>";
                        } elseif ($i - 0.5 <= $media) {
                            // Media estrella
                            echo "<span class='star half-filled'>&#9733;</span>";
                        } else {
                            // Estrella vacía
                            echo "<span class='star empty'>&#9733;</span>";
                        }
                    }
                    ?>
                </span>
                (<?php echo number_format($reseñas['media'], 1); ?> de 5 - <?php echo $reseñas['total']; ?> reseñas)
            </p>
        </div>
        <?php endif; ?>

        <!-- Formulario de reseña -->
        <div class="card">
            <h2 class="section-title">Deja tu reseña</h2>
            <form method="POST" action="guardar_reseña.php" class="review-form">
                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                <div class="rating-container">
                    <label class="rating-label">Puntuación:</label>
                    <div class="user-star-rating">
                        <input type="radio" id="star5" name="puntuacion" value="5" required>
                        <label for="star5" class="star">&#9733;</label>
                        <input type="radio" id="star4" name="puntuacion" value="4">
                        <label for="star4" class="star">&#9733;</label>
                        <input type="radio" id="star3" name="puntuacion" value="3">
                        <label for="star3" class="star">&#9733;</label>
                        <input type="radio" id="star2" name="puntuacion" value="2">
                        <label for="star2" class="star">&#9733;</label>
                        <input type="radio" id="star1" name="puntuacion" value="1">
                        <label for="star1" class="star">&#9733;</label>
                    </div>
                </div>
                <div class="review-comment">
                    <label for="comentario">Comentario:</label>
                    <textarea id="comentario" name="comentario" rows="4" placeholder="Escribe tu reseña aquí..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Enviar Reseña</button>
            </form>
        </div>

        <!-- Reseñas existentes -->
        <?php
        $stmt_todas_reseñas = $pdo->prepare("
            SELECT r.*, u.nombre 
            FROM reseñas r 
            JOIN usuarios u ON r.usuario_id = u.id 
            WHERE r.producto_id = ? 
            ORDER BY r.fecha DESC
        ");
        $stmt_todas_reseñas->execute([$id]);
        $lista_reseñas = $stmt_todas_reseñas->fetchAll(PDO::FETCH_ASSOC);

        if ($lista_reseñas): ?>
        <div class="card">
            <h2 class="section-title">Reseñas</h2>
            <?php foreach ($lista_reseñas as $reseña): ?>
                <div class="review">
                    <p><strong><?php echo htmlspecialchars($reseña['nombre']); ?></strong> - 
                        <span class="review-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $reseña['puntuacion']): ?>
                                    <span class="star filled">&#9733;</span>
                                <?php else: ?>
                                    <span class="star empty">&#9733;</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </span>
                    </p>
                    <p><?php echo htmlspecialchars($reseña['comentario']); ?></p>
                    <p><em><?php echo htmlspecialchars($reseña['fecha']); ?></em></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="card">
            <h2 class="section-title">Reseñas</h2>
            <p>No hay reseñas para este producto.</p>
        </div>
        <?php endif; ?>

        <!-- Juegos similares -->
        <?php if ($juegos_similares): ?>
        <div class="card">
            <h2 class="section-title">Juegos similares</h2>
            <div class="similar-games-grid">
                <?php foreach ($juegos_similares as $juego): ?>
                    <div class="game-card">
                        <?php if (isset($juego['descuento']) && $juego['descuento'] > 0): ?>
                            <div class="discount-badge">
                                -<?php echo $juego['descuento']; ?>%
                            </div>
                        <?php endif; ?>
                        <img 
                            src="<?php echo htmlspecialchars($juego['imagen']); ?>" 
                            alt="<?php echo htmlspecialchars($juego['nombre']); ?>"
                            class="game-image"
                        >
                        <div class="game-info">
                            <h3 class="game-title"><?php echo htmlspecialchars($juego['nombre']); ?></h3>
                            <p class="game-price">€<?php echo number_format($juego['precio'], 2); ?></p>
                            <a href="producto.php?id=<?php echo $juego['id']; ?>" class="view-details">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>