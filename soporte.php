<?php
$titulo = "Soporte - MGames";
require_once 'includes/header.php';
?>

<div class="content">
    <div class="support-container">
        <h1>Centro de Soporte</h1>
        
        <section class="faq-section">
            <h2>Preguntas Frecuentes</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>¿Cómo realizo una compra?</h3>
                    <p>Para realizar una compra, simplemente selecciona el producto que deseas, añádelo al carrito y sigue el proceso de checkout.</p>
                </div>
                <div class="faq-item">
                    <h3>¿Cuáles son los métodos de pago?</h3>
                    <p>Aceptamos tarjetas de crédito/débito, PayPal y transferencia bancaria.</p>
                </div>
                <!-- Más preguntas frecuentes -->
            </div>
        </section>

        <section class="contact-support">
            <h2>Contacta con Soporte</h2>
            <form class="support-form" method="POST" action="procesar_soporte.php">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="asunto">Asunto:</label>
                    <input type="text" id="asunto" name="asunto" required>
                </div>
                <div class="form-group">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" required></textarea>
                </div>
                <button type="submit" class="btn">Enviar Mensaje</button>
            </form>
        </section>
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