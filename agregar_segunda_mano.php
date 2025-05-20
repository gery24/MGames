<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario tiene el rol "CLIENTE"
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['CLIENTE', 'ADMIN'])) {
    die("Acceso denegado. Solo los usuarios con una cuenta creada pueden agregar juegos de segunda mano.");
}

// Obtener categorías de la base de datos
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Eliminar código para obtener plataformas de la base de datos

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Juego de Segunda Mano</title>
    <link rel="stylesheet" href="css/agregar_segundamano.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

    <div class='form-container'>
        <div id="add-game-form">
            <h2>Añadir Juego de Segunda Mano</h2>
            <form method="POST" action="guardar_segunda_mano.php" enctype="multipart/form-data">
                <input type="text" name="nombre" placeholder="Nombre del Juego" required>
                <input type="text" name="descripcion" placeholder="Descripción" required>
                <div class='form-group'>
                    <label for='comentario'>Comentario adicional:</label>
                    <textarea id='comentario' name='comentario' rows='4' placeholder='Añade cualquier comentario que quieras compartir sobre el juego...'></textarea>
                </div>
                <input type="number" name="precio" placeholder="Precio" required>
                <select name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['id']); ?>">
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!-- Campo de selección de plataformas como checkboxes -->
                <div class='form-group'>
                    <label>Plataformas:</label>
                    <div class='checkbox-group'>
                        <label>
                            <input type='checkbox' name='plataformas[]' value='fotosWeb/pc.png'>
                            PC
                        </label>
                        <label>
                            <input type='checkbox' name='plataformas[]' value='fotosWeb/ps.png'>
                            PlayStation
                        </label>
                        <label>
                            <input type='checkbox' name='plataformas[]' value='fotosWeb/xbox.png'>
                            Xbox
                        </label>
                        <label>
                            <input type='checkbox' name='plataformas[]' value='fotosWeb/switch.png'>
                            Switch
                        </label>
                    </div>
                </div>
                <div class='form-group'>
                    <label>Condición del juego:</label>
                    <div class='radio-group'>
                        <label>
                            <input type='radio' name='condicion' value='Nuevo' required>
                            Nuevo
                        </label>
                        <label>
                            <input type='radio' name='condicion' value='Seminuevo'>
                            Seminuevo
                        </label>
                        <label>
                            <input type='radio' name='condicion' value='Usado'>
                            Usado
                        </label>
                    </div>
                </div>
                <input type="file" name="imagen" accept="image/*" required>
                <button type="submit">Añadir Juego</button>
            </form>
        </div>
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