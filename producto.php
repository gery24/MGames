<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

try {
    // Obtener el producto y sus plataformas
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.id = ?
    ");
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

// Mostrar mensaje si hay un error o éxito en la reseña
if (isset($_GET['review_error'])) {
    echo '<p style="color: red;">Error al guardar la reseña: ' . htmlspecialchars($_GET['review_error']) . '</p>';
} elseif (isset($_GET['review_success'])) {
    echo '<p style="color: green;">¡Reseña guardada correctamente!</p>';
}
?>

<style>
    /* Variables para mantener consistencia en todo el sitio */
    :root {
        --primary-color: #4f46e5;
        --primary-dark: #4338ca;
        --secondary-color: #6366f1;
        --accent-color: #818cf8;
        --text-color: #1f2937;
        --text-light: #6b7280;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius: 0.5rem;
        --admin-color: #ff0000;
        --admin-dark: #cc0000;
        --admin-bg-light: #fff0f0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--text-color);
        line-height: 1.6;
        background-color: var(--bg-light);
        margin: 0;
        padding: 0;
    }

    .product-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Estilos de tarjeta principal */
    .card {
        background-color: var(--bg-white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 20px;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
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
        border-radius: var(--radius);
        object-fit: cover;
    }

    .product-info {
        flex: 1;
    }

    .product-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 15px 0;
        color: var(--text-color);
        line-height: 1.3;
    }

    .price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 15px 0;
    }

    .category {
        background-color: var(--bg-light);
        padding: 8px 15px;
        border-radius: var(--radius);
        margin: 15px 0;
        display: inline-block;
        font-size: 0.9rem;
        color: var(--text-color);
    }

    /* Botones */
    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius);
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
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
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    /* Secciones */
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        color: var(--text-color);
        position: relative;
    }

    .section-title:after {
        content: '';
        display: block;
        width: 50px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        margin: 0.5rem 0 0;
        border-radius: 2px;
    }

    .requirements-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .requirements-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary-color);
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
        color: var(--text-color);
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
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: var(--radius);
        margin-top: 8px;
        resize: vertical;
        font-family: inherit;
        font-size: 1rem;
    }

    textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    /* Reseñas */
    .review {
        background-color: var(--bg-light);
        border-radius: var(--radius);
        padding: 15px;
        margin-bottom: 15px;
        border-left: 3px solid var(--primary-color);
    }

    .review p {
        margin: 8px 0;
        line-height: 1.6;
    }

    .review strong {
        font-weight: 600;
        color: var(--text-color);
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
        height: 8px;
    }

    .similar-games-grid::-webkit-scrollbar-thumb {
        background-color: #c1c1c1;
        border-radius: 4px;
    }

    .game-card {
        position: relative;
        border-radius: var(--radius);
        overflow: hidden;
        background: var(--bg-white);
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: var(--shadow);
        flex: 0 0 250px;
        max-width: 250px;
    }

    .game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 8px 0;
        color: var(--text-color);
        line-height: 1.4;
    }

    .game-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-top: 10px;
    }

    .view-details {
        display: inline-block;
        background-color: var(--primary-color);
        color: white;
        padding: 8px 15px;
        border-radius: var(--radius);
        text-decoration: none;
        margin-top: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: background-color 0.3s, transform 0.3s;
    }

    .view-details:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
    }

    .discount-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #ef4444;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .login-message {
        background-color: var(--bg-light);
        border-left: 3px solid var(--primary-color);
        padding: 15px;
        margin-bottom: 15px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .login-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }

    .login-link:hover {
        text-decoration: underline;
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

    .platform img {
        width: 20px;
        height: auto;
        margin-right: 10px; /* Espacio entre las imágenes */
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

</style>

<div class="product-container">
    <!-- Tarjeta principal del producto -->
    <div class="card">
        <div class="product-header">
            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-image">
            <div class="product-info">
                <h1 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                <div class="category">
                    <?php echo isset($producto['categoria_nombre']) ? htmlspecialchars($producto['categoria_nombre']) : 'Categoría no disponible'; ?>
                </div>

                <!-- Mostrar plataformas justo debajo de la imagen del juego seleccionado -->
                <div class="platform">
                    <?php
                    // Mostrar las plataformas
                    $plataformas = [];
                    if (!empty($producto['plataforma1'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma1']);
                    }
                    if (!empty($producto['plataforma2'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma2']);
                    }
                    if (!empty($producto['plataforma3'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma3']);
                    }
                    if (!empty($producto['plataforma4'])) {
                        $plataformas[] = htmlspecialchars($producto['plataforma4']);
                    }

                    // Mostrar las imágenes de las plataformas solo si hay plataformas disponibles
                    if (!empty($plataformas)) {
                        foreach ($plataformas as $plataforma) {
                            echo '<img src="' . htmlspecialchars($plataforma) . '" alt="Plataforma" class="platform-img">';
                        }
                    } else {
                        echo '<p>No hay plataformas disponibles.</p>'; // Mensaje opcional si no hay plataformas
                    }
                    ?>
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
                        <button type="submit" class="btn btn-wishlist">
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
        <p><?php echo !empty($producto['acerca_de']) ? htmlspecialchars($producto['acerca_de']) : 'No hay descripción disponible para este juego.'; ?></p>
    </div>

    <!-- Requisitos del sistema -->
    <div class="card">
        <h2 class="section-title">Requisitos del Sistema</h2>
        <div class="requirements-grid">
            <div>
                <h3 class="requirements-title">Requisitos Mínimos</h3>
                <p><?php echo !empty($producto['reqmin']) ? htmlspecialchars($producto['reqmin']) : 'No se han especificado los requisitos mínimos para este juego.'; ?></p>
            </div>
            <div>
                <h3 class="requirements-title">Requisitos Recomendados</h3>
                <p><?php echo !empty($producto['reqmax']) ? htmlspecialchars($producto['reqmax']) : 'No se han especificado los requisitos recomendados para este juego.'; ?></p>
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
        
        <?php if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']['id'])): ?>
            <form method="POST" action="guardar_reseña.php" class="review-form">
                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['usuario']['id']; ?>">
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
                <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Enviar Reseña</button>
            </form>
        <?php else: ?>
            <div class="login-message">
                <div>
                    <p>Debes iniciar sesión para dejar una reseña.</p>
                    <a href="login.php" class="login-link">Iniciar sesión</a>
                </div>
            </div>
        <?php endif; ?>
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
                        <div class="category">
                        <?php echo isset($producto['categoria_nombre']) ? htmlspecialchars($producto['categoria_nombre']) : 'Categoría no disponible'; ?>
                        </div>
                        <div class="platform">
                            <?php
                            // Mostrar las plataformas
                            $plataformas = [];
                            if (!empty($juego['plataforma1'])) {
                                $plataformas[] = htmlspecialchars($juego['plataforma1']);
                            }
                            if (!empty($juego['plataforma2'])) {
                                $plataformas[] = htmlspecialchars($juego['plataforma2']);
                            }
                            if (!empty($juego['plataforma3'])) {
                                $plataformas[] = htmlspecialchars($juego['plataforma3']);
                            }
                            if (!empty($juego['plataforma4'])) {
                                $plataformas[] = htmlspecialchars($juego['plataforma4']);
                            }

                            // Mostrar las imágenes de las plataformas solo si hay plataformas disponibles
                            if (!empty($plataformas)) {
                                foreach ($plataformas as $plataforma) {
                                    echo '<img src="' . htmlspecialchars($plataforma) . '" alt="Plataforma" class="platform-img">';
                                }
                            } else {
                                echo '<p>No hay plataformas disponibles.</p>'; // Mensaje opcional si no hay plataformas
                            }
                            ?>
                        </div>
                        <a href="producto.php?id=<?php echo $juego['id']; ?>" class="view-details">
                            Ver detalles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mostrar últimas noticias del producto -->
    <div class="card">
        <h2 class="section-title">Últimas Noticias del Producto</h2>
        <p><?php echo !empty($producto['ultimas_noticias']) ? htmlspecialchars($producto['ultimas_noticias']) : 'No hay noticias disponibles para este producto.'; ?></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

