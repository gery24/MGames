<?php
// Incluir archivo de configuración de la base de datos
require_once 'config/database.php';

// Verificar si se ha pasado un ID de evento en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $evento_id = $_GET['id'];

    // Leer el evento específico desde la base de datos
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, imagen_url, fecha_evento, hora_evento, lugar, descripcion FROM eventos WHERE id = ?");
        $stmt->execute([$evento_id]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el evento no se encuentra, redirigir o mostrar un error
        if (!$evento) {
            $error_message = "Evento no encontrado.";
        }

    } catch (PDOException $e) {
        $error_message = "Error al obtener detalles del evento: " . $e->getMessage();
    }

} else {
    // Si no se pasa un ID, mostrar un error
    $error_message = "ID de evento no especificado.";
}

$titulo = "MGames - " . ($evento['nombre'] ?? 'Detalles del Evento');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <!-- Aquí se incluirán los estilos globales y específicos de la página de evento -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/evento.css"> <!-- Archivo de estilos para esta página -->
    <!-- Iconos de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php require_once 'includes/header.php'; ?>

<main class="evento-detail-page">
    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="error-message" style="color:red; text-align:center;"><?php echo $error_message; ?></div>
        <?php elseif ($evento): ?>
            <article class="evento-detail">
                <div class="evento-header">
                    <h1><?php echo htmlspecialchars($evento['nombre']); ?></h1>
                </div>
                <div class="evento-body">
                    <div class="image-box"> <!-- Nuevo recuadro blanco para la imagen -->
                        <div class="evento-image">
                            <img src="<?php echo htmlspecialchars($evento['imagen_url']); ?>" alt="<?php echo htmlspecialchars($evento['nombre']); ?>">
                        </div>
                    </div>
                    <div class="details-box"> <!-- Nuevo recuadro blanco para detalles y descripción -->
                        <div class="evento-info-and-description">
                            <div class="evento-info-box">
                                <h3>Detalles del Evento</h3>
                                <p><i class="far fa-calendar-alt"></i> Fecha: <?php echo date('d/m/Y', strtotime($evento['fecha_evento'])); ?></p>
                                <p><i class="far fa-clock"></i> Hora: <?php echo date('H:i', strtotime($evento['hora_evento'])); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> Lugar: <?php echo htmlspecialchars($evento['lugar']); ?></p>
                            </div>
                            <div class="evento-description">
                                <h3>Descripción</h3>
                                <div class="description-content">
                                    <?php if ($evento['nombre'] === 'Lanzamiento Exclusivo'): ?>
                                        <p class="full-description">
                                            ¡Prepárate para el gran lanzamiento de un nuevo y emocionante juego!
                                            Este proyecto ha sido creado con pasión por tres talentosos desarrolladores:
                                            Miguel, Gerard y Victor. Su visión es dar vida a una innovadora saga
                                            de juegos de escape room inspirados en el irreverente humor y las situaciones
                                            alocadas al estilo de las películas de Torrente. Sumérgete en una aventura
                                            llena de puzles, referencias culturales y mucho desparpajo. ¡No te pierdas
                                            este evento único donde desvelaremos todos los detalles y podrás ser de los
                                            primeros en conocer este universo!
                                        </p>
                                        <p class="truncated-description">
                                            <?php echo substr(nl2br(htmlspecialchars('¡Prepárate para el gran lanzamiento de un nuevo y emocionante juego! Este proyecto ha sido creado con pasión por tres talentosos desarrolladores: Miguel, Gerard y Victor. Su visión es dar vida a una innovadora saga de juegos de escape room inspirados en el irreverente humor y las situaciones alocadas al estilo de las películas de Torrente. Sumérgete en una aventura llena de puzles, referencias culturales y mucho desparpajo. ¡No te pierdas este evento único donde desvelaremos todos los detalles y podrás ser de los primeros en conocer este universo!')), 0, 200); ?>...
                                        </p>
                                    <?php else: ?>
                                        <p class="full-description"><?php echo nl2br(htmlspecialchars($evento['descripcion'])); ?></p>
                                        <p class="truncated-description">
                                            <?php echo substr(nl2br(htmlspecialchars($evento['descripcion'])), 0, 200); ?>...
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <button class="read-more-btn btn btn-secondary btn-sm">Leer Más</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Podrías añadir más secciones aquí, como un mapa, formulario de registro, etc. -->
            </article>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const readMoreBtn = document.querySelector('.read-more-btn');
    const fullDescription = document.querySelector('.full-description');
    const truncatedDescription = document.querySelector('.truncated-description');

    if (readMoreBtn && fullDescription && truncatedDescription) {
        // Ocultar la descripción completa inicialmente
        fullDescription.style.display = 'none';

        readMoreBtn.addEventListener('click', function() {
            if (fullDescription.style.display === 'none') {
                fullDescription.style.display = 'block';
                truncatedDescription.style.display = 'none';
                readMoreBtn.textContent = 'Leer Menos';
            } else {
                fullDescription.style.display = 'none';
                truncatedDescription.style.display = 'block';
                readMoreBtn.textContent = 'Leer Más';
            }
        });
    }
});
</script>

</body>
</html> 