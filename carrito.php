<?php
session_start();
require_once 'config/database.php';

// Verificar si hay productos en el carrito
$productos_en_carrito = $_SESSION['carrito'] ?? [];

// Obtener productos recomendados
try {
    // Consulta ajustada a la estructura de la tabla productos
    $stmt = $pdo->query("SELECT id, nombre, descripcion, precio, imagen, estado, segunda_mano FROM productos ORDER BY RAND() LIMIT 5");
    $productos_recomendados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $productos_recomendados = [];
    $_SESSION['error'] = "Error al cargar productos recomendados: " . $e->getMessage();
}
require_once 'includes/header.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - MGames</title>
    <link rel="stylesheet" href="css/carrito.css">
</head>
<body>
    <div class="content">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php endif; ?>

        <header >
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
                                        <select id="quantity-<?php echo $producto['id']; ?>" 
                                                name="quantity" 
                                                onchange="actualizarCantidad(<?php echo $producto['id']; ?>, this.value)">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>" 
                                                    <?php echo (isset($producto['cantidad']) && $producto['cantidad'] == $i) ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <form method="POST" action="eliminar_del_carrito.php">
                                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="btn">Eliminar</button>
                                    </form>
                                </div>
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-summary">
                        <h2>Resumen</h2>
                        <?php 
                        $subtotal = 0;
                        foreach ($productos_en_carrito as $producto) {
                            $cantidad = isset($producto['cantidad']) ? $producto['cantidad'] : 1;
                            $subtotal += $producto['precio'] * $cantidad;
                        }
                        $descuento = 13.93;
                        $total = $subtotal + $descuento;
                        ?>
                        <p>Precio oficial: €<?php echo number_format($total, 2); ?></p>
                        <p>Descuento: -€<?php echo number_format($descuento, 2); ?></p>
                        <p>Subtotal: €<?php echo number_format($subtotal, 2); ?></p>
                        <button class="btn" onclick="window.location.href='pago.php'">Proceder con el pago</button>
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
                            <form method="POST" action="agregar_al_carrito.php">
                                <input type="hidden" name="id" value="<?php echo $recomendado['id']; ?>">
                                <button type="submit" class="btn">Mover al carro</button>
                            </form>
                        </div>
                        <?php if (!empty($recomendado['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($recomendado['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($recomendado['nombre']); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <script>
        function checkout() {
            alert('Procediendo al pago...');
            // Aquí puedes redirigir a la página de checkout
            // window.location.href = 'checkout.php';
        }
        
        function actualizarCantidad(productoId, cantidad) {
            fetch('actualizar_cantidad.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + productoId + '&cantidad=' + cantidad
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error al actualizar la cantidad');
                }
            });
        }
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>

