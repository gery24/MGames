<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario es admin para añadir la clase 'admin' al body
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
$bodyClass = $isAdmin ? 'admin' : '';

// Título de la página
$titulo = "Preguntas Frecuentes - MGames";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body class="<?php echo $bodyClass; ?>">
    <?php require_once 'includes/header.php'; ?>
    
    <div class="content">
        <!-- Hero Section para FAQs -->
        <header class="faqs-hero">
            <div class="container">
                <h1>Preguntas Frecuentes</h1>
                <p>Encuentra respuestas a las dudas más comunes sobre nuestros productos y servicios.</p>
            </div>
        </header>

        <!-- Intro Section -->
        <section class="intro-section">
            <div class="container">
                <div class="intro-content">
                    <div class="intro-image">
                        <img src="FotosWeb/logo.png" alt="MGames Logo">
                    </div>
                    <div class="intro-text">
                        <h2>¿Cómo podemos ayudarte?</h2>
                        <p>En MGames nos preocupamos por brindarte la mejor experiencia. Hemos recopilado las preguntas más frecuentes para resolver tus dudas de manera rápida y sencilla.</p>
                        <p>Si no encuentras la respuesta que buscas, no dudes en contactarnos directamente a través de nuestro formulario de contacto.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Categories -->
        <section class="categories-section">
            <div class="container">
                <h2 class="section-title-centered" style="text-align: center !important; display: block; width: 100%;">Categorías de Preguntas</h2>
                <div class="categories-grid">
                    <!-- Categoría 1: General -->
                    <div class="category-card" data-category="general">
                        <div class="card-icon">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <h3>Preguntas Generales</h3>
                        <p>Información general sobre nuestra tienda, horarios y servicios.</p>
                    </div>

                    <!-- Categoría 2: Compras -->
                    <div class="category-card" data-category="compras">
                        <div class="card-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>Compras</h3>
                        <p>Todo sobre el proceso de compra, stock y disponibilidad de productos.</p>
                    </div>

                    <!-- Categoría 3: Pagos -->
                    <div class="category-card" data-category="pagos">
                        <div class="card-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>Pagos</h3>
                        <p>Métodos de pago, facturación y preguntas sobre transacciones.</p>
                    </div>

                    <!-- Categoría 4: Envíos -->
                    <div class="category-card" data-category="envios">
                        <div class="card-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3>Envíos</h3>
                        <p>Información sobre tiempos de entrega, seguimiento y costos de envío.</p>
                    </div>

                    <!-- Categoría 5: Devoluciones -->
                    <div class="category-card" data-category="devoluciones">
                        <div class="card-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <h3>Devoluciones</h3>
                        <p>Política de devoluciones, garantías y cambios de productos.</p>
                    </div>

                    <!-- Categoría 6: Cuenta y Privacidad -->
                    <div class="category-card" data-category="cuenta">
                        <div class="card-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Cuenta y Privacidad</h3>
                        <p>Información sobre tu cuenta, datos personales y privacidad.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Accordion -->
        <section class="faqs-section">
            <div class="container">
                <h2 class="section-title-centered" style="text-align: center !important; display: block; width: 100%;">Preguntas Frecuentes</h2>
                
                <div class="faqs-container">
                    <!-- General FAQs -->
                    <div class="faq-category" id="general">
                        <h3>Preguntas Generales</h3>
                        <div class="faq-list">
                            <!-- Question 1 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cuáles son los horarios de atención de MGames?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Nuestro horario de atención en tienda física es de lunes a viernes de 10:00 a 20:00 horas y sábados de 10:00 a 14:00 horas. Nuestra tienda online está disponible las 24 horas, los 7 días de la semana, y nuestro servicio de atención al cliente online opera de lunes a viernes de 9:00 a 18:00 horas.</p>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Dónde están ubicadas las tiendas físicas de MGames?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Actualmente contamos con nuestra tienda principal en Barcelona, en la Avinguda Meridiana, 263, Sant Andreu, 08027 Barcelona. Próximamente abriremos nuevas sucursales en Madrid y Valencia. Puedes consultar la información detallada y mapas en nuestra sección de "Tiendas".</p>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puedo contactar con el servicio de atención al cliente?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Puedes contactarnos a través de varios canales:</p>
                                    <ul>
                                        <li>Teléfono: +34 618491819 de lunes a viernes de 9:00 a 18:00</li>
                                        <li>Email: atencion@mgames.com</li>
                                        <li>Formulario de contacto en nuestra web</li>
                                        <li>Chat en vivo durante el horario de atención</li>
                                        <li>Redes sociales: @MGamesOficial</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿MGames ofrece servicio de reparación de consolas?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>En este momento no ofrecemos servicio de reparación de consolas directamente. Estamos trabajando para poder ofrecer este servicio en el futuro y ampliar nuestra gama de asistencias técnicas para cubrir todas vuestras necesidades. Os mantendremos informados a través de nuestra web y redes sociales cuando este servicio esté disponible. Mientras tanto, para cualquier otro tipo de soporte técnico relacionado con nuestros productos, no dudéis en contactarnos.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compras FAQs -->
                    <div class="faq-category" id="compras">
                        <h3>Compras</h3>
                        <div class="faq-list">
                            <!-- Question 1 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puedo saber si un producto está disponible?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>En nuestra tienda online, cada producto muestra su disponibilidad en tiempo real. Si un producto está en stock, verás la opción "Añadir al carrito". Si está agotado, aparecerá como "Agotado" o "Próximamente". También puedes consultar la disponibilidad en tienda física desde la ficha del producto o contactando directamente con nosotros.</p>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Puedo reservar productos que aún no han salido al mercado?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Sí, ofrecemos sistema de reservas para juegos y consolas de próximo lanzamiento. Las reservas tienen prioridad de entrega el día del lanzamiento y en muchos casos incluyen contenido exclusivo o regalos especiales.</p>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Tienen servicio de compra online y recogida en tienda?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>En este momento, solamente ofrecemos envíos a domicilio. Estamos trabajando para implementar opciones de recogida en tienda física en el futuro. Por favor, revisa nuestra página de Envíos para más detalles sobre las opciones de entrega a domicilio disponibles.</p>
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Venden productos de segunda mano?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Sí, disponemos de una sección de productos de segunda mano revisados y garantizados. Todos nuestros productos usados pasan por un riguroso control de calidad y se clasifican según su estado. Ofrecemos garantía de 6 meses en todos los productos de segunda mano.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagos FAQs -->
                    <div class="faq-category" id="pagos">
                        <h3>Pagos</h3>
                        <div class="faq-list">
                            <!-- Question 1 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Qué métodos de pago aceptan?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Aceptamos los siguientes métodos de pago:</p>
                                    <ul>
                                        <li>Tarjetas de crédito y débito (Visa, Mastercard, American Express)</li>
                                        <li>PayPal</li>
                                        <li>Transferencia bancaria</li>
                                        <li>Pago contra reembolso (con recargo adicional)</li>
                                        <li>Financiación en compras superiores a 100€ (sujeto a aprobación)</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Es seguro comprar en su tienda online?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Absolutamente. Nuestra tienda online utiliza certificados SSL de 256 bits para encriptar toda la información sensible. Además, no almacenamos datos de tarjetas de crédito en nuestros servidores. Cumplimos con todas las normativas de protección de datos (RGPD) y contamos con sellos de confianza online como Confianza Online y Trusted Shops.</p>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Ofrecen financiación para compras?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Sí, ofrecemos opciones de financiación para compras superiores a 100€. Puedes financiar tu compra hasta en 12 meses sin intereses (sujeto a aprobación crediticia). Para compras mayores, disponemos de planes de financiación de hasta 24 meses. El proceso de solicitud es rápido y puedes realizarlo online o en tienda.</p>
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puedo obtener la factura de mi compra?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Para compras online, la factura se envía automáticamente al email registrado una vez completado el pedido. También puedes descargarla desde tu área de cliente en cualquier momento. Para compras en tienda física, te entregamos la factura en el momento de la compra. Si necesitas una copia o una factura a nombre de empresa, puedes solicitarla a través de nuestro servicio de atención al cliente.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Envíos FAQs -->
                    <div class="faq-category" id="envios">
                        <h3>Envíos</h3>
                        <div class="faq-list">
                            <!-- Question 1 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cuánto tiempo tarda en llegar mi pedido?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Los tiempos de entrega dependen del tipo de envío seleccionado:</p>
                                    <ul>
                                        <li>Envío estándar: 2-4 días laborables</li>
                                        <li>Envío express: 24-48 horas laborables</li>
                                        <li>Envío premium: Entrega en el mismo día (solo disponible en algunas ciudades y para pedidos realizados antes de las 14:00)</li>
                                    </ul>
                                    <p>Estos plazos son para envíos a península. Para Baleares, Canarias, Ceuta y Melilla, los plazos pueden ser mayores.</p>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cuál es el coste de envío?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Los costes de envío varían según el destino y el tipo de envío:</p>
                                    <ul>
                                        <li>Envío estándar: 3,95€ (gratis en pedidos superiores a 50€)</li>
                                        <li>Envío express: 6,95€ (gratis en pedidos superiores a 100€)</li>
                                        <li>Envío premium (mismo día): 9,95€</li>
                                        <li>Recogida en tienda: Gratis</li>
                                    </ul>
                                    <p>Para envíos a Baleares, Canarias, Ceuta y Melilla, consulta las tarifas específicas durante el proceso de compra.</p>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puedo hacer seguimiento de mi pedido?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Una vez que tu pedido ha sido enviado, recibirás un email con el número de seguimiento y un enlace para rastrear tu envío en tiempo real. También puedes consultar el estado de tu pedido en tu área de cliente, en la sección "Mis pedidos". Si tienes la app de MGames, recibirás notificaciones con las actualizaciones del estado de tu envío.</p>
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Realizan envíos internacionales?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Sí, realizamos envíos a todos los países de la Unión Europea, así como a otros países seleccionados. Los costes y tiempos de entrega varían según el destino. Durante el proceso de compra, una vez introducida la dirección de envío, podrás ver las opciones disponibles para tu país y sus costes asociados. Ten en cuenta que para envíos fuera de la UE pueden aplicarse tasas aduaneras que corren a cargo del cliente.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Devoluciones FAQs -->
                    <div class="faq-category" id="devoluciones">
                        <h3>Devoluciones</h3>
                        <div class="faq-list">
                            <!-- Question 1 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cuál es la política de devoluciones?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Puedes devolver cualquier producto en un plazo de 30 días desde la recepción si no estás satisfecho. El producto debe estar en su embalaje original y en perfecto estado. Para productos digitales o códigos de descarga, solo aceptamos devoluciones si el código no ha sido utilizado. Los gastos de devolución corren a cargo del cliente, excepto en caso de productos defectuosos o error en el envío.</p>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puedo iniciar una devolución?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Para iniciar una devolución, sigue estos pasos:</p>
                                    <ol>
                                        <li>Accede a tu área de cliente y selecciona el pedido que deseas devolver</li>
                                        <li>Selecciona los productos a devolver y el motivo de la devolución</li>
                                        <li>Elige entre devolución del importe o cambio por otro producto</li>
                                        <li>Imprime la etiqueta de devolución y adjúntala al paquete</li>
                                        <li>Lleva el paquete a la oficina de correos o punto de recogida indicado</li>
                                    </ol>
                                    <p>También puedes iniciar la devolución en cualquiera de nuestras tiendas físicas presentando el ticket o factura de compra.</p>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cuánto tiempo tarda en procesarse el reembolso?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Una vez recibido el producto devuelto en nuestro almacén, verificamos su estado y procesamos el reembolso en un plazo de 3-5 días laborables. El tiempo que tarda en reflejarse el dinero en tu cuenta depende de tu entidad bancaria, pero generalmente es de 3-7 días adicionales. Para devoluciones en tienda física, el reembolso es inmediato si el pago se realizó con tarjeta o en efectivo.</p>
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Qué garantía ofrecen en sus productos?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Todos nuestros productos nuevos cuentan con la garantía legal de 2 años establecida por la normativa europea. Los productos de segunda mano tienen una garantía de 6 meses. En caso de defecto o mal funcionamiento dentro del periodo de garantía, puedes solicitar la reparación, sustitución o, si no fuera posible, el reembolso del producto. Algunos fabricantes ofrecen garantías comerciales adicionales que pueden extender este periodo.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cuenta y Privacidad FAQs -->
                    <div class="faq-category" id="cuenta">
                        <h3>Cuenta y Privacidad</h3>
                        <div class="faq-list">
                            <!-- Question 1 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puedo crear una cuenta en MGames?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Para crear una cuenta, es necesario registrarse y rellenar el formulario correspondiente.</p>
                                </div>
                            </div>

                            <!-- Question 2 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Qué beneficios tiene crear una cuenta?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Al crear una cuenta en MGames, podrás disfrutar de las siguientes ventajas como miembro:</p>
                                    <ul>
                                        <li>Realizar tus compras de forma más rápida, guardando tus datos.</li>
                                        <li>Consultar el estado y el historial de tus pedidos.</li>
                                        <li>Acumular puntos en nuestro programa de fidelización y acceder a ofertas exclusivas para miembros.</li>
                                        <li>Recibir notificaciones importantes sobre lanzamientos y el estado de tus pedidos.</li>
                                        <li>Guardar tus productos favoritos en la lista de deseos.</li>
                                    </ul>
                                    <p>Estas funcionalidades estarán disponibles una vez que inicies sesión con tu cuenta registrada.</p>
                                </div>
                            </div>

                            <!-- Question 3 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo protegen mis datos personales?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>En MGames nos tomamos muy en serio la protección de tus datos. Cumplimos estrictamente con el Reglamento General de Protección de Datos (RGPD) y aplicamos medidas de seguridad avanzadas:</p>
                                    <ul>
                                        <li>Encriptación SSL en todas las comunicaciones</li>
                                        <li>No almacenamos datos de tarjetas de crédito</li>
                                        <li>Acceso restringido a datos personales solo para personal autorizado</li>
                                        <li>Auditorías de seguridad periódicas</li>
                                        <li>Política de privacidad transparente</li>
                                    </ul>
                                    <p>Puedes consultar nuestra política de privacidad completa en el enlace del pie de página.</p>
                                </div>
                            </div>

                            <!-- Question 4 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puedo darme de baja o eliminar mi cuenta?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Si deseas eliminar tu cuenta, puedes hacerlo desde tu área de cliente:</p>
                                    <ol>
                                        <li>Accede a "Mi cuenta"</li>
                                        <li>Ve a "Configuración de la cuenta"</li>
                                        <li>Selecciona "Eliminar cuenta" al final de la página</li>
                                        <li>Confirma la acción siguiendo las instrucciones</li>
                                    </ol>
                                    <p>También puedes solicitar la eliminación de tu cuenta contactando con nuestro servicio de atención al cliente. Una vez eliminada la cuenta, todos tus datos personales serán borrados de nuestros sistemas en un plazo máximo de 30 días, de acuerdo con la normativa vigente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact CTA -->
        <section class="contact-cta">
            <div class="container">
                <h2>¿No has encontrado la respuesta que buscabas?</h2>
                <p>Nuestro equipo de atención al cliente está disponible para resolver todas tus dudas.</p>
                <div class="cta-buttons">
                    <a href="contacto.php" class="btn btn-primary">Contactar</a>
                    <a href="tienda.php" class="btn btn-outline">Nuestras Tiendas</a>
                </div>
            </div>
        </section>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <!-- Botón scroll arriba -->
    <button id="scrollToTopBtn" aria-label="Volver arriba">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
        <polyline points="18 15 12 9 6 15"></polyline>
      </svg>
    </button>

    <style>
        /* Estilos generales */
        .section-title-centered {
            text-align: center !important; /* Forzar centrado */
            width: 100%; /* Asegurar que ocupe el ancho completo para que text-align funcione */
            display: block; /* Asegurar comportamiento de bloque */
        }

        /* Estilos específicos para la página FAQs */
        .faqs-hero {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            background-size: cover;
            color: #fff;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 50px;
        }
        
        .faqs-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .faqs-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Intro Section */
        .intro-section {
            padding: 4rem 0;
            background-color: var(--bg-white);
            border-bottom: 1px solid #e5e7eb;
        }
        
        .intro-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .intro-image {
            width: 150px;
            height: 150px;
            margin-bottom: 2rem;
            overflow: hidden;
            border-radius: 50%;
        }
        
        .intro-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .intro-text {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .intro-text h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--text-color);
            position: relative;
        }
        
        .intro-text h2:after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        
        body.admin .intro-text h2:after {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .intro-text p {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }

        /* Categories Section */
        .categories-section {
            padding: 4rem 0;
            background-color: var(--bg-light);
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 4rem;
        }
        
        /* Asegurar que el contenedor apila el título y la cuadrícula verticalmente */
        .categories-section .container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrar el contenido horizontalmente dentro del flex container */
        }

        .categories-section h2 {
             /* Estos estilos ahora se manejan con .section-title-centered */
             /* Asegurar que no haya float o alineación que interfiera */
             float: none;
             text-align: center; /* Mantener por si acaso, aunque !important en la clase debería bastar */
             margin: 0 auto 2rem auto; /* Centrar el bloque si es necesario y mantener margen inferior */
             width: 100%; /* Asegurar ancho si no lo hace .section-title-centered */
             display: block; /* Asegurar display si no lo hace .section-title-centered */
        }
        
        .categories-section h2:after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        
        body.admin .categories-section h2:after {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .category-card {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .category-card.active {
            border-color: var(--primary-color);
        }
        
        body.admin .category-card.active {
            border-color: var(--admin-color);
        }
        
        .card-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        body.admin .card-icon {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .card-icon i {
            font-size: 1.8rem;
            color: white;
        }
        
        .category-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }
        
        .category-card p {
            line-height: 1.6;
            color: var(--text-light);
        }

        /* FAQs Section */
        .faqs-section {
            padding: 4rem 0;
            background-color: var(--bg-white);
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 4rem;
        }
        
        /* Asegurar que el contenedor apila el título y el acordeón verticalmente */
        .faqs-section .container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrar el contenido horizontalmente dentro del flex container */
        }

        .faqs-section h2 {
            /* Estos estilos ahora se manejan con .section-title-centered */
             /* Asegurar que no haya float o alineación que interfiera */
             float: none;
             text-align: center; /* Mantener por si acaso */
             margin: 0 auto 2rem auto; /* Centrar el bloque y mantener margen */
             width: 100%; /* Asegurar ancho */
             display: block; /* Asegurar display */
        }
        
        .faqs-section h2:after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        
        body.admin .faqs-section h2:after {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .faqs-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-category {
            margin-bottom: 3rem;
            display: none;
        }
        
        .faq-category.active {
            display: block;
        }
        
        .faq-category h3 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }
        
        body.admin .faq-category h3 {
            color: var(--admin-color);
        }
        
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .faq-item {
            border: 1px solid #e5e7eb;
            border-radius: var(--radius);
            overflow: hidden;
        }
        
        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background-color: var(--bg-white);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .faq-question:hover {
            background-color: var(--bg-light);
        }
        
        .faq-question span {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .faq-question i {
            color: var(--primary-color);
            transition: transform 0.3s ease;
        }
        
        body.admin .faq-question i {
            color: var(--admin-color);
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            background-color: var(--bg-light);
        }
        
        .faq-item.active .faq-answer {
            padding: 1.25rem;
            max-height: 1000px;
            border-top: 1px solid #e5e7eb;
        }
        
        .faq-answer p {
            margin-bottom: 1rem;
            line-height: 1.7;
            color: var(--text-color);
        }
        
        .faq-answer p:last-child {
            margin-bottom: 0;
        }
        
        .faq-answer ul, .faq-answer ol {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }
        
        .faq-answer ul li, .faq-answer ol li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
            color: var(--text-color);
        }

        /* Contact CTA */
        .contact-cta {
            padding: 4rem 0;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
        }
        
        body.admin .contact-cta {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .contact-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: white;
            position: relative;
        }
        
        .contact-cta h2:after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: white;
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        
        .contact-cta p {
            max-width: 700px;
            margin: 0 auto 2rem;
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .contact-cta .btn-primary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 10px 23px;
            border-radius: var(--radius);
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
        }
        
        .contact-cta .btn-primary:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .contact-cta .btn-outline {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 10px 23px;
            border-radius: var(--radius);
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            display: inline-block;
        }
        
        .contact-cta .btn-outline:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Botón scroll arriba */
        #scrollToTopBtn {
            display: none;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            width: 50px;
            height: 50px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        body.admin #scrollToTopBtn {
             background-color: #0d6efd;
        }

        #scrollToTopBtn:hover {
            background-color: #0b5ed7;
            transform: scale(1.1);
        }

        body.admin #scrollToTopBtn:hover {
            background-color: #0b5ed7;
        }

        #scrollToTopBtn svg {
            width: 24px;
            height: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .faqs-hero h1 {
                font-size: 2.5rem;
            }
            
            .faqs-hero p {
                font-size: 1.1rem;
            }
            
            .intro-text h2, 
            .categories-section h2, 
            .faqs-section h2 {
                font-size: 1.8rem;
            }
            
            .category-card {
                padding: 1.5rem;
            }
            
            .card-icon {
                width: 60px;
                height: 60px;
            }
            
            .card-icon i {
                font-size: 1.5rem;
            }
            
            .category-card h3 {
                font-size: 1.3rem;
            }
            
            .faq-category h3 {
                font-size: 1.6rem;
            }
            
            .faq-question span {
                font-size: 1rem;
            }
            
            .contact-cta h2 {
                font-size: 2rem;
            }
            
            .contact-cta p {
                font-size: 1.1rem;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 576px) {
            .faqs-hero {
                padding: 4rem 0;
            }
            
            .faqs-hero h1 {
                font-size: 2rem;
            }
            
            .intro-text h2, 
            .categories-section h2, 
            .faqs-section h2 {
                font-size: 1.6rem;
            }
            
            .intro-text h2:after, 
            .categories-section h2:after, 
            .faqs-section h2:after {
                width: 40px;
                margin-top: 0.4rem;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .faq-category h3 {
                font-size: 1.4rem;
            }
            
            .contact-cta h2 {
                font-size: 1.8rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
            }
            
            .cta-buttons .btn-primary,
            .cta-buttons .btn-outline {
                width: 100%;
            }
        }
    </style>

    <script>
        // Script para el acordeón de preguntas y categorías
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar la primera categoría por defecto
            document.getElementById('general').classList.add('active');
            document.querySelector('.category-card[data-category="general"]').classList.add('active');
            
            // Manejar clics en las categorías
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Desactivar todas las categorías
                    categoryCards.forEach(c => c.classList.remove('active'));
                    document.querySelectorAll('.faq-category').forEach(fc => fc.classList.remove('active'));
                    
                    // Activar la categoría seleccionada
                    this.classList.add('active');
                    document.getElementById(category).classList.add('active');
                    
                    // Cerrar todas las preguntas abiertas
                    document.querySelectorAll('.faq-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Scroll suave a la sección de preguntas
                    document.querySelector('.faqs-section').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });
            
            // Manejar clics en las preguntas
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const faqItem = this.parentElement;
                    
                    // Toggle active class
                    faqItem.classList.toggle('active');
                });
            });
            
            // Botón de volver arriba
            const scrollBtn = document.getElementById('scrollToTopBtn');
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollBtn.style.display = 'flex';
                } else {
                    scrollBtn.style.display = 'none';
                }
            });
            
            scrollBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>