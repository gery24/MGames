<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario es admin para añadir la clase 'admin' al body
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
$bodyClass = $isAdmin ? 'admin' : '';

// Redirigir si el usuario no está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

try {
    // Obtener los productos de la lista de deseos del usuario
    $stmt = $pdo->prepare("
        SELECT l.id, l.producto_id, l.tipo 
        FROM lista_deseos l
        WHERE l.usuario_id = ?
        ORDER BY l.fecha_agregado DESC
    ");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $items_lista_deseos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ahora $items_lista_deseos contiene los IDs y tipos de los ítems en la lista de deseos.
    // Necesitamos obtener los detalles de cada ítem (nombre, imagen, precio, etc.) 
    // de la tabla productos o segunda_mano basándonos en el campo 'tipo'.
    $productos = []; // Inicializar un array para almacenar los detalles completos

    foreach ($items_lista_deseos as $item) {
        if ($item['tipo'] === 'producto') {
            // Obtener detalles del producto normal
            $stmt_producto = $pdo->prepare("SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
            $stmt_producto->execute([$item['producto_id']]);
            $detalle_item = $stmt_producto->fetch(PDO::FETCH_ASSOC);
            if ($detalle_item) {
                $detalle_item['tipo'] = 'producto';
                $detalle_item['lista_deseos_id'] = $item['id'];
                $productos[] = $detalle_item;
            }
        } elseif ($item['tipo'] === 'segunda_mano') {
            // Obtener detalles del juego de segunda mano
            $stmt_segunda_mano = $pdo->prepare("SELECT sm.*, c.nombre as categoria_nombre FROM segunda_mano sm LEFT JOIN categorias c ON sm.categoria_id = c.id WHERE sm.id = ?");
            $stmt_segunda_mano->execute([$item['producto_id']]);
            $detalle_item = $stmt_segunda_mano->fetch(PDO::FETCH_ASSOC);
            if ($detalle_item) {
                $detalle_item['tipo'] = 'segunda_mano';
                $detalle_item['lista_deseos_id'] = $item['id'];
                $productos[] = $detalle_item;
            }
        }
    }

} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

// Título de la página
$titulo = "Lista de Deseos - MGames";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="<?php echo $bodyClass; ?>">
    <?php require_once 'includes/header.php'; ?>
    
    <div class="content">
        <div class="wishlist-container">
            <h1>Mi Lista de Deseos</h1>
            
            <?php if (empty($productos)): ?>
                <div class="empty-wishlist">
                    <div class="empty-icon">
                        <i class="fas fa-heart-broken"></i>
                    </div>
                    <p>Tu lista de deseos está vacía</p>
                    <a href="index.php" class="btn pulse-animation">Explorar Juegos</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach($productos as $producto): 
                        // Determinar la URL de detalle basada en el tipo
                        $detalle_url = ($producto['tipo'] === 'producto') ? 
                                       'producto.php?id=' . $producto['id'] : 
                                       'detalle_segunda_mano.php?id=' . $producto['id'];
                    ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                    alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <div class="product-overlay">
                                    <a href="<?php echo $detalle_url; ?>" class="btn-overlay">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <span class="category-badge"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></span>
                            </div>
                            <div class="product-card-content">
                                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <div class="price-container">
                                    <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                                    <?php if ($producto['tipo'] === 'producto' && isset($producto['precio_anterior']) && $producto['precio_anterior'] > $producto['precio']): ?>
                                        <p class="old-price">€<?php echo number_format($producto['precio_anterior'], 2); ?></p>
                                        <?php 
                                            $discount = round(100 - ($producto['precio'] * 100 / $producto['precio_anterior']));
                                            echo '<span class="discount-badge">-' . $discount . '%</span>';
                                        ?>
                                    <?php endif; ?>
                                     <?php if ($producto['tipo'] === 'segunda_mano' && isset($producto['estado'])): ?>
                                        <span class="discount-badge" style="background-color: #10b981;"><?php echo htmlspecialchars($producto['estado']); ?></span>
                                     <?php endif; ?>
                                </div>
                                <div class="product-actions">
                                    <a href="<?php echo $detalle_url; ?>" class="btn btn-primary">
                                        <i class="fas fa-info-circle"></i> Ver Detalles
                                    </a>
                                    <button class="btn btn-danger remove-from-wishlist" 
                                            data-wishlist-id="<?php echo $producto['lista_deseos_id']; ?>">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<style>
:root {
    --primary-color: #6d28d9;
    --primary-hover: #5b21b6;
    --secondary-color: #2563eb;
    --secondary-hover: #1d4ed8;
    --danger-color: #dc2626;
    --danger-hover: #b91c1c;
    --text-color: #1f2937;
    --light-text: #6b7280;
    --card-bg: #ffffff;
    --page-bg: #f3f4f6;
    --border-radius: 12px;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --hover-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Estilos para administradores */
body.admin {
    --primary-color: #ff0000;
    --primary-hover: #cc0000;
}

body {
    background-color: var(--page-bg);
    color: var(--text-color);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
}

/* Estilos del header */
.site-header {
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 8px 0;
    position: relative;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: #7e22ce;
    text-decoration: none;
}

body.admin .logo span {
    color: #ff0000;
}

.main-nav {
    display: flex;
    align-items: center;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 30px;
    margin: 0;
    padding: 0;
}

.nav-links li a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.nav-links li a:hover {
    color: #7e22ce;
}

body.admin .nav-links li a:hover {
    color: #ff0000;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-icon {
    color: #333;
    font-size: 1.2rem;
    transition: color 0.3s;
    text-decoration: none;
    position: relative;
}

.header-icon:hover {
    color: #7e22ce;
}

body.admin .header-icon:hover {
    color: #ff0000;
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #7e22ce;
    color: white;
    font-size: 0.75rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

body.admin .badge {
    background-color: #ff0000;
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

.profile-dropdown {
    position: relative;
}

/* Nuevo estilo para el avatar cuadrado */
.avatar-square {
    width: 32px;
    height: 32px;
    border-radius: 8px; /* Esquinas redondeadas pero cuadrado */
    background-color: #7e22ce;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.admin-avatar {
    background-color: #ff0000;
}

.dropdown-content {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 0.5rem;
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
    background-color: #f3f4f6;
    color: #7e22ce;
}

body.admin .dropdown-content a:hover {
    color: #ff0000;
}

.dropdown-content a i {
    width: 20px;
    text-align: center;
}

.admin-dropdown {
    border: 2px solid #ff0000;
}

.admin-dropdown a.admin-link {
    background-color: #ff0000;
    color: white;
}

.admin-dropdown a.admin-link:hover {
    background-color: #cc0000;
    color: white;
}

.auth-buttons {
    display: flex;
    gap: 10px;
}

.auth-buttons .btn {
    padding: 6px 20px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    width: auto;
}

.auth-buttons .btn-primary {
    background-color: #7e22ce;
    color: white;
    border: none;
}

.auth-buttons .btn-primary:hover {
    background-color: #6b21a8;
}

.auth-buttons .btn-outline {
    background-color: transparent;
    color: #7e22ce;
    border: 1px solid #7e22ce;
}

.auth-buttons .btn-outline:hover {
    background-color: rgba(126, 34, 206, 0.1);
}

.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #333;
    font-size: 1.5rem;
    cursor: pointer;
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: block;
    }
    
    .nav-links {
        display: none;
    }
    
    .header-actions {
        gap: 0.5rem;
    }
    
    .auth-buttons {
        display: none;
    }
}

/* Estilos para la lista de deseos */
@keyframes pulse-red {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
    }
}

.content {
    min-height: calc(100vh - 200px);
    padding: 2rem 0;
}

.wishlist-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.wishlist-container h1 {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--primary-color);
}

.empty-wishlist {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--card-bg);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    max-width: 600px;
    margin: 0 auto;
}

