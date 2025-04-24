<?php
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
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
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
  color: var(--light-color);
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

.btn {
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

.btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
}

.btn:active {
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
        
        <div class="contact-info">
            <div class="contact-details">
                <h2>Información de Contacto</h2>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Calle Principal 123, Ciudad</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <p>+34 123 456 789</p>
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

<!-- Footer -->
<link rel="stylesheet" href="css/footer.css"> <!-- Asegúrate de que esta ruta sea correcta -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="FotosWeb/logo.png" alt="MGames Logo">
                <h3>MGames</h3>
            </div>
            <div class="footer-links">
                <h4>Enlaces rápidos</h4>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="todos_productos.php">Tienda</a></li>
                    <li><a href="novedades.php">Novedades</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>Contacto</h4>
                <p><i class="fas fa-map-marker-alt"></i> Calle Principal 123, Ciudad</p>
                <p><i class="fas fa-phone"></i> +34 123 456 789</p>
                <p><i class="fas fa-envelope"></i> info@mgames.com</p>
            </div>
            <div class="footer-social">
                <h4>Síguenos</h4>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MGames. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>
