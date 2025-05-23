<?php

session_start();

$titulo = "Contacto - MGames";
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="css/styles.css"> <!-- CSS general -->
<link rel="stylesheet" href="css/contacto.css"> <!-- CSS específico para contacto -->

<style>
/* Estilos para la página de contacto */
:root {
  --primary-color: #6c5ce7;
  --secondary-color: #a29bfe;
  --accent-color: #fd79a8;
  --text-color: #2d3436;
  --light-color: #f5f6fa;
  --dark-color: #2d3436;
  --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.contact-container {
  background-color: white;
  border-radius: 15px;
  box-shadow: var(--shadow);
  overflow: hidden;
  margin-bottom: 50px;
}

.contact-container h1 {
  text-align: center;
  color: var(--primary-color);
  font-size: 2.5rem;
  margin: 30px 0;
  position: relative;
}

.contact-container h1:after {
  content: "";
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
  border-radius: 2px;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 30px;
  padding: 20px;
}

@media (min-width: 768px) {
  .contact-info {
    flex-direction: row;
  }
}

.contact-details,
.contact-form {
  flex: 1;
  padding: 30px;
  border-radius: 10px;
}

.contact-details {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); /* Fondo degradado */
  color: white; /* Color de texto blanco */
  position: relative;
  overflow: hidden;
}

.contact-details:before {
  content: "";
  position: absolute;
  top: -50px;
  right: -50px;
  width: 100px;
  height: 100px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
}

.contact-details h2,
.contact-form h2 {
  margin-bottom: 25px;
  font-size: 1.8rem;
  position: relative;
  display: inline-block;
}

.contact-details h2:after,
.contact-form h2:after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 40px;
  height: 3px;
  background-color: var(--accent-color);
  border-radius: 2px;
}

.info-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;
  transition: var(--transition);
}

.info-item:hover {
  transform: translateX(5px);
}

.info-item i {
  font-size: 1.2rem;
  margin-right: 15px;
  margin-top: 5px;
  color: white; /* Cambiar color de icono a blanco para el fondo degradado */
}

.info-item p {
  margin: 0;
  line-height: 1.6;
}

.contact-form {
  background-color: var(--light-color);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: var(--text-color);
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: var(--transition);
  background-color: white;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
}

.form-group textarea {
  min-height: 150px;
  resize: vertical;
}

.contact-form .btn { /* Estilo específico para el botón del formulario de contacto */
  background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: inline-block;
  text-transform: uppercase;
  letter-spacing: 1px;
  box-shadow: 0 4px 10px rgba(108, 92, 231, 0.3);
}

.contact-form .btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
}

.contact-form .btn:active {
  transform: translateY(1px);
}

/* Animaciones */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.contact-container {
  animation: fadeIn 0.8s ease-out;
}

.info-item:nth-child(1) {
  animation: fadeIn 0.5s ease-out 0.2s both;
}
.info-item:nth-child(2) {
  animation: fadeIn 0.5s ease-out 0.4s both;
}
.info-item:nth-child(3) {
  animation: fadeIn 0.5s ease-out 0.6s both;
}
.info-item:nth-child(4) {
  animation: fadeIn 0.5s ease-out 0.8s both;
}

/* Responsive adjustments */
@media (max-width: 767px) {
  .contact-details,
  .contact-form {
    padding: 20px;
  }

  .contact-container h1 {
    font-size: 2rem;
  }

  .contact-details h2,
  .contact-form h2 {
    font-size: 1.5rem;
  }

  .btn {
    width: 100%;
  }
}
</style>

<div class="content">
    <div class="contact-container">
        <h1>Contacto</h1>
        
        <?php if ($isLoggedIn && isset($_SESSION['usuario']['nombre'])): ?>
            <p style="text-align: center; font-size: 1.2rem; margin-bottom: 20px;">¡Hola, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>!</p>
        <?php endif; ?>

        <div class="contact-info">
            <div class="contact-details">
                <h2>Información de Contacto</h2>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Calle Principal 123, Ciudad</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <p>+34 618491819</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <p>info@mgames.com</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <p>Lunes a Viernes: 9:00 - 20:00</p>
                    <p>Sábados: 10:00 - 14:00</p>
                </div>
            </div>

            <form class="contact-form" method="POST" action="procesar_contacto.php">
                <h2>Envíanos un Mensaje</h2>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" required></textarea>
                </div>
                <button type="submit" class="btn">Enviar Mensaje</button>
            </form>
        </div>
    </div>
</div>

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

