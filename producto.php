<?php
session_start();
require_once 'config/database.php';

// Obtener el ID del producto de la URL
$producto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Obtener los detalles del producto
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

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
} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

$titulo = $producto['nombre'] . " - MGames";
require_once 'includes/header.php';
?>

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
}

.product-header {
    display: flex;
    gap: 2rem;
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-image {
    width: 400px;
    height: auto;
    object-fit: cover;
    border-radius: 10px;
}

.product-info {
    flex: 1;
}

.product-info h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.price {
    font-size: 1.5rem;
    color: #4747ff;
    font-weight: bold;
    margin: 1rem 0;
}

.category {
    display: inline-block;
    background: #f3f4f6;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    margin: 1rem 0;
}

.product-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.add-to-wishlist {
    background-color: #ff4747 !important;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.add-to-wishlist:hover {
    background-color: #ff3333 !important;
}

/* Estilos para las secciones de detalles */
.product-sections {
    margin-top: 2rem;
}

.product-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.product-section h2 {
    color: #333;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.requirements {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.min-requirements, .rec-requirements {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

.requirements h3 {
    color: #4747ff;
    margin-bottom: 1rem;
}

.requirements ul {
    list-style: none;
    padding: 0;
}

.requirements li {
    margin-bottom: 0.5rem;
    padding-left: 1.5rem;
    position: relative;
}

.requirements li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #4747ff;
}

@media (max-width: 768px) {
    .product-header {
        flex-direction: column;
    }
    
    .product-image {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .requirements {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wishlistButton = document.querySelector('.add-to-wishlist');
    
    wishlistButton.addEventListener('click', async function(e) {
        e.preventDefault();
        
        const productId = this.getAttribute('data-product-id');
        
        try {
            const response = await fetch('add_to_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ productId: productId })
            });

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
            const data = await response.json();
            
            if (data.success) {
                // Si la operación fue exitosa, redirigir a lista_deseos.php
                window.location.href = 'lista_deseos.php';
            } else {
                // Si hay un error, mostrar el mensaje
                if (data.message === 'Debes iniciar sesión') {
                    window.location.href = 'login.php';
                } else {
                    alert(data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ha ocurrido un error al procesar tu solicitud');
        }
    });
});
</script>

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
<?php require_once 'includes/footer.php'; ?>