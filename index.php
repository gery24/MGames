<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    // Verificar la conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener categorías
    $stmt = $pdo->query("SELECT id, nombre FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener filtros
    $categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    $rango_precio = isset($_GET['precio']) ? $_GET['precio'] : null;
    $busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : null;

    // Construir la consulta base
    $query = "SELECT p.*, c.nombre as categoria_nombre 
              FROM productos p 
              LEFT JOIN categorias c ON p.categoria_id = c.id 
              WHERE p.segunda_mano = 0";
    $params = [];

    // Añadir filtros si existen
    if ($categoria_id) {
        $query .= " AND p.categoria_id = ?";
        $params[] = $categoria_id;
    }

    // Añadir filtro de precio
    if ($rango_precio) {
        switch($rango_precio) {
            case '0-30':
                $query .= " AND p.precio < 30";
                break;
            case '30-40':
                $query .= " AND p.precio BETWEEN 30 AND 40";
                break;
            case '40-50':
                $query .= " AND p.precio BETWEEN 40 AND 50";
                break;
            case '50-60':
                $query .= " AND p.precio BETWEEN 50 AND 60";
                break;
            case '60+':
                $query .= " AND p.precio > 60";
                break;
        }
    }

    // Añadir búsqueda si existe
    if ($busqueda) {
        $query .= " AND (LOWER(p.nombre) LIKE LOWER(?) OR LOWER(p.descripcion) LIKE LOWER(?))";
        $busquedaParam = "%$busqueda%";
        $params[] = $busquedaParam;
        $params[] = $busquedaParam;
    }

    $query .= " ORDER BY p.id DESC";
    
    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container">
    <div class="filters-container">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <h3>Filtrar por Categoría</h3>
                <select name="categoria" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <h3>Filtrar por Precio</h3>
                <select name="precio" class="filter-select" onchange="this.form.submit()">
                    <option value="">Todos los precios</option>
                    <option value="0-30" <?php echo $rango_precio == '0-30' ? 'selected' : ''; ?>>Menos de 30€</option>
                    <option value="30-40" <?php echo $rango_precio == '30-40' ? 'selected' : ''; ?>>30€ - 40€</option>
                    <option value="40-50" <?php echo $rango_precio == '40-50' ? 'selected' : ''; ?>>40€ - 50€</option>
                    <option value="50-60" <?php echo $rango_precio == '50-60' ? 'selected' : ''; ?>>50€ - 60€</option>
                    <option value="60+" <?php echo $rango_precio == '60+' ? 'selected' : ''; ?>>Más de 60€</option>
                </select>
            </div>

            <div class="filter-group">
                <h3>Buscar Juegos</h3>
                <div class="search-input-group">
                    <input type="text" 
                           name="buscar" 
                           class="filter-input" 
                           placeholder="Buscar juegos..." 
                           value="<?php echo htmlspecialchars($busqueda ?? ''); ?>">
                    <button type="submit" class="search-button">Buscar</button>
                </div>
            </div>
        </form>
        <?php if ($categoria_id || $rango_precio || $busqueda): ?>
            <a href="index.php" class="clear-filters">Limpiar filtros</a>
        <?php endif; ?>
    </div>

    <section class="featured-products">
        <h2>Productos</h2>
        
        <?php if (empty($productos)): ?>
            <div class="no-results">
                <p>No se encontraron productos con los filtros seleccionados.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($productos as $producto): ?>
                    <div class="product-card <?php echo (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN') ? 'admin-style' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                            <a href="detalle.php?id=<?php echo $producto['id']; ?>" class="btn">Ver Detalles</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>