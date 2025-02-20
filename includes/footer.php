<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>Sobre MGames</h4>
            <p>Tu tienda de confianza para videojuegos nuevos y de segunda mano.</p>
        </div>
        <div class="footer-section">
            <h4>Enlaces Útiles</h4>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="segunda_mano.php">Segunda Mano</a></li>
                <li><a href="soporte.php">Soporte</a></li>
                <li><a href="contacto.php">Contacto</a></li>
                <?php if(isset($_SESSION['usuario'])): ?>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                    <li><a href="carrito.php">Carrito</a></li>
                    <?php if ($_SESSION['usuario']['rol'] === 'ADMIN'): ?>
                        <li><a href="panel_admin.php">Panel Admin</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <li><a href="register.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Síguenos</h4>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-section">
            <h4>Contacto</h4>
            <ul>
                <li><i class="fas fa-phone"></i> +34 123 456 789</li>
                <li><i class="fas fa-envelope"></i> info@mgames.com</li>
                <li><i class="fas fa-map-marker-alt"></i> Calle Principal 123, Ciudad</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 MGames. Todos los derechos reservados.</p>
    </div>
</footer>
<link rel="stylesheet" href="css/footer.css">
<link rel="stylesheet" href="css/panel_admin.css"> 