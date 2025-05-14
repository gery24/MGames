<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario es admin para añadir la clase 'admin' al body
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
$bodyClass = $isAdmin ? 'admin' : '';

// Verificar si hay productos en el carrito
$productos_en_carrito = $_SESSION['carrito'] ?? [];

// Obtener productos recomendados
try {
    // Consulta ajustada a la estructura de la tabla productos (asegurando que incluya descuento)
    $stmt = $pdo->query("SELECT id, nombre, descripcion, precio, imagen, estado, segunda_mano, descuento FROM productos ORDER BY RAND() LIMIT 4");
    $productos_recomendados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $productos_recomendados = [];
    $_SESSION['error'] = "Error al cargar productos recomendados: " . $e->getMessage();
}

// Obtener categorías para el filtro de búsqueda
try {
    $stmt = $pdo->query("SELECT id, nombre FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
    $_SESSION['error'] = "Error al cargar las categorías: " . $e->getMessage();
}

$titulo = "Carrito - MGames";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/carrito.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body class="<?php echo $bodyClass; ?>">
    <?php require_once 'includes/header.php'; ?>

    <div class="content">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> 
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <?php 
                echo $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php endif; ?>

        <header>
            <h1><i class="fas fa-shopping-cart"></i> Tu Carrito de Compras</h1>
        </header>

        <div class="cart-container">
            <?php if (empty($productos_en_carrito)): ?>
                <p>No hay productos en tu carrito. <a href="index.php">Continuar comprando</a></p>
            <?php else: ?>
                <div class="cart-content">
                    <div class="products-list">
                        <?php foreach ($productos_en_carrito as $producto): ?>
                            <div class="product-card">
                                <img src="<?php echo !empty($producto['imagen']) ? htmlspecialchars($producto['imagen']) : 'images/default.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
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
                                        <button type="submit" class="btn">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-summary">
                        <h2>Resumen del Pedido</h2>
                        <?php 
                        $subtotal = 0;
                        $total_descuento_aplicado = 0;

                        // Obtener información completa de los productos en el carrito, incluyendo descuento
                        $productos_ids_en_carrito = array_keys($productos_en_carrito);
                        $productos_db = [];
                        if (!empty($productos_ids_en_carrito)) {
                            $placeholders = implode(',', array_fill(0, count($productos_ids_en_carrito), '?'));
                            $query_productos_db = "SELECT id, precio, descuento FROM productos WHERE id IN ({$placeholders})";
                            $stmt_productos_db = $pdo->prepare($query_productos_db);
                            $stmt_productos_db->execute($productos_ids_en_carrito);
                            // Mapear resultados por ID para fácil acceso
                            foreach($stmt_productos_db->fetchAll(PDO::FETCH_ASSOC) as $prod_db) {
                                $productos_db[$prod_db['id']] = $prod_db;
                            }
                        }

                        foreach ($productos_en_carrito as $producto_id => $producto_en_sesion) {
                            $cantidad = isset($producto_en_sesion['cantidad']) ? $producto_en_sesion['cantidad'] : 1;
                            
                            // Usar precio y descuento de la base de datos si están disponibles
                            if (isset($productos_db[$producto_id])) {
                                $prod_info_db = $productos_db[$producto_id];
                                $precio_unitario = $prod_info_db['precio'];
                                $descuento_porcentaje = $prod_info_db['descuento'] ?? 0;
                                $precio_con_descuento_unitario = $precio_unitario * (1 - ($descuento_porcentaje / 100));
                                
                                $subtotal += $precio_con_descuento_unitario * $cantidad;
                                // Calcular la parte del descuento aplicado en esta línea
                                $total_descuento_aplicado += ($precio_unitario - $precio_con_descuento_unitario) * $cantidad;

                            } else {
                                // Si por alguna razón no se encuentra en DB, usar el precio de la sesión (menos ideal)
                                $subtotal += $producto_en_sesion['precio'] * $cantidad;
                                // Aquí no podemos calcular el descuento si no lo tenemos de la DB o sesión
                            }
                        }
                        
                        // Asegurarnos de que el descuento se muestre como un valor absoluto positivo
                        $total_descuento_aplicado = abs($total_descuento_aplicado);

                        $total = $subtotal; // El subtotal ya incluye los precios con descuento
                        ?>
                        <p><span>Subtotal (con descuentos):</span> <span>€<?php echo number_format($subtotal, 2); ?></span></p>
                        <?php if ($total_descuento_aplicado > 0): ?>
                            <p><span>Descuento total aplicado:</span> <span style="color: #10b981;">-€<?php echo number_format($total_descuento_aplicado, 2); ?></span></p>
                        <?php endif; ?>
                        <p><span>Total a pagar:</span> <span>€<?php echo number_format($total, 2); ?></span></p>
                        <button class="btn" onclick="window.location.href='pago.php'">
                            <i class="fas fa-credit-card"></i> Proceder al pago
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <section class="recommended-products">
            <h2>Juegos Recomendados</h2>
            <div class="products-list">
                <?php foreach ($productos_recomendados as $recomendado): ?>
                    <div class="product-card">
                        <img src="<?php echo !empty($recomendado['imagen']) ? htmlspecialchars($recomendado['imagen']) : 'images/default.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($recomendado['nombre']); ?>">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($recomendado['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($recomendado['precio'], 2); ?></p>
                            <form method="POST" action="agregar_al_carrito.php">
                                <input type="hidden" name="id" value="<?php echo $recomendado['id']; ?>">
                                <button type="submit" class="btn">
                                    <i class="fas fa-cart-plus"></i> Añadir al carrito
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle para el formulario de búsqueda
            const searchToggle = document.getElementById('search-toggle');
            const searchForm = document.getElementById('search-form');

            if (searchToggle && searchForm) {
                searchToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (searchForm.style.display === 'none') {
                        searchForm.style.display = 'flex';
                        searchForm.querySelector('input').focus();
                    } else {
                        searchForm.style.display = 'none';
                    }
                });
                
                // Cerrar el formulario cuando se hace clic fuera de él
                document.addEventListener('click', function(e) {
                    if (!searchToggle.contains(e.target) && !searchForm.contains(e.target)) {
                        searchForm.style.display = 'none';
                    }
                });
            }
            
            // Añadir animación a las tarjetas al cargar la página
            const cards = document.querySelectorAll('.product-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
            
            // Toggle para menú móvil
            const menuToggle = document.getElementById('menuToggle');
            const navLinks = document.querySelector('.nav-links');
            const headerActions = document.querySelector('.header-actions');
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    headerActions.classList.toggle('active');
                });
            }
        });
        
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
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }
    </script>
    <style>
