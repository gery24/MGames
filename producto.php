<?php
session_start();
require_once 'config/database.php';

// Obtener el ID del producto de la URL
$producto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Obtener los detalles del producto
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        header('Location: index.php');
        exit;
    }

} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

$titulo = $producto['nombre'] . " - MGames";
require_once 'includes/header.php';
?>

<div class="content">
    <div class="product-details">
        <div class="product-header">
            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                 class="product-image">
            <div class="product-info">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                
                <div class="product-actions">
                    <button class="btn add-to-cart">Añadir al Carrito</button>
                    <button class="btn add-to-wishlist" data-product-id="<?php echo $producto['id']; ?>">
                        <i class="fas fa-heart"></i> Añadir a Lista de Deseos
                    </button>
                </div>
            </div>
        </div>

        <!-- Sección de Detalles -->
        <div class="product-sections">
            <!-- Acerca del Juego -->
            <section class="product-section">
                <h2>Acerca del Juego</h2>
                <div class="section-content">
                    <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?>
                </div>
            </section>

            <!-- Requisitos del Sistema -->
            <section class="product-section">
                <h2>Requisitos del Sistema</h2>
                <div class="section-content">
                    <div class="requirements">
                        <div class="min-requirements">
                            <h3>Requisitos Mínimos</h3>
                            <ul>
                                <li>SO: Windows 10 64-bit</li>
                                <li>Procesador: Intel Core i5-2500K | AMD FX-6300</li>
                                <li>Memoria: 8 GB RAM</li>
                                <li>Gráficos: NVIDIA GTX 770 2GB | AMD Radeon R9 280</li>
                                <li>DirectX: Versión 11</li>
                                <li>Almacenamiento: 50 GB</li>
                            </ul>
                        </div>
                        <div class="rec-requirements">
                            <h3>Requisitos Recomendados</h3>
                            <ul>
                                <li>SO: Windows 10 64-bit</li>
                                <li>Procesador: Intel Core i7-4770K | AMD Ryzen 5 1500X</li>
                                <li>Memoria: 16 GB RAM</li>
                                <li>Gráficos: NVIDIA GTX 1060 6GB | AMD RX 580 8GB</li>
                                <li>DirectX: Versión 12</li>
                                <li>Almacenamiento: 50 GB SSD</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
.product-details {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.product-header {
    display: flex;
    gap: 2rem;
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-image {
    width: 400px;
    height: auto;
    object-fit: cover;
    border-radius: 10px;
}

.product-info {
    flex: 1;
}

.product-info h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.price {
    font-size: 1.5rem;
    color: #4747ff;
    font-weight: bold;
    margin: 1rem 0;
}

.category {
    display: inline-block;
    background: #f3f4f6;
    padding: 0.5rem 1rem;
    border-radius: 5px;
    margin: 1rem 0;
}

.product-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.add-to-wishlist {
    background-color: #ff4747 !important;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.add-to-wishlist:hover {
    background-color: #ff3333 !important;
}

/* Estilos para las secciones de detalles */
.product-sections {
    margin-top: 2rem;
}

.product-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.product-section h2 {
    color: #333;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

.requirements {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.min-requirements, .rec-requirements {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
}

.requirements h3 {
    color: #4747ff;
    margin-bottom: 1rem;
}

.requirements ul {
    list-style: none;
    padding: 0;
}

.requirements li {
    margin-bottom: 0.5rem;
    padding-left: 1.5rem;
    position: relative;
}

.requirements li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #4747ff;
}

@media (max-width: 768px) {
    .product-header {
        flex-direction: column;
    }
    
    .product-image {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .requirements {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wishlistButton = document.querySelector('.add-to-wishlist');
    
    wishlistButton.addEventListener('click', async function(e) {
        e.preventDefault();
        
        const productId = this.getAttribute('data-product-id');
        
        try {
            const response = await fetch('add_to_wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ productId: productId })
            });

            const data = await response.json();
            
            if (data.success) {
                // Si la operación fue exitosa, redirigir a lista_deseos.php
                window.location.href = 'lista_deseos.php';
            } else {
                // Si hay un error, mostrar el mensaje
                if (data.message === 'Debes iniciar sesión') {
                    window.location.href = 'login.php';
                } else {
                    alert(data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ha ocurrido un error al procesar tu solicitud');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>