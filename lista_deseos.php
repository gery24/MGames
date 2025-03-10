<?php
session_start();

// Verifica si hay productos en la lista de deseos
$lista_deseos = $_SESSION['lista_deseos'] ?? [];
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Deseos</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .content {
            padding: 20px;
        }

        .hero {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px; /* Reducido el padding vertical */
            text-align: center;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            /* Eliminar altura fija o mínima para que se ajuste al contenido */
            height: auto;
            max-height: 60px; /* Limitar la altura máxima */
            overflow: hidden; /* Evitar desbordamiento */
        }

        .hero h1 {
            margin: 0;
            font-size: 1.5rem;
            line-height: 1.2;
        }

        .wishlist-container {
            display: flex;
            flex-direction: column;
            padding: 20px;
            margin: 0 15%;
            /* Reducir el margen superior para acercar el contenido al hero */
            margin-top: 0;
        }

        .wishlist-content {
            display: flex;
            gap: 20px;
        }

        .products-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex-grow: 1;
        }

        .product-card {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            background: var(--card-background);
            padding: 15px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            height: 200px;
        }

        .product-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-left: 15px;
            border-radius: var(--border-radius);
        }

        .product-card-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }

        .product-card-content h3 {
            font-size: 1.2rem;
            margin: 0;
            color: var(--text-color);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .price {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: bold;
            margin: 5px 0;
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s;
            min-width: 100px;
            font-size: 0.9rem;
        }

        .btn:hover {
            background-color: var(--secondary-color);
        }

        .hero .btn {
            padding: 0; /* Eliminar padding para reducir altura */
            background-color: transparent;
            min-width: auto;
            font-size: 1.5rem;
        }

        .hero .btn:hover {
            background-color: transparent;
        }

        @media (max-width: 768px) {
            .wishlist-container {
                margin: 0 5%;
            }
            
            .hero {
                max-height: 50px; /* Altura máxima más pequeña en móviles */
            }
            
            .hero h1 {
                font-size: 1.2rem;
            }
        }

        a {
            color: black; /* Cambia el color del enlace a negro */
            text-decoration: none; /* Quita el subrayado */
        }
    </style>
</head>
<body>
    <div class="content">
        <header class="hero">
            <h1 class="btn">Lista de Deseos</h1>
        </header>

        <div class="wishlist-container">
            <?php if (empty($lista_deseos)): ?>
                <p>No hay productos en tu lista de deseos.</p>
            <?php else: ?>
                <div class="wishlist-content">
                    <div class="products-list">
                        <?php foreach ($lista_deseos as $producto): ?>
                            <div class="product-card">
                                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <div class="product-card-content">
                                    <h3><a href="producto.php?id=<?php echo $producto['id']; ?>"><?php echo htmlspecialchars($producto['nombre']); ?></a></h3>
                                    <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                                    <form method="POST" action="eliminar_deseos.php">
                                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>