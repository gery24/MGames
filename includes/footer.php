<style>
/* Reset para eliminar márgenes y paddings por defecto */
html, body {
    margin: 0;
    padding: 0;
    min-height: 100%;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* Asegura que el body ocupe al menos toda la altura de la ventana */
}

/* Hacer que el contenido principal ocupe todo el espacio disponible */
.content {
    flex: 1;
}

/* Estilos integrados para el footer */
.mgames-footer {
    background-color: #1e2530;
    color: #ffffff;
    padding: 3rem 0 1.5rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    width: 100%;
    box-sizing: border-box;
    margin-bottom: 0; /* Elimina cualquier margen inferior */
}

.mgames-footer a {
    color: #ffffff;
    text-decoration: none;
}

.mgames-footer a:hover {
    color: #4f46e5;
}

.footer-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.footer-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.footer-column {
    flex: 1;
    min-width: 200px;
    margin-bottom: 1.5rem;
    padding-right: 2rem;
}

.footer-column h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.footer-column h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background-color: #4f46e5;
}

.footer-logo {
    margin-bottom: 1rem;
}

.footer-logo img {
    height: 40px;
    margin-bottom: 1rem;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.75rem;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.contact-icon {
    margin-right: 0.5rem;
    color: #4f46e5;
    width: 16px;
    text-align: center;
}

.social-icons {
    display: flex;
    gap: 0.75rem;
}

.social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transition: all 0.3s ease;
}

.social-icon:hover {
    background-color: #4f46e5;
    transform: translateY(-3px);
}

.footer-divider {
    height: 1px;
    background-color: rgba(255, 255, 255, 0.1);
    margin-bottom: 1.5rem;
}

.footer-copyright {
    text-align: center;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 768px) {
    .footer-grid {
        flex-direction: column;
    }
    
    .footer-column {
        width: 100%;
        padding-right: 0;
    }
}
</style>

<footer class="mgames-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <!-- Logo y nombre -->
            <div class="footer-column">
                <div class="footer-logo">
                    <img src="FotosWeb/logo.png" alt="MGames Logo">
                    <h2>MGames</h2>
                </div>
            </div>
            
            <!-- Enlaces rápidos -->
            <div class="footer-column">
                <h3>Enlaces rápidos</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="tienda.php">Tienda</a></li>
                    <li><a href="segunda_mano.php">Segunda Mano</a></li>
                    <li><a href="novedades.php">Novedades</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="eventos.php">Eventos</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                    <li><a href="SobreNosotros.php">Sobre Nosotros</a></li>
                    <li><a href="Faqs.php">Preguntas Frecuentes</a></li>
                </ul>
            </div>
            
            <!-- Contacto -->
            <div class="footer-column">
                <h3>Contacto</h3>
                <div class="contact-item">
                    <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                    <span>Calle Principal 123, Ciudad</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon"><i class="fas fa-phone"></i></span>
                    <span>+34 123 456 789</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                    <span>info@mgames.com</span>
                </div>
            </div>
            
            <!-- Redes sociales -->
            <div class="footer-column">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="#" class="social-icon" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-icon" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer-divider"></div>
        
        <div class="footer-copyright">
            <p>&copy; <?php echo date('Y'); ?> MGames. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>
<script>
// Script para asegurar que el footer llegue hasta el final de la página
document.addEventListener('DOMContentLoaded', function() {
    // Función para ajustar el footer
    function adjustFooter() {
        const body = document.body;
        const html = document.documentElement;
        const footer = document.querySelector('.mgames-footer');
        
        // Obtener la altura del documento
        const docHeight = Math.max(
            body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight
        );
        
        // Obtener la posición actual del footer
        const footerTop = footer.offsetTop + footer.offsetHeight;
        
        // Si el footer no llega al final de la página, ajustar
        if (footerTop < window.innerHeight) {
            footer.style.marginTop = (window.innerHeight - footerTop) + 'px';
        }
    }
    
    // Ejecutar al cargar y al cambiar el tamaño de la ventana
    adjustFooter();
    window.addEventListener('resize', adjustFooter);
});
</script>
