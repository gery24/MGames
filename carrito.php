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
                <!-- Diseño mejorado para carrito vacío -->
                <div class="empty-cart-container">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Tu carrito está vacío</h2>
                    <p>Parece que aún no has añadido ningún juego a tu carrito. Explora nuestra tienda para encontrar los mejores títulos.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-gamepad"></i> Explorar juegos
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-content">
                    <div class="products-list">
                        <?php foreach ($productos_en_carrito as $cart_item_key => $producto_data): ?>
                            <?php 
                            // Extraer el ID numérico del producto de la clave del carrito (ej: "productos_7" -> 7)
                            $key_parts = explode('_', $cart_item_key);
                            $producto_id = (count($key_parts) > 1) ? intval($key_parts[1]) : $cart_item_key; // Usar la clave completa si no tiene el formato esperado

                            // Verificar si el elemento es un array y contiene las claves necesarias
                            if (is_array($producto_data) && isset($producto_data['nombre'], $producto_data['imagen'], $producto_data['precio'])): 
                            ?>
                            <div class="product-card">
                                <!-- Envuelve la imagen en un contenedor -->
                                <div class="product-image-container">
                                    <img src="<?php echo !empty($producto_data['imagen']) ? htmlspecialchars($producto_data['imagen']) : 'images/default.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($producto_data['nombre'] ?? 'Producto desconocido'); ?>">
                                    <?php if (isset($producto_data['descuento']) && $producto_data['descuento'] > 0): ?>
                                        <div class="discount-badge">-<?php echo htmlspecialchars($producto_data['descuento']); ?>%</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-card-content">
                                    <!-- Contenedor para nombre y precio -->
                                    <div class="product-header">
                                        <h3><?php echo htmlspecialchars($producto_data['nombre']); ?></h3>
                                        <p class="price">€<?php echo number_format($producto_data['precio'], 2); ?></p>
                                    </div>
                                    <div class="quantity">
                                        <label for="quantity-<?php echo $cart_item_key; ?>">Cantidad:</label>
                                        <select id="quantity-<?php echo $cart_item_key; ?>" 
                                                name="quantity" 
                                                onchange="actualizarCantidad('<?php echo $cart_item_key; ?>', this.value)">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>" 
                                                    <?php echo (isset($producto_data['cantidad']) && $producto_data['cantidad'] == $i) ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <form method="POST" action="eliminar_del_carrito.php">
                                        <input type="hidden" name="id" value="<?php echo $cart_item_key; ?>">
                                        <input type="hidden" name="tipo_producto" value="<?php echo $producto_data['tipo_producto']; ?>">
                                        <button type="submit" class="btn">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-summary">
                        <h2>Resumen del Pedido</h2>
                        <?php 
                        $subtotal = 0;
                        $total_descuento_aplicado = 0;

                        // Obtener información completa de los productos en el carrito, incluyendo descuento
                        $productos_ids_en_carrito = array_keys($_SESSION['carrito'] ?? []); // Usar el carrito actual para obtener IDs
                        $productos_db = [];
                        if (!empty($productos_ids_en_carrito)) {
                            $placeholders = implode(',', array_fill(0, count($productos_ids_en_carrito), '?'));
                            $query_productos_db = "SELECT id, nombre, precio, descuento FROM productos WHERE id IN ({$placeholders})"; // Añadimos nombre a la consulta
                            $stmt_productos_db = $pdo->prepare($query_productos_db);
                            $stmt_productos_db->execute($productos_ids_en_carrito);
                            // Mapear resultados por ID para fácil acceso
                            foreach($stmt_productos_db->fetchAll(PDO::FETCH_ASSOC) as $prod_db) {
                                $productos_db[$prod_db['id']] = $prod_db;
                            }
                        }

                        // Modificar la estructura HTML del resumen para incluir atributos data-producto-id
                        
                        // --- Mostrar resumen por producto ---
                        foreach ($productos_en_carrito as $cart_item_key => $producto_data) {
                            
                            // Extraer el ID numérico del producto de la clave del carrito
                            $key_parts = explode('_', $cart_item_key);
                            $producto_id = (count($key_parts) > 1) ? intval($key_parts[1]) : $cart_item_key; // Usar la clave completa si no tiene el formato esperado

                            // Aplicar la misma validación que para mostrar las tarjetas
                            if (is_array($producto_data) && isset($producto_data['nombre'], $producto_data['imagen'], $producto_data['precio'])): 

                                $cantidad = isset($producto_data['cantidad']) ? $producto_data['cantidad'] : 1;
                                
                                // Usar información de la base de datos si está disponible
                                if (isset($productos_db[$producto_id])) {
                                    $prod_info_db = $productos_db[$producto_id];
                                    $nombre_producto = htmlspecialchars($prod_info_db['nombre']);
                                    $precio_unitario = $prod_info_db['precio'];
                                    $descuento_porcentaje = $prod_info_db['descuento'] ?? 0;
                                    $precio_con_descuento_unitario = $precio_unitario * (1 - ($descuento_porcentaje / 100));
                                    $precio_linea_total = $precio_con_descuento_unitario * $cantidad; // Precio con descuento por cantidad

                                    // Acumular subtotal y descuento total aplicado
                                    $subtotal += $precio_linea_total;
                                    $total_descuento_aplicado += ($precio_unitario * $cantidad) - $precio_linea_total; // Descuento total aplicado en esta línea

                                    ?>
                                    <div class="summary-item" data-producto-id="<?php echo $cart_item_key; ?>">
                                        <p><strong><?php echo $nombre_producto; ?></strong> (<span class="producto-cantidad">x<?php echo $cantidad; ?></span>)</p>
                                        <p>Precio unitario: €<?php echo number_format($precio_unitario, 2); ?></p>
                                        <p>Descuento: <?php echo $descuento_porcentaje > 0 ? $descuento_porcentaje . '%' : '0%'; ?></p>
                                        <?php if ($descuento_porcentaje > 0): ?>
                                            <p>Precio con descuento: €<?php echo number_format($precio_con_descuento_unitario, 2); ?></p>
                                        <?php endif; ?>
                                        <p>Subtotal línea: <span class="subtotal-linea">€<?php echo number_format($precio_linea_total, 2); ?></span></p>
                                    </div>
                                    <hr> <!-- Separador entre productos -->
                                    <?php

                                } else {
                                    // Si por alguna razón no se encuentra en DB, mostrar información básica de la sesión
                                    // Sin embargo, con la validación inicial, esto solo ocurriría si los datos de la sesión son inconsistentes
                                    $nombre_producto = htmlspecialchars($producto_data['nombre']);
                                    $precio_unitario = $producto_data['precio'];
                                    $cantidad = $producto_data['cantidad'] ?? 1;
                                    $precio_linea_total = $precio_unitario * $cantidad;
                                    $subtotal += $precio_linea_total;
                                    // Aquí no podemos calcular el descuento individual si no lo tenemos de la DB o sesión
                                    ?>
                                    <div class="summary-item" data-producto-id="<?php echo $cart_item_key; ?>">
                                        <p><strong><?php echo $nombre_producto; ?></strong> (<span class="producto-cantidad">x<?php echo $cantidad; ?></span>)</p>
                                        <p>Precio unitario: €<?php echo number_format($precio_unitario, 2); ?></p>
                                        <p>Descuento: N/A</p>
                                        <p>Subtotal línea: <span class="subtotal-linea">€<?php echo number_format($precio_linea_total, 2); ?></span></p>
                                    </div>
                                    <hr> <!-- Separador entre productos -->
                                    <?php
                                }
                            
                            endif; // Cierre de la validación
                        } // Cierre del foreach
                        
                        // Asegurarnos de que el descuento total se muestre como un valor absoluto positivo
                        $total_descuento_aplicado = abs($total_descuento_aplicado);

                        $total = $subtotal; // El subtotal ya incluye los precios con descuento
                        ?>
                        
                        <!-- --- Mostrar Totales Finales --- -->
                        <div class="final-summary">
                            <?php
                            echo "<p><strong>Total a pagar: €" . number_format($total, 2) . "</strong></p>";
                            ?>
                        </div>

                        <button class="btn" onclick="window.location.href='pago.php'">
                            <i class="fas fa-credit-card"></i> Proceder al pago
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <section class="recommended-products">
            <h2>Juegos Recomendados</h2>
            <!-- Nuevo slider horizontal para productos recomendados -->
            <div class="products-slider">
                <?php foreach ($productos_recomendados as $recomendado): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="<?php echo !empty($recomendado['imagen']) ? htmlspecialchars($recomendado['imagen']) : 'images/default.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($recomendado['nombre']); ?>">
                            <?php if (isset($recomendado['descuento']) && $recomendado['descuento'] > 0): ?>
                                <div class="discount-badge">-<?php echo htmlspecialchars($recomendado['descuento']); ?>%</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($recomendado['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($recomendado['precio'], 2); ?></p>
                            <form method="POST" action="agregar_al_carrito.php">
                                <input type="hidden" name="id" value="<?php echo $recomendado['id']; ?>">
                                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($recomendado['nombre']); ?>">
                                <input type="hidden" name="precio" value="<?php echo $recomendado['precio']; ?>">
                                <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($recomendado['imagen']); ?>">
                                <input type="hidden" name="tipo_producto" value="producto">
                                <input type="hidden" name="cantidad" value="1">
                                <?php if (isset($recomendado['descuento'])): ?>
                                    <input type="hidden" name="descuento" value="<?php echo $recomendado['descuento']; ?>">
                                <?php endif; ?>
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
            
            // Scroll horizontal con rueda del mouse para el slider de productos
            const slider = document.querySelector('.products-slider');
            if (slider) {
                slider.addEventListener('wheel', function(e) {
                    if (e.deltaY !== 0) {
                        e.preventDefault();
                        slider.scrollLeft += e.deltaY;
                    }
                }, { passive: false });
            }
        });
        
        // Función mejorada para actualizar la cantidad y reflejar los cambios en el resumen
        function actualizarCantidad(cartItemKey, cantidad) {
            console.log('Actualizando cantidad para key:', cartItemKey, 'con cantidad:', cantidad);
            
            // Mostrar indicador de carga
            const loadingIndicator = document.createElement('div');
            loadingIndicator.className = 'update-message';
            loadingIndicator.textContent = 'Actualizando...';
            document.querySelector('.cart-container').prepend(loadingIndicator);
            
            fetch('actualizar_cantidad.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + cartItemKey + '&cantidad=' + cantidad
            })
            .then(response => {
                console.log('Respuesta recibida');
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                
                // Eliminar indicador de carga
                loadingIndicator.remove();
                
                if (data.success) {
                    // Actualizar el total a pagar
                    const totalElement = document.querySelector('.final-summary p strong');
                    if (totalElement) {
                        totalElement.textContent = 'Total a pagar: €' + data.total;
                        totalElement.classList.add('price-updated');
                        setTimeout(() => totalElement.classList.remove('price-updated'), 1000);
                    } else {
                        console.error('Elemento total no encontrado');
                    }
                    
                    // Actualizar el subtotal de la línea del producto modificado
                    const productoSubtotalElement = document.querySelector(`.summary-item[data-producto-id="${cartItemKey}"] .subtotal-linea`);
                    if (productoSubtotalElement) {
                        const subtotalProducto = data.subtotales[cartItemKey].subtotal;
                        productoSubtotalElement.textContent = '€' + subtotalProducto;
                        productoSubtotalElement.classList.add('price-updated');
                        setTimeout(() => productoSubtotalElement.classList.remove('price-updated'), 1000);
                    } else {
                        console.error('Elemento subtotal no encontrado para el item key:', cartItemKey);
                    }
                    
                    // Actualizar la cantidad mostrada en el resumen
                    const cantidadElement = document.querySelector(`.summary-item[data-producto-id="${cartItemKey}"] .producto-cantidad`);
                    if (cantidadElement) {
                        const nuevaCantidad = data.subtotales[cartItemKey].cantidad;
                        cantidadElement.textContent = 'x' + nuevaCantidad;
                        cantidadElement.classList.add('quantity-updated');
                        setTimeout(() => cantidadElement.classList.remove('quantity-updated'), 1000);
                    } else {
                        console.error('Elemento cantidad no encontrado para el item key:', cartItemKey);
                    }
                    
                    // Mostrar mensaje de confirmación
                    const mensajeElement = document.createElement('div');
                    mensajeElement.className = 'update-message';
                    mensajeElement.innerHTML = '<i class="fas fa-check-circle"></i> Carrito actualizado';
                    document.querySelector('.cart-container').prepend(mensajeElement);
                    
                    // Eliminar el mensaje después de 2 segundos
                    setTimeout(() => {
                        mensajeElement.remove();
                    }, 2000);
                } else {
                    console.error('Error en la respuesta:', data.message);
                    alert('Error al actualizar la cantidad: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                // Eliminar indicador de carga
                loadingIndicator.remove();
                
                console.error('Error en la solicitud:', error);
                alert('Error al procesar la solicitud. Por favor, inténtalo de nuevo.');
            });
        }
    </script>
    
    <?php require_once 'includes/footer.php'; ?>

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

    /* Añadir animación para la cantidad actualizada */
    .quantity-updated {
      animation: quantityPulse 1s ease;
      font-weight: bold;
      color: #10b981;
    }

    @keyframes quantityPulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.2);
      }
      100% {
        transform: scale(1);
      }
    }

    /* Versión para administradores */
    body.admin .quantity-updated {
      color: #ff0000;
    }

    /* Mejorar el mensaje de actualización */
    .update-message {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .update-message i {
      font-size: 1.1rem;
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
</body>
</html>
