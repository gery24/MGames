<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario es admin para añadir la clase 'admin' al body
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
$bodyClass = $isAdmin ? 'admin' : '';

// Título de la página
$titulo = "Nosotros - MGames";
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
        <!-- Hero Section para Nosotros -->
        <header class="about-hero">
            <div class="container">
                <h1>Nosotros</h1>
                <p>Conoce al equipo detrás de MGames, tu tienda de videojuegos favorita</p>
            </div>
        </header>

        <!-- Quiénes Somos -->
        <section class="about-section">
            <div class="container">
                <h2>Quiénes Somos</h2>
                <div class="about-content">
                    <div class="about-image">
                        <img src="FotosWeb/logo.png" alt="Tienda MGames">
                    </div>
                    <div class="about-text">
                        <p>MGames es una empresa líder en la venta de videojuegos, fundada por un grupo de apasionados gamers que decidieron convertir su pasión en un negocio. Desde nuestros inicios, nos hemos dedicado a ofrecer los mejores títulos del mercado, con un servicio personalizado y experto que nos distingue de la competencia.</p>
                        <p>Con más de 10 años de experiencia en el sector, entendemos las necesidades de los jugadores y trabajamos constantemente para satisfacerlas, ofreciendo no solo productos de calidad sino también una experiencia de compra única.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Misión y Visión -->
        <section class="mission-vision-section">
            <div class="container">
                <div class="mission-vision-container">
                    <div class="mission-card">
                        <div class="card-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Nuestra Misión</h3>
                        <p>Proporcionar a los gamers de todos los niveles acceso a los mejores videojuegos y accesorios del mercado, con un asesoramiento experto y personalizado, fomentando la cultura gamer y creando una comunidad unida por la pasión por los videojuegos.</p>
                    </div>
                    <div class="vision-card">
                        <div class="card-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Nuestra Visión</h3>
                        <p>Ser reconocidos como la tienda de videojuegos de referencia a nivel nacional, destacando por nuestra atención al cliente, variedad de productos y contribución a la comunidad gamer, adaptándonos constantemente a las nuevas tendencias y tecnologías del sector.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nuestros Valores -->
        <section class="values-section">
            <div class="container">
                <h2>Nuestros Valores</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Pasión</h3>
                        <p>Amamos los videojuegos tanto como tú. Nuestra pasión nos impulsa a ofrecer siempre lo mejor.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3>Compromiso</h3>
                        <p>Nos comprometemos a satisfacer las necesidades de nuestros clientes, ofreciendo un servicio de calidad.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Comunidad</h3>
                        <p>Creemos en el poder de la comunidad gamer y trabajamos para fortalecerla día a día.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3>Innovación</h3>
                        <p>Nos adaptamos constantemente a las nuevas tendencias y tecnologías del sector.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nuestro Equipo -->
        <section class="team-section">
            <div class="container">
                <h2>Nuestro Equipo</h2>
                <p class="team-intro">Detrás de MGames hay un equipo de profesionales apasionados por los videojuegos, comprometidos con ofrecer el mejor servicio y asesoramiento a nuestros clientes.</p>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="member-image">
                            <img src="FotosWeb/Mure.jpg" alt="CEO de MGames">
                        </div>
                        <h3>Miguel Mure</h3>
                        <p class="member-role">Fundador y Diseñador Visual</p>
                        <p class="member-desc">Apasionado gamer desde los 5 años, Miguel fundó MGames con la ayuda de Gerard, con la visión de crear una comunidad donde todos los jugadores se sintieran como en casa.</p>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-member">
                        <div class="member-image">
                            <img src="FotosWeb/Gerard.png" alt="CTO de MGames">
                        </div>
                        <h3>Gerard Romero</h3>
                        <p class="member-role">Fundador y Director tecnológico</p>
                        <p class="member-desc">Ingeniero informático y gamer de corazón, Gerard lidera nuestro departamento tecnológico asegurando una experiencia de compra online impecable.</p>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Nuestra Historia -->
        <section class="history-section">
            <div class="container">
                <h2>Nuestra Historia</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">2023</div>
                        <div class="timeline-content">
                            <h3>Los inicios</h3>
                            <p>Fundación de MGames en Barcelona, un proyecto nacido de la pasión por los videojuegos con el objetivo de crear una comunidad única.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">2024</div>
                        <div class="timeline-content">
                            <h3>Los Pilares del exito</h3>
                            <p>Definición de nuestra identidad y valores. Desarrollo de nuestra plataforma online con una amplia visión de futuro y experiencia de usuario.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-date">2025</div>
                        <div class="timeline-content">
                            <h3>Expansión total</h3>
                            <p>Lanzamiento oficial de nuestra tienda online y consolidación en el mercado nacional, ampliando nuestro catálogo y nuestra comunidad de jugadores.</p>
                        </div>
                    </div>
                    
            </div>
        </section>

        <!-- Testimonios -->
        <section class="testimonials-section">
            <div class="container">
                <h2>Lo que dicen nuestros clientes</h2>
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>MGames ha sido mi tienda de confianza durante años. Su catálogo es impresionante y el servicio al cliente es inmejorable. ¡Siempre encuentro lo que busco.<br>Me declaro TOTALMENTE FAN DE LOS FUNDADORES!</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-image">
                                <img src="FotosWeb/julian.png" alt="Cliente">
                            </div>
                            <div class="author-info">
                                <h4>Julian Fortuño</h4>
                                <p>Cliente desde 2024</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>Los eventos y torneos que organiza MGames son geniales. He conocido a muchos amigos gracias a ellos y me han permitido disfrutar aún más de mi afición, que ni yo sabia que tenia antes de conocer a los fundadores.</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-image">
                                <img src="FotosWeb/marta.jpg" alt="Cliente">
                            </div>
                            <div class="author-info">
                                <h4>Marta Carbonell</h4>
                                <p>Cliente desde 2025</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>Lo que más valoro de MGames es su asesoramiento personalizado. Siempre me recomiendan juegos que acaban convirtiéndose en mis favoritos.</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-image">
                                <img src="FotosWeb/victor.jpg" alt="Cliente">
                            </div>
                            <div class="author-info">
                                <h4>Victor Martin</h4>
                                <p>Cliente desde 2024</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contacto CTA -->
        <section class="contact-cta">
            <div class="container">
                <h2 style="color: white;">¿Quieres formar parte de nuestra historia?</h2>
                <p>Si tienes alguna pregunta o quieres saber más sobre MGames, no dudes en contactarnos.</p>
                <div class="cta-buttons">
                    <a href="contacto.php" class="btn btn-primary">Contactar</a>
                    <a href="tienda.php" class="btn btn-outline">Nuestra Tiendas</a>
                </div>
            </div>
        </section>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <style>
        /* Estilos específicos para la página Nosotros */
        .about-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('FotosWeb/about-hero.jpg') no-repeat center center;
            background-size: cover;
            color: #fff;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 50px;
        }
        
        .about-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .about-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .about-section,
        .values-section,
        .history-section {
            padding: 4rem 0;
            background-color: var(--bg-white);
        }

        .mission-vision-section,
        .team-section,
        .testimonials-section {
            padding: 4rem 0;
            background-color: var(--bg-light);
        }
        
        .about-content {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 2rem;
            flex-direction: column;
        }
        
        .about-text {
            flex: none;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
        }
        
        .about-text p {
            margin-bottom: 1.5rem;
        }
        
        .about-image {
            flex: none;
            width: 150px;
            height: auto;
            max-width: 100%;
            margin: 0 auto 2rem;
        }
        
        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        
        .about-section h2, .mission-vision-section h2, .values-section h2, 
        .team-section h2, .history-section h2, .testimonials-section h2,
        .contact-cta h2
        {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--text-color);
            text-align: center;
            position: relative;
        }
        
        .about-section h2:after, .mission-vision-section h2:after, .values-section h2:after, 
        .team-section h2:after, .history-section h2:after, .testimonials-section h2:after,
        .contact-cta h2:after
        {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        
        body.admin .about-section h2:after, 
        body.admin .mission-vision-section h2:after, 
        body.admin .values-section h2:after, 
        body.admin .team-section h2:after, 
        body.admin .history-section h2:after, 
        body.admin .testimonials-section h2:after,
        body.admin .contact-cta h2:after
         {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .mission-vision-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .mission-card, .vision-card {
            flex: 1;
            min-width: 300px;
            max-width: 550px;
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .mission-card:hover, .vision-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
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
        
        .mission-card h3, .vision-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }
        
        .mission-card p, .vision-card p {
            line-height: 1.7;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .value-card {
            background-color: var(--card-bg);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .value-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        body.admin .value-icon {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .value-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .value-card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
            color: var(--text-color);
        }
        
        .value-card p {
            line-height: 1.6;
        }
        
        .team-intro {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 2rem;
            line-height: 1.7;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .team-member {
            background-color: var(--card-bg);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .member-image {
            height: 250px;
            overflow: hidden;
        }
        
        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .team-member:hover .member-image img {
            transform: scale(1.05);
        }
        
        .team-member h3 {
            font-size: 1.4rem;
            margin: 1.5rem 1rem 0.25rem;
            color: var(--text-color);
        }
        
        .member-role {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            padding: 0 1rem;
        }
        
        body.admin .member-role {
            color: var(--admin-color);
        }
        
        .member-desc {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .member-social {
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 0 1rem 1.5rem;
            margin-top: auto;
        }
        
        .member-social a {
            width: 35px;
            height: 35px;
            background-color: var(--bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }
        
        body.admin .member-social a {
            color: var(--admin-color);
        }
        
        .member-social a:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }
        
        body.admin .member-social a:hover {
            background-color: var(--admin-color);
        }
        
        .timeline {
            position: relative;
            max-width: 1000px;
            margin: 3rem auto 0;
            padding: 2rem 0;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 4px;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
            transform: translateX(-50%);
        }
        
        body.admin .timeline::before {
            background: linear-gradient(to bottom, var(--admin-color), var(--admin-dark));
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 3rem;
            width: 100%;
        }
        
        .timeline-dot {
            position: absolute;
            top: 1.5rem;
            left: 50%;
            width: 18px;
            height: 18px;
            background-color: var(--primary-color);
            border-radius: 50%;
            transform: translateX(-50%);
            z-index: 1;
            border: 3px solid var(--bg-white);
        }
        
        body.admin .timeline-dot {
            background-color: var(--admin-color);
            border-color: var(--admin-bg-light);
        }
        
        .timeline-date {
            position: absolute;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            z-index: 1;
            white-space: nowrap;
        }
        
        body.admin .timeline-date {
            background-color: var(--admin-color);
        }
        
        .timeline-item:nth-child(odd) .timeline-date {
            transform: translateX(-150%);
        }
        
        .timeline-item:nth-child(even) .timeline-date {
            transform: translateX(50%);
        }
        
        .timeline-content {
            width: 45%;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            line-height: 1.7;
        }
        
        .timeline-content h3 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        body.admin .timeline-content h3 {
            color: var(--admin-color);
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .testimonial-card {
            background-color: var(--card-bg);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .testimonial-content {
            padding: 2rem;
            position: relative;
            flex-grow: 1;
            line-height: 1.7;
        }
        
        .quote-icon {
            font-size: 2.5rem;
            color: var(--bg-light);
            position: absolute;
            top: 1rem;
            left: 1rem;
            opacity: 0.5;
        }
        
        .testimonial-content p {
            position: relative;
            z-index: 1;
            margin-top: 1rem;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            padding: 1.5rem 2rem;
            background-color: #f8fafc;
            margin-top: auto;
        }
        
        body.admin .testimonial-author {
            background-color: var(--admin-bg-light);
        }
        
        .author-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1rem;
        }
        
        .author-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .author-info h4 {
            font-size: 1.1rem;
            margin: 0 0 0.25rem;
            color: var(--text-color);
        }
        
        .author-info p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .contact-cta {
            padding: 4rem 0;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
        }
        
        body.admin .contact-cta {
            background: linear-gradient(to right, var(--admin-color), var(--admin-dark));
        }
        
        .contact-cta p {
            max-width: 700px;
            margin: 0 auto 2rem;
            font-size: 1.1rem;
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
        }
        
        .contact-cta .btn-outline:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2.5rem;
            }
            
            .about-content {
                flex-direction: column;
                gap: 2rem;
            }
            
            .mission-vision-container {
                flex-direction: column;
                align-items: center;
                gap: 2rem;
            }
             
            .mission-card, .vision-card {
                max-width: 400px;
                padding: 1.5rem;
            }

            .timeline::before {
                left: 20px;
            }
            
            .timeline-item:nth-child(odd) .timeline-content,
            .timeline-item:nth-child(even) .timeline-content {
                width: calc(100% - 60px);
                margin-left: 60px;
                margin-right: auto;
                text-align: left;
                padding: 1.5rem;
            }
            
            .timeline-dot {
                left: 20px;
                top: 1rem;
            }
            
            .timeline-date {
                left: 20px;
                transform: translateX(0);
                top: -2rem;
                font-size: 0.8rem;
                padding: 0.15rem 0.5rem;
            }
            
            .testimonials-grid {
                gap: 1.5rem;
            }

            .testimonial-content {
                padding: 1.5rem;
            }

            .testimonial-author {
                padding: 1rem 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .about-hero {
                padding: 4rem 0;
            }
            
            .about-hero h1 {
                font-size: 2rem;
            }
            
            .about-section h2, .mission-vision-section h2, .values-section h2, 
            .team-section h2, .history-section h2, .testimonials-section h2,
            .contact-cta h2
            {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
             .about-section h2:after, .mission-vision-section h2:after, .values-section h2:after, 
            .team-section h2:after, .history-section h2:after, .testimonials-section h2:after,
            .contact-cta h2:after
            {
                width: 40px;
                margin-top: 0.4rem;
            }
            
            .contact-cta h2 {
                font-size: 2rem;
                margin-bottom: 2rem;
                color: white !important;
                text-align: center;
                position: relative;
            }
            
            .contact-cta p {
                font-size: 1rem;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .cta-buttons .btn {
                width: 100%;
            }

            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 1.5rem;
            }
        }

        /* Equipo */
        .team-section .container {
             display: flex;
             flex-direction: column;
             align-items: center;
        }

        /* Historia */
        .history-section .container {
            /* Asegurar que el contenedor no tenga estilos que impidan que el título esté encima */
            /* Por ejemplo, si fuera flex o grid con una dirección específica */
            display: block; /* Asegurar que sea un bloque normal */
            /* Opcional: centrar el contenido si es necesario */
            /* text-align: center; */
        }

        .history-section h2 {
            margin-bottom: 2rem; /* Asegurar espacio debajo del título */
            /* Otros estilos de h2 ya definidos globalmente */
        }
        
        .timeline {
            position: relative;
        }

        /* Testimonios */
        .testimonials-section .container {
            /* Asegurar que el contenedor no tenga estilos que impidan que el título esté encima */
            display: block; /* Asegurar que sea un bloque normal */
            /* Opcional: centrar el contenido si es necesario */
            /* text-align: center; */
        }

        .testimonials-section h2 {
            margin-bottom: 2rem; /* Asegurar espacio debajo del título */
            /* Otros estilos de h2 ya definidos globalmente */
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        /* Botón scroll arriba */
        #scrollToTopBtn {
            display: none; /* Ocultar por defecto */
            position: fixed; /* Posición fija en la pantalla */
            bottom: 20px; /* Distancia desde el borde inferior */
            right: 20px; /* Distancia desde el borde derecho */
            z-index: 99; /* Asegurar que esté por encima de otros elementos */
            width: 50px; /* Tamaño del botón */
            height: 50px; /* Tamaño del botón */
            background-color: #0d6efd; /* Color azul */
            color: white; /* Color del icono */
            border: none; /* Sin borde */
            border-radius: 50%; /* Forma redonda */
            cursor: pointer; /* Cursor de mano */
            justify-content: center; /* Centrar contenido (el icono) */
            align-items: center; /* Centrar contenido (el icono) */
            box-shadow: var(--shadow);
            transition: background-color 0.3s ease, opacity 0.3s ease, transform 0.3s ease; /* Transiciones suaves */
        }

        body.admin #scrollToTopBtn {
             background-color: #0d6efd; /* Color azul también para admin */
        }

        #scrollToTopBtn:hover {
            background-color: #0b5ed7; /* Color azul más oscuro en hover */
            transform: translateY(-2px); /* Efecto de elevación al pasar el ratón */
            box-shadow: var(--hover-shadow);
        }

        body.admin #scrollToTopBtn:hover {
            background-color: #0b5ed7; /* Color azul más oscuro en hover para admin */
        }

        #scrollToTopBtn svg {
            width: 24px; /* Tamaño del icono SVG */
            height: 24px; /* Tamaño del icono SVG */
        }

        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2.5rem;
            }
            
            .about-content {
                flex-direction: column;
                gap: 2rem;
            }
            
            .mission-vision-container {
                flex-direction: column;
                align-items: center;
                gap: 2rem;
            }
             
            .mission-card, .vision-card {
                max-width: 400px;
                padding: 1.5rem;
            }

            .timeline::before {
                left: 20px;
            }
            
            .timeline-item:nth-child(odd) .timeline-content,
            .timeline-item:nth-child(even) .timeline-content {
                width: calc(100% - 60px);
                margin-left: 60px;
                margin-right: auto;
                text-align: left;
                padding: 1.5rem;
            }
            
            .timeline-dot {
                left: 20px;
                top: 1rem;
            }
            
            .timeline-date {
                left: 20px;
                transform: translateX(0);
                top: -2rem;
                font-size: 0.8rem;
                padding: 0.15rem 0.5rem;
            }
            
            .testimonials-grid {
                gap: 1.5rem;
            }

            .testimonial-content {
                padding: 1.5rem;
            }

            .testimonial-author {
                padding: 1rem 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .about-hero {
                padding: 4rem 0;
            }
            
            .about-hero h1 {
                font-size: 2rem;
            }
            
            .about-section h2, .mission-vision-section h2, .values-section h2, 
            .team-section h2, .history-section h2, .testimonials-section h2,
            .contact-cta h2
            {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
             .about-section h2:after, .mission-vision-section h2:after, .values-section h2:after, 
            .team-section h2:after, .history-section h2:after, .testimonials-section h2:after,
            .contact-cta h2:after
            {
                width: 40px;
                margin-top: 0.4rem;
            }
            
            .contact-cta h2 {
                font-size: 2rem;
                margin-bottom: 2rem;
                color: white !important;
                text-align: center;
                position: relative;
            }
            
            .contact-cta p {
                font-size: 1rem;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .cta-buttons .btn {
                width: 100%;
            }

            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 1.5rem;
            }
        }
    </style>

    <!-- Botón scroll arriba -->
    <button id="scrollToTopBtn" aria-label="Volver arriba">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
        <polyline points="18 15 12 9 6 15"></polyline>
      </svg>
    </button>

    <script>
        // Script para el botón de volver arriba
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
</body>
</html>
