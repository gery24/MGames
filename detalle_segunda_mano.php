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
    $stmt_similares = $pdo->prepare("SELECT * FROM segunda_mano WHERE categoria_id = ? AND id != ? ORDER BY RAND() LIMIT 4");
    $stmt_similares->execute([$producto['categoria_id'], $id]);
    $juegos_similares = $stmt_similares->fetchAll(PDO::FETCH_ASSOC);

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
    <title><?php echo $titulo; ?> - Segunda Mano</title>
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
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0;
        }

        /* Estilos de tarjeta principal */
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 16px;
            padding: 24px;
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
            font-size: 28px;
            font-weight: bold;
            margin: 0 0 15px 0;
            color: #333;
        }

        .price {
            font-size: 28px;
            font-weight: bold;
            color: #6d28d9; /* Color morado para mantener consistencia */
            margin: 15px 0;
        }

        .category {
            background-color: #f8f9fa;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 15px 0;
            display: inline-block;
        }

        .estado-badge {
            background-color: #6d28d9;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            margin: 15px 10px 15px 0;
            display: inline-block;
            font-weight: 500;
            font-size: 0.85rem;
            width: auto;
            max-width: 100px;
            text-align: center;
        }

        /* Botones */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #6d28d9;
            color: white;
            box-shadow: 0 4px 6px rgba(109, 40, 217, 0.2);
            width: 100%;
            max-width: 250px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            background-color: #5b21b6;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(109, 40, 217, 0.3);
        }

        /* Secciones */
        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        /* Juegos similares */
        .similar-games-grid {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 15px;
            scrollbar-width: thin;
            justify-content: center;
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
            color: #6d28d9;
            margin-top: 10px;
        }

        .view-details {
            display: inline-block;
            background-color: #6d28d9;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .view-details:hover {
            background-color: #5b21b6;
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
                    <div class="estado-badge">
                        <?php echo htmlspecialchars($producto['estado'] ?? 'Usado'); ?>
                    </div>
                    <div class="category">
                        <?php echo htmlspecialchars($producto['categoria_nombre']); ?>
                    </div>
                    <div class="button-group">
                        <form method="POST" action="agregar_al_carrito.php">
                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Añadir al Carrito
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acerca del juego -->
        <?php if (isset($producto['acerca_de']) && !empty($producto['acerca_de'])): ?>
        <div class="card">
            <h2 class="section-title">Acerca del juego</h2>
            <p><?php echo htmlspecialchars($producto['acerca_de']); ?></p>
        </div>
        <?php endif; ?>

        <!-- Descripción -->
        <div class="card">
            <h2 class="section-title">Descripción</h2>
            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
        </div>

        <!-- Descripción adicional -->
        <?php if (!empty($producto['descripcion_adicional'])): ?>
        <div class="card">
            <h2 class="section-title">Descripción adicional</h2>
            <p><?php echo htmlspecialchars($producto['descripcion_adicional']); ?></p>
        </div>
        <?php endif; ?>

        <!-- Comentarios adicionales -->
        <?php if (!empty($producto['comentario'])): ?>
        <div class="card">
            <h2 class="section-title">Comentarios adicionales</h2>
            <p><?php echo htmlspecialchars($producto['comentario']); ?></p>
        </div>
        <?php endif; ?>

        <!-- Juegos similares -->
        <?php if ($juegos_similares): ?>
        <div class="card">
            <h2 class="section-title">Juegos similares</h2>
            <div style="text-align: center;">
                <div class="similar-games-grid">
                    <?php foreach ($juegos_similares as $juego): ?>
                        <div class="game-card">
                            <img 
                                src="<?php echo htmlspecialchars($juego['imagen']); ?>" 
                                alt="<?php echo htmlspecialchars($juego['nombre']); ?>"
                                class="game-image"
                            >
                            <div class="game-info">
                                <h3 class="game-title"><?php echo htmlspecialchars($juego['nombre']); ?></h3>
                                <p class="game-price">€<?php echo number_format($juego['precio'], 2); ?></p>
                                <a href="detalle_segunda_mano.php?id=<?php echo $juego['id']; ?>" class="view-details">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </style>
<!-- Botón -->
<!-- Botón scroll arriba -->
<button id="scrollToTopBtn" aria-label="Volver arriba">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
       stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
    <polyline points="18 15 12 9 6 15"></polyline>
  </svg>
</button>

<!-- Estilos CSS -->
<style>
 #scrollToTopBtn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 50px;
  height: 50px;
  background-color: #0d6efd; /* Azul Bootstrap */
  color: white;
  border: none;
  border-radius: 50%;
  display: none;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: background-color 0.3s, transform 0.3s;
  z-index: 1000;
}

#scrollToTopBtn:hover {
  background-color: #0b5ed7;
  transform: scale(1.1);
}

#scrollToTopBtn svg {
  width: 24px;
  height: 24px;
}
</style>

<!-- Script JS -->
<script>
 const scrollBtn = document.getElementById('scrollToTopBtn');

window.addEventListener('scroll', () => {
  scrollBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
});

scrollBtn.addEventListener('click', () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
});
</script>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
                        