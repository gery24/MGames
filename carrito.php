<?php
session_start();
require_once 'config/database.php';

// Verificar si hay productos en el carrito
$productos_en_carrito = $_SESSION['carrito'] ?? [];

// Suponiendo que tienes la información del usuario en la sesión
$usuario = $_SESSION['usuario'] ?? null;

// Obtener productos recomendados (puedes ajustar la consulta según tu base de datos)
$stmt = $pdo->query("SELECT * FROM productos ORDER BY RAND() LIMIT 5"); // Cambia esto según tu lógica
$productos_recomendados = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - MGames</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/carrito.css">
</head>
<body>
    <div class="content">
        <header class="hero">
            <h1>Tu Carrito</h1>
        </header>

        <div class="cart-container">
            <?php if (empty($productos_en_carrito)): ?>
                <p>No hay productos en tu carrito.</p>
            <?php else: ?>
                <div class="cart-content">
                    <div class="products-list">
                        <?php foreach ($productos_en_carrito as $producto): ?>
                            <div class="product-card">
                                <div class="product-card-content">
                                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                    <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                                    <div class="quantity">
                                        <label for="quantity-<?php echo $producto['id']; ?>">Cantidad:</label>
                                        <select id="quantity-<?php echo $producto['id']; ?>" name="quantity">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <form method="POST" action="eliminar_del_carrito.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn">Eliminar</button>
                                    </form>
                                </div>
                                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-summary">
                        <h2>Resumen</h2>
                        <p>Precio oficial: €<?php echo number_format(array_sum(array_column($productos_en_carrito, 'precio')) + 13.93, 2); ?></p>
                        <p>Descuento: -€13.93</p>
                        <p>Subtotal: €<?php echo number_format(array_sum(array_column($productos_en_carrito, 'precio')), 2); ?></p>
                        <button class="btn" onclick="checkout()">Proceder con el pago</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <section class="recommended-products">
            <h2>Juegos Recomendados</h2>
            <div class="products-list">
                <?php foreach ($productos_recomendados as $recomendado): ?>
                    <div class="product-card">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($recomendado['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($recomendado['precio'], 2); ?></p>
                            <form method="POST" action="agregar_al_carrito.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $recomendado['id']; ?>">
                                <button type="submit" class="btn">Mover al carro</button>
                            </form>
                        </div>
                        <img src="<?php echo htmlspecialchars($recomendado['imagen']); ?>" alt="<?php echo htmlspecialchars($recomendado['nombre']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <script>
        function checkout() {
            // Lógica para proceder al pago
            alert('Procediendo al pago...');
        }
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>