/* Estilos generales del header */
.site-header {
    background-color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: #4f46e5;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.nav-links {
    display: flex;
    gap: 1.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-links li a {
    color: #1f2937;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
    padding: 0.5rem 0;
    position: relative;
}

.nav-links li a:hover {
    color: #4f46e5;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    color: #1f2937;
    font-size: 1.25rem;
    transition: color 0.3s;
    position: relative;
    text-decoration: none;
}

.header-icon:hover {
    color: #4f46e5;
}

/* Estilos para el botón de búsqueda (lupa) */
.search-container {
    position: relative;
}

.search-button {
    width: 32px;
    height: 32px;
    background-color: #4f46e5;
    color: white;
    border: none;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s;
}

.search-button:hover {
    background-color: #4338ca;
}

.search-button i {
    font-size: 1rem;
}

.search-form {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 100;
    display: flex;
    gap: 0.5rem;
    min-width: 300px;
}

.search-form input,
.search-form select {
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
}

.search-form input {
    flex: 1;
}

.filter-select {
    width: auto;
}

/* Estilos para el badge */
.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #4f46e5;
    color: white;
    font-size: 0.75rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.balance-indicator {
    position: absolute;
    top: -8px;
    right: -20px;
    background-color: #10b981;
    color: white;
    font-size: 0.75rem;
    padding: 0.1rem 0.4rem;
    border-radius: 0.25rem;
    white-space: nowrap;
}

/* Estilos para el perfil de usuario */
.user-profile {
    position: relative;
}

.profile-dropdown {
    position: relative;
}

.profile-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
}

