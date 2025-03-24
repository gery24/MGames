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
    <style>
        body {
            background-color: #ffffff;
            color: #333333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .product-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .product-header {
            display: flex; /* Cambiar a flexbox */
            align-items: center; /* Centrar verticalmente */
            gap: 30px; /* Espacio entre la imagen y el contenido */
            margin-bottom: 40px;
            background-color: #f8f9fa; /* Fondo claro para el encabezado del producto */
            padding: 20px;
            border-radius: 10px;
        }

        .product-image {
            width: 300px; /* Ajustar el tamaño de la imagen */
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .product-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-title {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #333;
        }

        .price {
            font-size: 2em;
            color: #2563eb;
            margin-bottom: 20px;
        }

        .buy-button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .buy-button:hover {
            background-color: #1d4ed8;
        }

        .product-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #2563eb;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .similar-games-section {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .similar-games-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #333;
        }

        /* Modificación para mostrar los juegos horizontalmente */
        .similar-games-grid {
            display: flex; /* Cambiar a flexbox para layout horizontal */
            flex-wrap: nowrap; /* Evitar que se envuelvan a la siguiente línea */
            gap: 1.5rem;
            overflow-x: auto; /* Permitir desplazamiento horizontal si hay muchos juegos */
            padding-bottom: 1rem; /* Espacio para la barra de desplazamiento */
            scrollbar-width: thin; /* Para Firefox */
            -ms-overflow-style: none; /* Para IE y Edge */
        }

        /* Ocultar la barra de desplazamiento en Chrome, Safari y Opera */
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
            flex: 0 0 250px; /* Ancho fijo para cada tarjeta */
            max-width: 250px; /* Asegurar que no crezcan demasiado */
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
            padding: 1rem;
        }

        .game-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .game-price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
            margin-top: 0.5rem;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #ff4444;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .view-details {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 0.5rem;
            transition: background-color 0.2s;
        }

        .view-details:hover {
            background-color: #1d4ed8;
        }

        /* Estilos para las estrellas */
        .review {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 5px solid #007bff; /* Color de la barra lateral */
        }

        .review p {
            margin: 5px 0;
        }

        .star-rating {
            direction: rtl; /* Para que las estrellas se seleccionen de derecha a izquierda */
            display: flex;
            justify-content: flex-start;
        }

        .star {
            font-size: 30px; /* Tamaño de las estrellas */
            color: #ddd; /* Color por defecto */
            cursor: pointer;
        }

        .star:hover,
        .star:hover ~ .star {
            color: #ff6600; /* Color al pasar el ratón */
        }

        input[type="radio"] {
            display: none; /* Ocultar los botones de radio */
        }

        input[type="radio"]:checked ~ .star {
            color: #ff6600; /* Color de las estrellas seleccionadas */
        }

        /* Estrellas para la media de reseñas */
        .rating-stars {
            display: inline-flex;
            margin-left: 5px;
        }

        .rating-stars .star {
            cursor: default;
        }

        .rating-stars .star.filled {
            color: #ff6600; /* Color de estrellas llenas */
        }

        .rating-stars .star.half-filled {
            position: relative;
            color: #ddd;
        }

        .rating-stars .star.half-filled:before {
            content: "★";
            position: absolute;
            color: #ff6600;
            width: 50%;
            overflow: hidden;
        }

        .rating-stars .star.empty {
            color: #ddd; /* Color de estrellas vacías */
        }

        /* Estrellas para las reseñas individuales */
        .review-stars {
            display: inline-flex;
        }

        .review-stars .star {
            font-size: 20px; /* Tamaño más pequeño para las reseñas individuales */
            cursor: default;
        }

        .review-stars .star.filled {
            color: #ff6600;
        }

        .review-stars .star.empty {
            color: #ddd;
        }

        /* Estilos para el formulario de reseña */
        .rating-container {
            margin-bottom: 15px;
        }

        .rating-label {
            display: block;
            margin-bottom: 5px;
        }

        .user-star-rating {
            display: flex;
            flex-direction: row-reverse; /* Para que las estrellas se seleccionen de derecha a izquierda */
            justify-content: flex-end; /* Alinear a la izquierda */
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

        @media (max-width: 768px) {
            .product-header {
                flex-direction: column; /* Apilar en móviles */
                align-items: flex-start;
            }

            .product-image {
                width: 100%; /* Imagen a ancho completo en móviles */
                max-width: 300px;
                margin: 0 auto;
            }

            .requirements-grid {
                grid-template-columns: 1fr;
            }

            /* Mantener el scroll horizontal en móviles */
            .similar-games-grid {
                padding-bottom: 0.5rem;
            }

            .game-card {
                flex: 0 0 200px; /* Tarjetas más pequeñas en móviles */
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="product-container">
        <div class="product-header">
            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
            <div class="product-info">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                <p class="platform">Categoria: <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>                
                <!-- Formulario modificado para añadir al carrito -->
                <form method="POST" action="agregar_al_carrito.php">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                    <button type="submit" class="btn buy-button">Añadir al carrito</button>
                </form>
                    <br>
                <form method="POST" action="agregar_lista_deseos.php">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <button type="submit" class="btn buy-button">Añadir a lista de deseos</button>
                </form> 
                
            </div>
        </div>

        <div class="product-section">
            <h2 class="section-title">Acerca del juego</h2>
            <p><?php echo htmlspecialchars($producto['acerca_de']); ?></p>
        </div>

        <div class="product-section">
            <h2 class="section-title">Requisitos del sistema</h2>
            <div class="requirements-grid">
                <div>
                    <h3>Mínimos</h3>
                    <p><?php echo htmlspecialchars($producto['reqmin']); ?></p>
                </div>
                <div>
                    <h3>Recomendados</h3>
                    <p><?php echo htmlspecialchars($producto['reqmax']); ?></p>
                </div>
            </div>
        </div>

        <!-- Mostrar la media de las reseñas -->
        <?php if ($reseñas['total'] > 0): ?>
            <div class="product-section">
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

        <div class="review-section">
            <h2 class="section-title">Deja tu reseña</h2>
            <form method="POST" action="guardar_reseña.php">
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
                <button type="submit" class="btn">Enviar Reseña</button>
            </form>
        </div>

        <!-- Mostrar las reseñas -->
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

        if ($lista_reseñas) {
            echo "<h3>Reseñas:</h3>";
            foreach ($lista_reseñas as $reseña) {
                echo "<div class='review'>";
                echo "<p><strong>" . htmlspecialchars($reseña['nombre']) . "</strong> - ";
                echo "<span class='review-stars'>";
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $reseña['puntuacion']) {
                        echo "<span class='star filled'>&#9733;</span>"; // Estrella llena
                    } else {
                        echo "<span class='star empty'>&#9733;</span>"; // Estrella vacía
                    }
                }
                echo "</span>";
                echo "</p>";
                echo "<p>" . htmlspecialchars($reseña['comentario']) . "</p>";
                echo "<p><em>" . htmlspecialchars($reseña['fecha']) . "</em></p>";
                echo "</div>";
            }
        } else {
            echo "<p>No hay reseñas para este producto.</p>";
        }
        ?>
    </div>

    <section class="similar-games-section">
        <h2 class="similar-games-title">Juegos similares</h2>
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
    </section>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>