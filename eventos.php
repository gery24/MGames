<?php
// Aquí deberías incluir la lógica para conectar a la base de datos y obtener los datos de los eventos.
// Por ejemplo:
// require_once 'config/database.php';
// $stmt = $pdo->query("SELECT * FROM eventos ORDER BY fecha ASC");
// $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGames - Próximos Eventos</title>
    <!-- Aquí se incluirán los estilos específicos de eventos -->
    <link rel="stylesheet" href="css/eventos.css">
    <!-- Posiblemente necesites incluir estilos globales si no están en el header -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Iconos de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php require_once 'includes/header.php'; ?>

<main class="eventos-page">
    <div class="container">
        <h1 class="page-title">Próximos Eventos de Gaming</h1>
        <!-- Opcional: Decoración del título -->
        <div class="title-decoration">
            <div class="title-line"></div>
            <div class="title-dot"></div>
        </div>

        <div class="eventos-list">
            <?php
            // Aquí iría el bucle foreach para mostrar cada evento obtenido de la base de datos.
            // Ejemplo (descomentar y adaptar cuando tengas la lógica de DB):
            /*
            if (!empty($eventos)) {
                foreach ($eventos as $evento) {
                    echo '<div class="evento-item">';
                    echo '<div class="evento-image-container">';
                    echo '<img src="' . htmlspecialchars($evento['imagen']) . '" alt="' . htmlspecialchars($evento['nombre']) . '">';
                    echo '<div class="evento-fecha">';
                    echo '<span class="evento-dia">' . date('d', strtotime($evento['fecha'])) . '</span>';
                    echo '<span class="evento-mes">' . date('M', strtotime($evento['fecha'])) . '</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="evento-content">';
                    echo '<h3>' . htmlspecialchars($evento['nombre']) . '</h3>';
                    echo '<div class="evento-info">';
                    echo '<p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($evento['lugar']) . '</p>';
                    echo '<p><i class="far fa-clock"></i> ' . htmlspecialchars($evento['hora']) . '</p>';
                    echo '</div>';
                    echo '<p class="evento-descripcion">' . htmlspecialchars($evento['descripcion']) . '</p>';
                    echo '<div class="evento-actions">';
                    echo '<a href="evento.php?id=' . htmlspecialchars($evento['id']) . '" class="btn btn-primary">Más Información</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No hay eventos próximos en este momento.</p>';
            }
            */
            ?>
            <!-- Placeholder o contenido estático si no usas DB por ahora -->
             <p>Lista de eventos próximamente...</p>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

</body>
</html> 