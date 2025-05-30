<?php
session_start();
// Incluir archivo de configuración de la base de datos
require_once 'config/database.php';

// Leer eventos desde la base de datos
try {
    $stmt = $pdo->query("SELECT id, nombre, imagen_url, fecha_evento, hora_evento, lugar, descripcion, slug FROM eventos ORDER BY fecha_evento ASC");
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div style='color:red'>Error al obtener eventos: " . $e->getMessage() . "</div>";
    $eventos = [];
}
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
    <!-- Hero Section -->
    <section class="eventos-hero">
        <div class="hero-content">
            <h1>Próximos Eventos de Gaming</h1>
            <p>Descubre y participa en los eventos más emocionantes del mundo gaming</p>
        </div>
    </section>

    <!-- Filtros de Eventos (fuera del container) -->
    <div class="eventos-filters">
        <div class="search-box">
            <input type="text" id="evento-search" placeholder="Buscar eventos..."> 
            <!-- <i class="fas fa-search"></i> -->
        </div>
        
        <div class="filter-options">
            <select id="filtro-fecha">
                <option value="">Todas las fechas</option>
                <option value="proximos">Próximos 7 días</option>
                <option value="mes">Este mes</option>
                <option value="tres-meses">Próximos 3 meses</option>
            </select>
            <select id="filtro-tipo">
                <option value="">Todos los tipos</option>
                <option value="torneo">Torneos</option>
                <option value="lanzamiento">Lanzamientos</option>
                <option value="convencion">Convenciones</option>
            </select>
        </div>
    </div>

    <!-- Contenedor solo para el grid de eventos -->
    <div class="eventos-container">
        <div class="eventos-grid">
            <?php foreach ($eventos as $evento): ?>
                <div class="evento-card" data-fecha="<?php echo $evento['fecha_evento']; ?>" data-tipo="<?php echo strtolower(explode(' ', $evento['nombre'])[0]); ?>">
                    <div class="evento-image-container">
                        <img src="<?php echo htmlspecialchars($evento['imagen_url']); ?>" alt="<?php echo htmlspecialchars($evento['nombre']); ?>">
                        <div class="evento-fecha">
                            <span class="evento-dia"><?php echo date('d', strtotime($evento['fecha_evento'])); ?></span>
                            <span class="evento-mes"><?php echo strtoupper(date('M', strtotime($evento['fecha_evento']))); ?></span>
                        </div>
                    </div>
                    <div class="evento-content">
                        <h3><?php echo htmlspecialchars($evento['nombre']); ?></h3>
                        <div class="evento-info">
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($evento['lugar']); ?></p>
                            <p><i class="far fa-clock"></i> <?php echo date('H:i', strtotime($evento['hora_evento'])); ?></p>
                        </div>
                        <p class="evento-descripcion">
                            <?php
                                // Truncar la descripción a unas 150 caracteres (ajustar según necesidad)
                                $descripcion_corta = htmlspecialchars($evento['descripcion']);
                                $limite_caracteres = 150;
                                if (strlen($descripcion_corta) > $limite_caracteres) {
                                    $descripcion_corta = substr($descripcion_corta, 0, $limite_caracteres) . '...';
                                }
                                echo nl2br($descripcion_corta);
                            ?>
                        </p>
                        <div class="evento-stats">
                            <span><i class="fas fa-users"></i> 150+ Asistentes</span>
                            <span><i class="fas fa-trophy"></i> Premios</span>
                        </div>
                        <div class="evento-actions">
                            <a href="evento.php?id=<?php echo $evento['id']; ?>" class="btn btn-primary">Ver Más</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Newsletter Section (fuera del container) -->
    <section class="eventos-newsletter">
        <div class="newsletter-content">
            <h2>¡No te pierdas ningún evento!</h2>
            <p>Suscríbete para recibir notificaciones de nuevos eventos y actualizaciones</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Tu correo electrónico" required>
                <button type="submit" class="btn btn-primary">Suscribirse</button> <br>
            </form>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

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

<script>
// Funcionalidad de búsqueda y filtrado
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('evento-search');
    const filtroFecha = document.getElementById('filtro-fecha');
    const filtroTipo = document.getElementById('filtro-tipo');
    const eventos = document.querySelectorAll('.evento-card');

    function filtrarEventos() {
        const searchTerm = searchInput.value.toLowerCase();
        const fechaSeleccionada = filtroFecha.value;
        const tipoSeleccionado = filtroTipo.value;

        eventos.forEach(evento => {
            const nombre = evento.querySelector('h3').textContent.toLowerCase();
            const fecha = evento.dataset.fecha;
            const tipo = evento.dataset.tipo;
            
            let mostrar = nombre.includes(searchTerm);
            
            if (fechaSeleccionada) {
                const fechaEvento = new Date(fecha);
                const hoy = new Date();
                
                switch(fechaSeleccionada) {
                    case 'proximos':
                        const sieteDias = new Date(hoy.getTime() + 7 * 24 * 60 * 60 * 1000);
                        mostrar = mostrar && fechaEvento <= sieteDias;
                        break;
                    case 'mes':
                        const finMes = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
                        mostrar = mostrar && fechaEvento <= finMes;
                        break;
                    case 'tres-meses':
                        const tresMeses = new Date(hoy.getFullYear(), hoy.getMonth() + 3, hoy.getDate());
                        mostrar = mostrar && fechaEvento <= tresMeses;
                        break;
                }
            }
            
            if (tipoSeleccionado) {
                mostrar = mostrar && tipo === tipoSeleccionado;
            }
            
            evento.style.display = mostrar ? 'block' : 'none';
        });
    }

    searchInput.addEventListener('input', filtrarEventos);
    filtroFecha.addEventListener('change', filtrarEventos);
    filtroTipo.addEventListener('change', filtrarEventos);

    // Animación al hacer hover sobre las tarjetas
    eventos.forEach(evento => {
        evento.addEventListener('mouseenter', function() {
            this.querySelector('.evento-overlay').style.opacity = '1';
        });
        
        evento.addEventListener('mouseleave', function() {
            this.querySelector('.evento-overlay').style.opacity = '0';
        });
    });
});
</script>

</body>
</html> 