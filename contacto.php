<?php
$titulo = "Contacto - MGames";
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="css/styles.css"> <!-- CSS general -->
<link rel="stylesheet" href="css/contacto.css"> <!-- CSS específico para contacto -->

<style>
/* Estilos para la página de contacto */
.content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.contact-container {
  background-color: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 50px;
}

.contact-container h1 {
  text-align: center;
  color: #7e22ce;
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
  background: linear-gradient(to right, #7e22ce, #a29bfe);
  border-radius: 2px;
}

.contact-container .contact-info {
  display: flex;
  flex-direction: column;
  gap: 30px;
  padding: 20px;
}

@media (min-width: 768px) {
  .contact-container .contact-info {
    flex-direction: row;
  }
}

.contact-container .contact-details.contact-info-section,
.contact-container .contact-form {
  flex: 1;
  padding: 30px;
  border-radius: 10px;
}

.contact-container .contact-details.contact-info-section {
  background: linear-gradient(135deg, #7e22ce, #a29bfe);
  color: white;
  position: relative;
  overflow: hidden;
}

.contact-container .contact-details.contact-info-section:before {
  content: "";
  position: absolute;
  top: -50px;
  right: -50px;
  width: 100px;
  height: 100px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
}

.contact-container .contact-details.contact-info-section h2,
.contact-container .contact-form h2 {
  margin-bottom: 25px;
  font-size: 1.8rem;
  position: relative;
  display: inline-block;
}

.contact-container .contact-details.contact-info-section h2:after,
.contact-container .contact-form h2:after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 40px;
  height: 3px;
  background-color: #fd79a8;
  border-radius: 2px;
}

.contact-container .info-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;
  transition: all 0.3s ease;
}

.contact-container .info-item:hover {
  transform: translateX(5px);
}

.contact-container .info-item i {
  font-size: 1.2rem;
  margin-right: 15px;
  margin-top: 5px;
  color: #f5f6fa;
}

.contact-container .info-item p {
  margin: 0;
  line-height: 1.6;
}

.contact-container .contact-form {
  background-color: #f5f6fa;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.contact-container .form-group {
  margin-bottom: 20px;
}

.contact-container .form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #2d3436;
}

.contact-container .form-group input,
.contact-container .form-group textarea {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: white;
}

.contact-container .form-group input:focus,
.contact-container .form-group textarea:focus {
  border-color: #7e22ce;
  outline: none;
  box-shadow: 0 0 0 3px rgba(126, 34, 206, 0.2);
}

.contact-container .form-group textarea {
  min-height: 150px;
  resize: vertical;
}

.contact-container .btn {
  background: linear-gradient(to right, #7e22ce, #a29bfe);
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-block;
  text-transform: uppercase;
  letter-spacing: 1px;
  box-shadow: 0 4px 10px rgba(126, 34, 206, 0.3);
}

.contact-container .btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(126, 34, 206, 0.4);
}

.contact-container .btn:active {
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

.contact-container .info-item:nth-child(1) {
  animation: fadeIn 0.5s ease-out 0.2s both;
}
.contact-container .info-item:nth-child(2) {
  animation: fadeIn 0.5s ease-out 0.4s both;
}
.contact-container .info-item:nth-child(3) {
  animation: fadeIn 0.5s ease-out 0.6s both;
}
.contact-container .info-item:nth-child(4) {
  animation: fadeIn 0.5s ease-out 0.8s both;
}

/* Responsive adjustments */
@media (max-width: 767px) {
  .contact-container .contact-details.contact-info-section,
  .contact-container .contact-form {
    padding: 20px;
  }

  .contact-container h1 {
    font-size: 2rem;
  }

  .contact-container .contact-details.contact-info-section h2,
  .contact-container .contact-form h2 {
    font-size: 1.5rem;
  }

  .contact-container .btn {
    width: 100%;
  }
}
</style>

<div class="content">
    <div class="contact-container">
        <h1>Contacto</h1>
        
        <div class="contact-info">
            <div class="contact-details contact-info-section">
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

