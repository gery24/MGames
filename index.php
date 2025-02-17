<?php
session_start();
require_once 'config/database.php';

try {
    // Verificar la conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener categorías
    $stmt = $pdo->query("SELECT id, nombre FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener filtros
    $categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : null;
    $rango_precio = isset($_GET['precio']) ? $_GET['precio'] : null;
    $busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : null;

    // Construir la consulta base
    $query = "SELECT p.*, c.nombre as categoria_nombre 
              FROM productos p 
              LEFT JOIN categorias c ON p.categoria_id = c.id";
    $params = [];

    // Añadir filtros si existen
    $where = [];
    if ($categoria_id) {
        $where[] = "p.categoria_id = ?";
        $params[] = $categoria_id;
    }

    // Añadir filtro de precio
    if ($rango_precio) {
        switch($rango_precio) {
            case '0-30':
                $where[] = "p.precio < 30";
                break;
            case '30-40':
                $where[] = "p.precio BETWEEN 30 AND 40";
                break;
            case '40-50':
                $where[] = "p.precio BETWEEN 40 AND 50";
                break;
            case '50-60':
                $where[] = "p.precio BETWEEN 50 AND 60";
                break;
            case '60+':
                $where[] = "p.precio > 60";
                break;
        }
    }

    // Añadir búsqueda al WHERE si existe
    if ($busqueda) {
        $where[] = "p.nombre LIKE ?";
        $params[] = "%$busqueda%";
    }

    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }

    $query .= " ORDER BY p.id DESC";

    // Ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Mostrar información detallada del error
    echo "Error en la base de datos: " . $e->getMessage() . "<br>";
    echo "SQL State: " . $e->getCode() . "<br>";
    // Mostrar la estructura actual de las tablas
    try {
        echo "<h3>Estructura de la tabla categorias:</h3>";
        foreach($pdo->query("DESCRIBE categorias") as $row) {
            print_r($row);
            echo "<br>";
        }
        echo "<h3>Estructura de la tabla productos:</h3>";
        foreach($pdo->query("DESCRIBE productos") as $row) {
            print_r($row);
            echo "<br>";
        }
    } catch(PDOException $e2) {
        echo "Error al obtener la estructura de las tablas: " . $e2->getMessage();
    }
    die();
}

// Incluir el header compartido
$titulo = "MGames - Tu tienda de videojuegos";
require_once 'includes/header.php';
?>

<div class="content">
    <!-- Hero Section -->
    <header class="hero">
        <h1>Bienvenido a MGames</h1>
        <p>Tu destino para los mejores videojuegos</p>
    </header>

    <!-- Filtros -->
    <section class="filters">
        <div class="filter-container">
            <!-- Filtro de Categorías -->
            <div class="filter">
                <h2>Filtrar por Categoría</h2>
                <form method="GET" action="index.php" class="filter-form">
                    <?php if(isset($_GET['precio'])): ?>
                        <input type="hidden" name="precio" value="<?php echo htmlspecialchars($_GET['precio']); ?>">
                    <?php endif; ?>
                    <?php if(isset($_GET['buscar'])): ?>
                        <input type="hidden" name="buscar" value="<?php echo htmlspecialchars($_GET['buscar']); ?>">
                    <?php endif; ?>
                    <select name="categoria" onchange="this.form.submit()">
                        <option value="">Todas las categorías</option>
                        <?php foreach($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                                <?php echo (isset($categoria_id) && $categoria_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- Filtro de Precios -->
            <div class="filter">
                <h2>Filtrar por Precio</h2>
                <form method="GET" action="index.php" class="filter-form">
                    <?php if(isset($_GET['categoria'])): ?>
                        <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($_GET['categoria']); ?>">
                    <?php endif; ?>
                    <?php if(isset($_GET['buscar'])): ?>
                        <input type="hidden" name="buscar" value="<?php echo htmlspecialchars($_GET['buscar']); ?>">
                    <?php endif; ?>
                    <select name="precio" onchange="this.form.submit()">
                        <option value="">Todos los precios</option>
                        <option value="0-30" <?php echo ($rango_precio == '0-30') ? 'selected' : ''; ?>>Menos de 30€</option>
                        <option value="30-40" <?php echo ($rango_precio == '30-40') ? 'selected' : ''; ?>>30€ - 40€</option>
                        <option value="40-50" <?php echo ($rango_precio == '40-50') ? 'selected' : ''; ?>>40€ - 50€</option>
                        <option value="50-60" <?php echo ($rango_precio == '50-60') ? 'selected' : ''; ?>>50€ - 60€</option>
                        <option value="60+" <?php echo ($rango_precio == '60+') ? 'selected' : ''; ?>>Más de 60€</option>
                    </select>
                </form>
            </div>

            <!-- Barra de búsqueda -->
            <div class="filter">
                <h2>Buscar Juegos</h2>
                <form method="GET" action="index.php" class="filter-form">
                    <?php if(isset($_GET['categoria'])): ?>
                        <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($_GET['categoria']); ?>">
                    <?php endif; ?>
                    <?php if(isset($_GET['precio'])): ?>
                        <input type="hidden" name="precio" value="<?php echo htmlspecialchars($_GET['precio']); ?>">
                    <?php endif; ?>
                    <div class="search-form">
                        <input type="text" name="buscar" placeholder="Buscar juegos..." 
                               value="<?php echo htmlspecialchars($_GET['buscar'] ?? ''); ?>">
                        <button type="submit" class="btn">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Productos -->
    <section class="featured-products">
        <h2>Productos</h2>
        <div class="products-grid">
            <?php foreach($productos as $producto): ?>
                <div class="product-card">
                    <img src="<?php echo $producto['imagen'] ?? 'images/default.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <div class="product-card-content">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="price">€<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?> 