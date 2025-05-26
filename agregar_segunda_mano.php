<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario tiene el rol "CLIENTE" o "ADMIN"
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['CLIENTE', 'ADMIN'])) {
    // Redirigir a la página de login si el usuario no está logueado o no tiene el rol adecuado
    header('Location: login.php');
    exit;
}

// Obtener categorías de la base de datos
$stmt = $pdo->query("SELECT * FROM categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Juego de Segunda Mano</title>
    <style>
        /* Estilos directamente en la página para evitar problemas de carga */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            text-align: center;
            color: #1e293b;
            margin-bottom: 2rem;
            font-size: 1.8rem;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 1.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #124779;
            outline: none;
            box-shadow: 0 0 0 2px rgba(18, 71, 121, 0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        
        /* Estilos para los botones de condición */
        .condition-options {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .condition-option {
            flex: 1;
            position: relative;
        }
        
        .condition-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .condition-option label {
            display: block;
            padding: 12px;
            text-align: center;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8fafc;
            font-weight: 500;
        }
        
        .condition-option:nth-child(1) label {
            border-left: 4px solid #10b981; /* Verde para Nuevo */
        }
        
        .condition-option:nth-child(2) label {
            border-left: 4px solid #f59e0b; /* Amarillo para Seminuevo */
        }
        
        .condition-option:nth-child(3) label {
            border-left: 4px solid #ef4444; /* Rojo para Usado */
        }
        
        .condition-option input[type="radio"]:checked + label {
            background-color: #f0f9ff;
            border-color: #124779;
            box-shadow: 0 4px 6px rgba(18, 71, 121, 0.1);
            font-weight: 600;
        }
        
        .condition-option:nth-child(1) input[type="radio"]:checked + label {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
        }
        
        .condition-option:nth-child(2) input[type="radio"]:checked + label {
            background-color: rgba(245, 158, 11, 0.1);
            border-color: #f59e0b;
        }
        
        .condition-option:nth-child(3) input[type="radio"]:checked + label {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
        }
        
        .condition-option label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Estilos para las plataformas */
        .platform-options {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .platform-option {
            position: relative;
            flex-basis: calc(50% - 0.5rem);
        }
        
        .platform-option input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .platform-option label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }
        
        .platform-option label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-color: #124779;
        }
        
        .platform-option input[type="checkbox"]:checked + label {
            background-color: #f0f9ff;
            border-color: #124779;
            box-shadow: 0 4px 6px rgba(18, 71, 121, 0.1);
        }
        
        .platform-option img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }
        
        /* Estilos para el botón de subir archivo */
        .file-input {
            margin-bottom: 1.5rem;
        }
        
        /* Estilos para el botón de envío */
        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: #124779;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        button[type="submit"]:hover {
            background-color: #0f3a5f;
        }
        
        /* Estilos para el botón de scroll */
        #scrollToTopBtn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: #0d6efd;
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .condition-options {
                flex-direction: column;
            }
            
            .platform-option {
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Añadir Juego de Segunda Mano</h2>
    
    <form method="POST" action="guardar_segunda_mano.php" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre del Juego" required>
        
        <input type="text" name="descripcion" placeholder="Descripción" required>
        
        <div class="form-group">
            <label for="comentario">Comentario adicional:</label>
            <textarea id="comentario" name="comentario" rows="4" placeholder="Añade cualquier comentario que quieras compartir sobre el juego..."></textarea>
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
        
        <div class="form-group">
            <label>Condición del juego:</label>
            <div class="condition-options">
                <div class="condition-option">
                    <input type="radio" id="nuevo" name="condicion" value="Nuevo" required>
                    <label for="nuevo">Nuevo</label>
                </div>
                <div class="condition-option">
                    <input type="radio" id="seminuevo" name="condicion" value="Seminuevo">
                    <label for="seminuevo">Seminuevo</label>
                </div>
                <div class="condition-option">
                    <input type="radio" id="usado" name="condicion" value="Usado">
                    <label for="usado">Usado</label>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Plataformas disponibles:</label>
            <div class="platform-options">
                <div class="platform-option">
                    <input type="checkbox" id="pc" name="plataformas[]" value="pc">
                    <label for="pc">
                        <img src="fotosWeb/pc.png" alt="Icono PC">
                        PC
                    </label>
                </div>
                <div class="platform-option">
                    <input type="checkbox" id="playstation" name="plataformas[]" value="playstation">
                    <label for="playstation">
                        <img src="fotosWeb/ps.png" alt="Icono PlayStation">
                        PlayStation
                    </label>
                </div>
                <div class="platform-option">
                    <input type="checkbox" id="xbox" name="plataformas[]" value="xbox">
                    <label for="xbox">
                        <img src="fotosWeb/xbox.png" alt="Icono Xbox">
                        Xbox
                    </label>
                </div>
                <div class="platform-option">
                    <input type="checkbox" id="switch" name="plataformas[]" value="switch">
                    <label for="switch">
                        <img src="fotosWeb/switch.png" alt="Icono Switch">
                        Switch
                    </label>
                </div>
            </div>
        </div>
        
        <div class="file-input">
            <input type="file" name="imagen" accept="image/*" required>
        </div>
        
        <button type="submit">Añadir Juego</button>
    </form>
</div>

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

.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
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
    // Script para el botón de scroll
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
