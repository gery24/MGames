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