.profile-button:hover {
    background-color: #f9fafb;
}

.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #4f46e5;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.username {
    font-weight: 500;
    color: #1f2937;
    max-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dropdown-content {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 0.5rem 0;
    z-index: 100;
    display: none;
}

.profile-dropdown:hover .dropdown-content {
    display: block;
}

.dropdown-content a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: #1f2937;
    text-decoration: none;
    transition: background-color 0.3s;
}

.dropdown-content a:hover {
    background-color: #f9fafb;
    color: #4f46e5;
}

.dropdown-content a i {
    width: 20px;
    text-align: center;
}

/* Estilos para los botones de autenticación */
.auth-buttons {
    display: flex;
    gap: 0.5rem;
}

/* Estilos para los botones */
.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border: none;
    background-color: #4f46e5;
    color: white;
}

/* Corregir el problema del hover en los botones */
.btn:hover {
    background-color: #4338ca;
    color: white;
    opacity: 1;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.btn-primary {
    background-color: #4f46e5;
    color: white;
}

.btn-primary:hover {
    background-color: #4338ca;
    color: white;
    opacity: 1;
}

.btn-outline {
    background-color: transparent;
    color: #4f46e5;
    border: 2px solid #4f46e5;
}

.btn-outline:hover {
    background-color: rgba(79, 70, 229, 0.1);
    color: #4f46e5;
    opacity: 1;
}

/* Estilos para el menú móvil */
.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #1f2937;
    font-size: 1.5rem;
    cursor: pointer;
}

/* Admin-specific styles for header */
body.admin .site-header {
    border-bottom: 3px solid #ff0000;
}

body.admin .logo span {
    color: #ff0000;
}

body.admin .search-button {
    background-color: #ff0000;
}

body.admin .search-button:hover {
    background-color: #cc0000;
}

body.admin .nav-links li a:hover {
    color: #ff0000;
}

body.admin .header-icon:hover {
    color: #ff0000;
}

body.admin .badge {
    background-color: #ff0000;
}

body.admin .avatar-circle {
    background-color: #ff0000;
}

body.admin .admin-username {
    color: white !important;
    font-weight: bold;
}

body.admin .profile-button:hover {
    background-color: rgba(255, 0, 0, 0.1);
}

body.admin .dropdown-content {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 0.5rem 0;
    z-index: 100;
    display: none;
    border: 2px solid #ff0000;
}

body.admin .dropdown-content a:hover {
    color: #ff0000;
}

body.admin .admin-link {
    background-color: #ff0000;
    color: white !important;
}

body.admin .admin-link:hover {
    background-color: #cc0000;
}

/* Estilos específicos para los botones del carrito */
.cart-summary .btn,
.product-card-content .btn {
    background-color: #4f46e5;
    color: white;
    opacity: 1;
}

.cart-summary .btn:hover,
.product-card-content .btn:hover {
    background-color: #4338ca;
    color: white;
    opacity: 1;
}

/* Para usuarios admin */
body.admin .cart-summary .btn,
body.admin .product-card-content .btn {
    background-color: #ff0000;
    color: white;
    opacity: 1;
}

body.admin .cart-summary .btn:hover,
body.admin .product-card-content .btn:hover {
    background-color: #cc0000;
    color: white;
    opacity: 1;
}

/* Responsive styles */
@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: block;
    }

    .nav-links {
        display: none;
    }

    .nav-links.active {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        padding: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 50;
    }

    .header-actions {
        gap: 0.5rem;
    }

    .search-container,
    .auth-buttons {
        display: none;
    }

    .header-actions.active .search-container,
    .header-actions.active .auth-buttons {
        display: block;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        padding: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 50;
    }
}
</style>
</body>
</html>
