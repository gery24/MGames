<?php
session_start();
require_once 'config/database.php';

// Redirigir si el usuario no está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

try {
    // Obtener los productos de la lista de deseos del usuario
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM lista_deseos l
        JOIN productos p ON l.producto_id = p.id
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE l.usuario_id = ?
        ORDER BY l.fecha_agregado DESC
    ");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $productos = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

$titulo = "Lista de Deseos - MGames";
require_once 'includes/header.php';
?>

<div class="content">
    <div class="wishlist-container">
        <h1>Mi Lista de Deseos</h1>
        
        <?php if (empty($productos)): ?>
            <div class="empty-wishlist">
                <p>Tu lista de deseos está vacía</p>
                <a href="index.php" class="btn">Explorar Juegos</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($productos as $producto): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                            <div class="product-actions">
                                <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn">Ver Detalles</a>
                                <button class="btn btn-danger remove-from-wishlist" 
                                        data-product-id="<?php echo $producto['id']; ?>">
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
.wishlist-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.empty-wishlist {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.empty-wishlist p {
    margin-bottom: 1rem;
    font-size: 1.2rem;
    color: #666;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn-danger {
    background-color: #dc2626 !important;
}

.btn-danger:hover {
    background-color: #b91c1c !important;
}

.product-card {
    height: auto;
    min-height: 350px;
    display: flex;
    flex-direction: column;
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-card-content {
    padding: 1rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-actions {
    margin-top: auto;
    flex-direction: column;
    gap: 0.5rem;
}

.product-actions .btn {
    width: 100%;
    text-align: center;
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
            
            const productId = this.getAttribute('data-product-id');
            
            try {
                const response = await fetch('eliminar_de_lista.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ productId: productId })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Eliminar el producto del DOM
                    this.closest('.product-card').remove();
                    
                    // Si no quedan productos, mostrar mensaje de lista vacía
                    if (document.querySelectorAll('.product-card').length === 0) {
                        location.reload();
                    }
                } else {
                    alert(data.message || 'Error al eliminar el producto');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ha ocurrido un error al eliminar el producto');
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>