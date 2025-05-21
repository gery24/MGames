<?php
// Incluir archivo de configuración de la base de datos
require_once 'config/database.php';

// Leer artículos del blog desde la base de datos
try {
    $stmt = $pdo->query("SELECT id, titulo, contenido, imagen_url, categoria, autor, fecha_publicacion, slug FROM articulos_blog ORDER BY fecha_publicacion DESC");
    $articulos_blog = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div style='color:red'>Error al obtener artículos del blog: " . $e->getMessage() . "</div>";
    $articulos_blog = [];
}

$titulo = "Blog y Guías - MGames";
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <!-- Estilos generales y de eventos (para replicar la apariencia) -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/eventos.css">
    <!-- Iconos de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php require_once 'includes/header.php'; ?>

<main class="eventos-page blog-page-adapted"> <!-- Usar clases de eventos y una específica si se necesita -->
    <!-- Hero Section - Adaptar o mantener según necesidad -->
    <section class="eventos-hero">
        <div class="hero-content">
            <h1>Nuestro Blog y Guías</h1> <!-- Título adaptado -->
            <p>Explora nuestros artículos, análisis y guías exclusivas</p> <!-- Subtítulo adaptado -->
        </div>
    </section>

    <!-- Sección de filtros - Se mantiene la estructura pero podría no tener funcionalidad para blog -->
    <!-- Puedes eliminar esta sección si los filtros no son relevantes para el blog -->
    <div class="eventos-filters">
        <div class="search-box">
            <input type="text" id="blog-search" placeholder="Buscar artículos..."> 
        </div>
        
        <div class="filter-options">
             <!-- Puedes añadir selectores para filtrar por categoría o autor si implementas esa funcionalidad -->
             <select id="filtro-categoria">
                <option value="">Todas las categorías</option>
                <!-- Opciones de categoría se cargarían aquí si tuvieras una tabla de categorías de blog -->
             </select>
        </div>
    </div>

    <!-- Contenedor para el grid de artículos del blog -->
    <div class="eventos-container">
        <div class="eventos-grid">
            <?php if (!empty($articulos_blog)): ?>
                <?php foreach ($articulos_blog as $articulo): ?>
                    <div class="evento-card"> <!-- Usar clase de tarjeta de evento -->
                        <div class="evento-image-container"> <!-- Contenedor de imagen de evento -->
                            <img src="<?php echo htmlspecialchars($articulo['imagen_url'] ?? ''); ?>" alt="<?php echo htmlspecialchars($articulo['titulo'] ?? ''); ?>">
                            <!-- Badge de categoría -->
                            <?php if (!empty($articulo['categoria'])): ?>
                                <div class="blog-category-badge"> <!-- Usar la clase que definimos antes -->
                                    <?php echo htmlspecialchars($articulo['categoria'] ?? ''); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="evento-content"> <!-- Contenido de tarjeta de evento -->
                            <h3><?php echo htmlspecialchars($articulo['titulo'] ?? ''); ?></h3>
                            <div class="evento-info"> <!-- Adaptar info para metadatos del blog -->
                                <span><i class="far fa-calendar"></i> <?php echo htmlspecialchars(date('d M, Y', strtotime($articulo['fecha_publicacion'] ?? ''))); ?></span>
                                <span><i class="far fa-user"></i> <?php echo htmlspecialchars($articulo['autor'] ?? ''); ?></span>
                            </div>
                            <p class="evento-descripcion">
                                <?php
                                    // Mostrar un extracto del contenido
                                    $contenido_corto = htmlspecialchars($articulo['contenido'] ?? '');
                                    $limite_caracteres = 150;
                                    if (strlen($contenido_corto) > $limite_caracteres) {
                                        $contenido_corto = substr($contenido_corto, 0, $limite_caracteres) . '...';
                                    }
                                    echo nl2br($contenido_corto);
                                ?>
                            </p>
                            <!-- No hay stats para blog, se omite .evento-stats -->
                            <div class="evento-actions"> <!-- Acciones de tarjeta de evento -->
                                <!-- Enlace para ver el artículo completo -->
                                <a href="detalle_articulo_blog.php?id=<?php echo htmlspecialchars($articulo['id'] ?? ''); ?>" class="btn btn-primary">Leer más</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay artículos en el blog en este momento.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Newsletter Section - Mantener o adaptar -->
    <section class="eventos-newsletter">
        <div class="newsletter-content">
            <h2>Suscríbete al Blog</h2> <!-- Título adaptado -->
            <p>Recibe las últimas publicaciones y noticias directamente en tu correo.</p> <!-- Subtítulo adaptado -->
            <form class="newsletter-form">
                <input type="email" placeholder="Tu correo electrónico" required>
                <button type="submit" class="btn btn-primary">Suscribirse</button>
            </form>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

<!-- Botón scroll arriba y scripts JS si los necesitas -->
<button id="scrollToTopBtn" aria-label="Volver arriba">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
       stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
    <polyline points="18 15 12 9 6 15"></polyline>
  </svg>
</button>

<!-- Estilos CSS para el botón scroll arriba -->
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
  display: none; /* Oculto por defecto */
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

<!-- Script JS para el botón scroll arriba -->
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

<!-- Si la página de eventos tenía scripts JS (como filtros), decido si mantenerlos o eliminarlos para el blog -->
<!-- En este caso, los filtros de eventos no son directamente aplicables, así que no los copio -->

</body>
</html> 