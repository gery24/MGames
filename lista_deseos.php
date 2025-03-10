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
            /* Reducir los márgenes laterales para permitir tarjetas más anchas */
            margin: 0 5%;
            margin-top: 0;
        }

        .wishlist-content {
            display: flex;
            gap: 20px;
            width: 100%; /* Asegurar que ocupe todo el ancho disponible */
        }

        .products-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex-grow: 1;
            width: 100%; /* Asegurar que ocupe todo el ancho disponible */
        }

        .product-card {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            background: var(--card-background);
            padding: 15px 25px; /* Aumentar el padding horizontal */
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            height: 200px;
            width: 100%; /* Asegurar que ocupe todo el ancho disponible */
            max-width: 100%; /* Evitar que se desborde */
        }

        .product-card img {
            width: 150px; /* Aumentar el tamaño de la imagen */
            height: 150px; /* Aumentar el tamaño de la imagen */
            object-fit: cover;
            margin-left: 25px; /* Aumentar el margen */
            border-radius: var(--border-radius);
        }

        .product-card-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            padding-right: 20px; /* Añadir espacio a la derecha */
        }

        .product-card-content h3 {
            font-size: 1.4rem; /* Aumentar tamaño de fuente */
            margin: 0;
            color: var(--text-color);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .price {
            font-size: 1.4rem; /* Aumentar tamaño de fuente */
            color: var(--primary-color);
            font-weight: bold;
            margin: 10px 0; /* Aumentar margen */
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px; /* Aumentar padding */
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s;
            min-width: 120px; /* Aumentar ancho mínimo */
            font-size: 1rem; /* Aumentar tamaño de fuente */
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
                margin: 0 2%; /* Reducir aún más los márgenes en móviles */
                padding: 10px;
            }
            
            .hero {
                max-height: 50px; /* Altura máxima más pequeña en móviles */
            }
            
            .hero h1 {
                font-size: 1.2rem;
            }
            
            .product-card {
                padding: 10px 15px; /* Reducir padding en móviles */
                height: auto; /* Permitir altura automática */
                min-height: 150px; /* Establecer altura mínima */
            }
            
            .product-card img {
                width: 100px; /* Reducir tamaño de imagen en móviles */
                height: 100px;
                margin-left: 15px;
            }
            
            .product-card-content h3 {
                font-size: 1.2rem;
            }
            
            .price {
                font-size: 1.2rem;
                margin: 5px 0;
            }
            
            .btn {
                padding: 8px 15px;
                min-width: 100px;
                font-size: 0.9rem;
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