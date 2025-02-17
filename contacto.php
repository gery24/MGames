<?php
$titulo = "Contacto - MGames";
require_once 'includes/header.php';
?>

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

<?php require_once 'includes/footer.php'; ?> 