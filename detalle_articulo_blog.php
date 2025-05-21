<?php
require_once 'config/database.php'; // Incluir archivo de configuración de la base de datos

// Verificar si se ha pasado un ID de artículo
if (!isset($_GET['id'])) {
    // Redirigir a la página principal del blog si no hay ID
    header('Location: blog.php');
    exit;
}

$articulo_id = intval($_GET['id']);

// Consultar el artículo específico de la base de datos
try {
    $stmt = $pdo->prepare("SELECT id, titulo, contenido, imagen_url, categoria, autor, fecha_publicacion FROM articulos_blog WHERE id = ?");
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
require_once 'includes/header.php'; // Incluir el header
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <!-- Posiblemente necesites incluir estilos globales si no están en el header -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Estilos específicos del blog si existen -->
    <link rel="stylesheet" href="css/blog.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome para iconos -->
</head>
<body>

<div class="content">
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
</div>

<?php require_once 'includes/footer.php'; ?>
</body>
</html> 