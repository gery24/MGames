<?php
session_start();
// Incluir archivo de configuración de la base de datos
require_once 'config/database.php';

$articulo = null; // Inicializar la variable artículo

// Verificar si se ha pasado un ID de artículo
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $articulo_id = intval($_GET['id']);

    // Consultar el artículo específico de la base de datos
    try {
        $stmt = $pdo->prepare("SELECT id, titulo, contenido, imagen_url, categoria, autor, fecha_publicacion FROM articulos_blog WHERE id = ? LIMIT 1");
        $stmt->execute([$articulo_id]);
        $articulo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no se encuentra el artículo, redirigir al blog
        if (!$articulo) {
            header('Location: blog.php');
            exit;
        }

    } catch (PDOException $e) {
        // Manejo de errores (adaptar según tu entorno)
        die("Error al cargar el artículo del blog: " . $e->getMessage());
    }

    $titulo = htmlspecialchars($articulo['titulo'] ?? 'Artículo del Blog'); // Usar el título del artículo

} else {
    // Leer artículos del blog desde la base de datos para la lista
    try {
        $stmt = $pdo->query("SELECT id, titulo, SUBSTRING(contenido, 1, 150) as contenido_corto, imagen_url, categoria, autor, fecha_publicacion, slug FROM articulos_blog ORDER BY fecha_publicacion DESC");
        $articulos_blog = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div style='color:red'>Error al obtener artículos del blog: " . $e->getMessage() . "</div>";
        $articulos_blog = [];
    }

    $titulo = "Blog y Guías - MGames";
}

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

    <?php if ($articulo): ?>
        <!-- --- Vista de detalle de artículo --- -->
        <section class="article-page">
            <div class="container">
                <article class="article-content">
                    <h1 class="article-title section-title"><?php echo htmlspecialchars($articulo['titulo'] ?? ''); ?></h1>

                    <div class="article-meta">
                        <span><i class="far fa-calendar"></i> <?php echo htmlspecialchars(date('d M, Y', strtotime($articulo['fecha_publicacion'] ?? ''))); ?></span>
                        <span><i class="far fa-user"></i> <?php echo htmlspecialchars($articulo['autor'] ?? ''); ?></span>
                        <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($articulo['categoria'] ?? ''); ?></span>
                    </div>

                    <?php if (!empty($articulo['imagen_url'])): ?>
                        <div class="article-image">
                            <img src="<?php echo htmlspecialchars($articulo['imagen_url']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo'] ?? ''); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="article-body">
                        <p><?php echo nl2br(htmlspecialchars($articulo['contenido'] ?? '')); ?></p> <!-- Usar nl2br para respetar saltos de línea -->
                    </div>

                    <div class="back-link">
                        <a href="blog.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Volver al Blog</a>
                    </div>
                </article>
            </div>
        </section>
    <?php else: ?>
        <!-- --- Vista de lista de artículos --- -->

        <!-- Hero Section - Adaptar o mantener según necesidad -->
        <section class="eventos-hero">
            <div class="hero-content">
                <h1>Nuestro Blog y Guías</h1> <!-- Título adaptado -->
                <p>Explora nuestros artículos, análisis y guías exclusivas</p> <!-- Subtítulo adaptado -->
            </div>
        </section>

        <!-- Sección de filtros -->
        <div class="eventos-filters">
            <div class="search-box">
                <input type="text" id="blog-search" placeholder="Buscar artículos..."> 
            </div>
            <div class="filter-options">
                <!-- Selector de categoría eliminado según solicitud anterior -->
            </div>
        </div>

        <!-- Contenedor para el grid de artículos del blog -->
        <div class="eventos-container">
            <div class="eventos-grid">
                <?php if (!empty($articulos_blog)): ?>
                    <?php foreach ($articulos_blog as $articulo_lista): ?>
                        <div class="evento-card"> <!-- Usar clase de tarjeta de evento -->
                            <div class="evento-image-container"> <!-- Contenedor de imagen de evento -->
                                <img src="<?php echo htmlspecialchars($articulo_lista['imagen_url'] ?? ''); ?>" alt="<?php echo htmlspecialchars($articulo_lista['titulo'] ?? ''); ?>">
                                <!-- Badge de categoría -->
                                <?php if (!empty($articulo_lista['categoria'])): ?>
                                    <div class="blog-category-badge"> <!-- Usar la clase que definimos antes -->
                                        <?php echo htmlspecialchars($articulo_lista['categoria'] ?? ''); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="evento-content"> <!-- Contenido de tarjeta de evento -->
                                <h3><?php echo htmlspecialchars($articulo_lista['titulo'] ?? ''); ?></h3>
                                <div class="evento-info"> <!-- Adaptar info para metadatos del blog -->
                                    <span><i class="far fa-calendar"></i> <?php echo htmlspecialchars(date('d M, Y', strtotime($articulo_lista['fecha_publicacion'] ?? ''))); ?></span>
                                    <span><i class="far fa-user"></i> <?php echo htmlspecialchars($articulo_lista['autor'] ?? ''); ?></span>
                                </div>
                                <p class="evento-descripcion">
                                    <?php
                                        // Mostrar el contenido corto obtenido directamente de la consulta (150 caracteres por SUBSTRING)
                                        $contenido_para_lista = htmlspecialchars($articulo_lista['contenido_corto'] ?? '');
                                        
                                        // Asegurarse de que siempre termine con '...'
                                        // El extracto ya viene truncado a 150 caracteres por la consulta SQL.
                                        // Solo necesitamos añadir los puntos suspensivos para la visualización.
                                        $extracto_mostrado = $contenido_para_lista . '...';

                                        echo nl2br($extracto_mostrado); // Usar nl2br para respetar saltos de línea
                                    ?>
                                </p>
                                <div class="evento-actions"> <!-- Acciones de tarjeta de evento -->
                                    <!-- Enlace para ver el artículo completo, ahora apunta a blog.php con el ID -->
                                    <a href="blog.php?id=<?php echo htmlspecialchars($articulo_lista['id'] ?? ''); ?>" class="btn btn-primary">Leer más</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay artículos en el blog en este momento.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Newsletter Section -->
        <section class="eventos-newsletter">
            <div class="newsletter-content">
                <h2>Suscríbete al Blog</h2> <!-- Título adaptado -->
                <p>Recibe las últimas publicaciones y noticias directamente en tu correo.</p> <!-- Subtítulo adaptado -->
                <form class="newsletter-form" action="suscribir.php" method="post"> <!-- Añadida acción y método al formulario -->
                    <input type="email" name="email" placeholder="Tu correo electrónico" required> <!-- Añadido name="email" -->
                    <button type="submit" class="btn btn-primary">Suscribirse</button>
                </form>
            </div>
        </section>

    <?php endif; ?>

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

<!-- Script para la búsqueda en la lista de artículos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Solo añadir funcionalidad de búsqueda si estamos en la vista de lista (sin ID en la URL)
    if (!window.location.search.includes('id=')) {
        const searchInput = document.getElementById('blog-search'); // ID del input de búsqueda del blog
        const articulos = document.querySelectorAll('.evento-card'); // Las tarjetas de artículo usan la clase de evento-card

        function filtrarArticulos() {
            const searchTerm = searchInput.value.toLowerCase();

            articulos.forEach(articulo => {
                const titulo = articulo.querySelector('h3').textContent.toLowerCase();

                let mostrar = titulo.includes(searchTerm);

                articulo.style.display = mostrar ? 'block' : 'none';
            });
        }

        if (searchInput) searchInput.addEventListener('input', filtrarArticulos);
    }
});
</script>

</body>
</html> 