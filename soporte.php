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

<?php require_once 'includes/footer.php'; ?> 