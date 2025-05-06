<?php
require_once 'config/database.php';
session_start();

// Obtener categorías para el selector
try {
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error obteniendo categorías: " . $e->getMessage());
}

// Obtener filtros
$categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$precio = isset($_GET['precio']) ? $_GET['precio'] : '';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Construir la consulta SQL base
$sql = "SELECT s.*, c.nombre as categoria_nombre 
        FROM segunda_mano s 
        LEFT JOIN categorias c ON s.categoria_id = c.id 
        WHERE 1=1";

// Aplicar filtros
if (!empty($categoria_id)) {
    $sql .= " AND s.categoria_id = :categoria_id";
}

if (!empty($precio)) {
    switch($precio) {
        case '0-20':
            $sql .= " AND s.precio <= 20";
            break;
        case '20-50':
            $sql .= " AND s.precio > 20 AND s.precio <= 50";
            break;
        case '50-100':
            $sql .= " AND s.precio > 50 AND s.precio <= 100";
            break;
        case '100+':
            $sql .= " AND s.precio > 100";
            break;
    }
}

if (!empty($busqueda)) {
    $sql .= " AND LOWER(s.nombre) LIKE LOWER(:busqueda)";
}

$sql .= " ORDER BY s.id DESC";

try {
    $stmt = $pdo->prepare($sql);
    
    if (!empty($categoria_id)) {
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
    }
    
    if (!empty($busqueda)) {
        $busquedaParam = "%$busqueda%";
        $stmt->bindParam(':busqueda', $busquedaParam, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segunda Mano - MGames</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="css/segunda_mano.css"> <!-- Estilos específicos -->
</head>
<body>
    <div class="content">
        <section class="segunda-mano-section">
            <div class="section-header">
                <h1>Juegos de Segunda Mano</h1>
                <p>Encuentra grandes títulos a precios increíbles</p>
            </div>
            
            <!-- Filtros -->
            <div class="filters-container">
                <form method="GET" class="filters-form" id="filterForm">
                    <div class="filter-group">
                        <h3>Filtrar por Categoría</h3>
                        <select name="categoria" class="filter-select" onchange="document.getElementById('filterForm').submit();">
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
                        <select name="precio" class="filter-select" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Todos los precios</option>
                            <option value="0-20" <?php echo $precio == '0-20' ? 'selected' : ''; ?>>Hasta 20€</option>
                            <option value="20-50" <?php echo $precio == '20-50' ? 'selected' : ''; ?>>20€ - 50€</option>
                            <option value="50-100" <?php echo $precio == '50-100' ? 'selected' : ''; ?>>50€ - 100€</option>
                            <option value="100+" <?php echo $precio == '100+' ? 'selected' : ''; ?>>Más de 100€</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <h3>Buscar Juegos</h3>
                        <div class="search-input-group">
                            <input type="text" 
                                   name="busqueda" 
                                   placeholder="Buscar por nombre del juego..." 
                                   value="<?php echo htmlspecialchars($busqueda); ?>"
                                   class="filter-input">
                            <button type="submit" class="search-button">Buscar</button>
                        </div>
                    </div>
                </form>
                <?php if (!empty($categoria_id) || !empty($precio) || !empty($busqueda)): ?>
                    <a href="segunda_mano.php" class="clear-filters">Limpiar filtros</a>
                <?php endif; ?>
            </div>

            <!-- Grid de productos -->
            <div class="products-grid">
                <?php if (empty($productos)): ?>
                    <div class="no-results">
                        <p>No se encontraron productos con los filtros seleccionados.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($productos as $producto): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            </div>
                            <div class="product-card-content">
                                <h3 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p class="product-price"><?php echo number_format($producto['precio'], 2); ?>€</p>
                                <p class="product-category"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                                <a href="detalle_segunda_mano.php?id=<?php echo $producto['id']; ?>" class="btn-details">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="add-game-container">
                <a href="agregar_segunda_mano.php" class="btn-add-game">
                    <span class="add-icon">+</span>
                    <span class="add-text">Añadir Juego de Segunda Mano</span>
                </a>
            </div>
        </section>
    </div>
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
</body>
</html>