.empty-icon {
    font-size: 5rem;
    color: #e11d48;
    margin-bottom: 1.5rem;
    opacity: 0.7;
}

.empty-wishlist p {
    margin-bottom: 2rem;
    color: #666;
    font-size: 1.2rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

.product-card {
    background: var(--card-bg);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    min-height: 350px;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--hover-shadow);
}

.product-image {
    position: relative;
    overflow: hidden;
    height: 200px;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.btn-overlay {
    background: rgba(255, 255, 255, 0.9);
    color: var(--primary-color);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.btn-overlay:hover {
    background: var(--primary-color);
    color: white;
}

.category-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: var(--primary-color);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.product-card-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-card-content h3 {
    margin-bottom: 0.8rem;
    line-height: 1.4;
    height: 2.8rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.price-container {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.price {
    font-weight: bold;
    color: var(--primary-color);
    margin: 0;
}

.old-price {
    text-decoration: line-through;
    color: var(--light-text);
    font-size: 0.9rem;
    margin: 0;
}

.discount-badge {
    background: #e11d48;
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
}

.product-actions {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.7rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
    width: 100%;
    text-align: center;
}

.btn-primary {
    background-color: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-hover);
}

.btn-danger {
    background-color: var(--danger-color) !important;
    color: white;
}

.btn-danger:hover {
    background-color: var(--danger-hover) !important;
}

.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(109, 40, 217, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(109, 40, 217, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(109, 40, 217, 0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.5rem;
    }
}

@media (max-width: 480px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-card {
        max-width: 100%;
    }
}

/* Admin styling for header */
body.admin .site-header {
    border-bottom: 3px solid var(--admin-color);
}

body.admin .logo span {
    color: var(--admin-color);
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

body.admin .nav-links li a:hover {
    color: var(--admin-color);
}

body.admin .header-icon:hover {
    color: var(--admin-color);
}

body.admin .badge {
    background-color: var(--admin-color);
}

body.admin .dropdown-content a:hover {
    color: var(--admin-color);
}

body.admin .dropdown-content {
    border: 2px solid var(--admin-color);
}

body.admin .admin-link {
    background-color: var(--admin-color);
    color: white;
}

body.admin .admin-link:hover {
    background-color: var(--admin-dark);
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const removeButtons = document.querySelectorAll('.remove-from-wishlist');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('¿Estás seguro de que quieres eliminar este producto de tu lista de deseos?')) {
                return;
            }
            
            // Usar data-wishlist-id en lugar de data-product-id
            const wishlistId = this.getAttribute('data-wishlist-id');
            const productCard = this.closest('.product-card');
            
            // Añadir clase para animación de salida
            productCard.style.transition = 'all 0.5s ease';
            productCard.style.opacity = '0';
            productCard.style.transform = 'scale(0.8)';
            
            try {
                const response = await fetch('eliminar_de_lista.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    // Pasar el ID de la entrada de la lista de deseos
                    body: JSON.stringify({ wishlistId: wishlistId })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Esperar a que termine la animación antes de eliminar
                    setTimeout(() => {
                        productCard.remove();
                        
                        // Si no quedan productos, mostrar mensaje de lista vacía
                        if (document.querySelectorAll('.product-card').length === 0) {
                            location.reload();
                        }
                    }, 500);
                } else {
                    // Restaurar el elemento si hay error
                    productCard.style.opacity = '1';
                    productCard.style.transform = 'scale(1)';
                    alert(data.message || 'Error al eliminar el producto');
                }
            } catch (error) {
                // Restaurar el elemento si hay error
                productCard.style.opacity = '1';
                productCard.style.transform = 'scale(1)';
                console.error('Error:', error);
                alert('Ha ocurrido un error al eliminar el producto');
            }
        });
    });
});
</script>
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
