<?php

// Incluir archivo de configuración de la base de datos
require_once 'config/database.php';

try {
    // Verificar la conexión
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener categorías con la columna foto
    $stmt = $pdo->query("SELECT id, nombre, foto FROM categorias");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar el número de productos por categoría
    $stmt = $pdo->query("SELECT c.id, c.nombre, COUNT(p.id) as count 
                        FROM categorias c 
                        LEFT JOIN productos p ON c.id = p.categoria_id 
                        GROUP BY c.id");
    $categorias_count = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    // Mostrar información detallada del error en un entorno de desarrollo
    // En producción, se debería registrar el error y mostrar un mensaje genérico al usuario.
    echo "Error en la base de datos: " . $e->getMessage();
    // Opcional: mostrar detalles adicionales solo si es un entorno de desarrollo
    if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') { // Ejemplo simple de verificación de entorno local
         echo "<br>SQL State: " . $e->getCode();
         // echo "<br>Query: " . $query; // Si $query estuviera definida antes del prepare/execute
    }
    die();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGames - Tienda de Videojuegos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

    <!-- Header Mejorado -->
    <header class="site-header">
        <div class="container header-container">
            <a href="index.php" class="logo">
                <span>MGames</span>
            </a>
            
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="tienda.php">Tienda</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <div class="search-container">
                    <button class="search-button" id="search-toggle">
                        <i class="fas fa-search"></i>
                    </button>
                    <form class="search-form" method="GET" action="index.php" id="search-form" style="display: none;">
                        <select name="categoria" class="filter-select">
                            <option value="">Todas las categorías</option>
                                                            <option value="1">
                                    Acción                                </option>
                                                            <option value="2">
                                    Aventura                                </option>
                                                            <option value="5">
                                    Carreras                                </option>
                                                            <option value="4">
                                    Deportes                                </option>
                                                            <option value="6">
                                    Estrategia                                </option>
                                                            <option value="9">
                                    Lucha                                </option>
                                                            <option value="3">
                                    Rol                                </option>
                                                            <option value="8">
                                    Shooter                                </option>
                                                            <option value="7">
                                    Simulación                                </option>
                                                            <option value="13">
                                    Tarjeta Nintendo                                </option>
                                                            <option value="11">
                                    Tarjeta Play                                </option>
                                                            <option value="12">
                                    Tarjeta XBOX                                </option>
                                                            <option value="10">
                                    Terror                                </option>
                                                    </select>
                    </form>
                </div>
                <a href="lista_deseos.php" class="header-icon">
                    <i class="fas fa-heart"></i>
                                    </a>
                <a href="carrito.php" class="header-icon">
                    <i class="fas fa-shopping-cart"></i>
                                    </a>
                <a href="cartera.php" class="header-icon">
                    <i class="fas fa-wallet"></i>
                                    </a>
                <div class="user-profile">
                                            <div class="auth-buttons">
                            <a href="login.php" class="btn btn-sm btn-outline">Iniciar Sesión</a>
                            <a href="register.php" class="btn btn-sm btn-primary">Registrarse</a>
                        </div>
                                    </div>
                <button id="menuToggle" class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="content">
    <!-- Hero Section con Video de Fondo -->
    <header class="hero">
        <div class="video-container">
            <video id="hero-video" autoplay loop muted playsinline>
                <source src="FotosWeb/videoplayback (1).mp4" type="video/mp4">
                Tu navegador no soporta videos HTML5.
            </video>
            <br>
            <br>
            <br>
            <div class="video-overlay"></div>
            <div class="hero-content">
                    <h1>Explora Nuestro Universo Gaming</h1>
                    <p>Descubre los mejores títulos, ofertas exclusivas y contenido premium para todos los gamers</p>
                <br>
                <div class="hero-buttons">
                        <a href="#categorias" class="btn btn-primary">Explorar Categorías</a>
                    <a href="#ofertas" class="btn btn-transparent">Ver Ofertas</a>
                    </div>
                </div>
            </div>
    </header>

        <!-- Categorías Visuales -->
        <section id="categorias" class="categorias-visuales">
            <div class="container">
                <h2 class="section-title">Categorías de Juegos</h2>
                <div class="categorias-grid">
                <?php 
                // Colores para las categorías (reutilizamos si es necesario, o definimos aquí si no existen)
                $colors = [
                    'from-red-500 to-orange-500',
                    'from-blue-500 to-indigo-500',
                    'from-green-500 to-emerald-500',
                    'from-yellow-500 to-amber-500',
                    'from-purple-500 to-pink-500',
                    'from-indigo-500 to-purple-500'
                ];
                $i = 0;

                // Generar tarjetas de categoría dinámicamente
                // Mostrar solo las primeras 3 categorías inicialmente
                $first_four_categories = array_slice($categorias, 0, 3);
                foreach($first_four_categories as $cat): 
                    // Encontrar el conteo para esta categoría desde $categorias_count
                    $current_cat_count = 0;
                     if (isset($cat['id'])) { // Asegurarse de que el ID existe antes de buscar el conteo
                        foreach($categorias_count as $cat_count_data) {
                            if (isset($cat_count_data['id']) && $cat_count_data['id'] == $cat['id']) {
                                $current_cat_count = $cat_count_data['count'];
                                break;
                            }
                        }
                    }
                    
                    $color_class = $colors[$i % count($colors)];
                    $i++;
                ?>
                    <a href="todos_productos.php?categoria=<?php echo htmlspecialchars($cat['id'] ?? ''); ?>" 
                       class="categoria-card <?php echo htmlspecialchars($color_class); ?>"
                       style="background-image: url('<?php echo htmlspecialchars($cat['foto'] ?? ''); ?>'); background-size: cover; background-position: center;">
                        <div class="categoria-overlay"></div>
                        <div class="categoria-content">
                             <?php 
                                // Mostrar el nombre de la categoría solo si no es una de las tarjetas
                                if (!in_array($cat['nombre'] ?? '', ['Tarjeta Play', 'Tarjeta XBOX', 'Tarjeta Nintendo'])) {
                                    echo '<h3>' . htmlspecialchars($cat['nombre'] ?? '') . '</h3>';
                                }
                            ?>
                            <p><?php echo $current_cat_count; ?> juegos</p>
                        </div>
                    </a>
                <?php endforeach; ?>
                <?php 
                // Las demás categorías se añadirán vía JavaScript por toggleCategories()
                ?>
                </div>
                <div class="text-center mt-6">
                    <a href="#" id="toggle-categories" class="btn btn-outline" onclick="toggleCategories(); return false;">Mostrar Todas las Categorías</a>
                </div>
            </div>
        </section>

        <!-- Sección de Ofertas -->
        <section id="ofertas" class="ofertas">
            <div class="container">
                <h2 class="section-title">Ofertas Imperdibles</h2>
                <div class="ofertas-grid">
                <br />
<b>Warning</b>:  Undefined variable $ofertas in <b>C:\xampp\htdocs\MGames\tienda.php</b> on line <b>226</b><br />
<br />
<b>Warning</b>:  foreach() argument must be of type array|object, null given in <b>C:\xampp\htdocs\MGames\tienda.php</b> on line <b>226</b><br />
                </div>
                <div class="text-center mt-6">
                    <a href="todos_productos.php?oferta=1" class="btn btn-outline">Ver Todas las Ofertas</a>
                </div>
            </div>
        </section>

        <!-- Próximos Lanzamientos -->
        <section class="proximos-lanzamientos">
            <div class="container">
                <h2 class="section-title">Próximos Lanzamientos</h2>
                <div class="lanzamientos-grid">
                                        <div class="lanzamiento-card">
                            <div class="lanzamiento-image">
                            <img src="fotosWeb/Grand Theft Auto V.png"
                                alt="Grand Theft Auto V">
                                <div class="lanzamiento-fecha">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="lanzamiento-content">
                                <h3>Grand Theft Auto V</h3>
                                <p>Juego de mundo abierto con una gran historia....</p>
                                <div class="lanzamiento-actions">
                                <span
                                    class="lanzamiento-price">€29.99</span>
                                <a href="producto.php?id=1"
                                    class="btn btn-secondary btn-sm">Pre-ordenar</a>
                                </div>
                            </div>
                        </div>
                                            <div class="lanzamiento-card">
                            <div class="lanzamiento-image">
                            <img src="fotosWeb/Red Dead Redemption 2.png"
                                alt="Red Dead Redemption 2">
                                <div class="lanzamiento-fecha">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="lanzamiento-content">
                                <h3>Red Dead Redemption 2</h3>
                                <p>Aventura en el Salvaje Oeste con gráficos impresionantes....</p>
                                <div class="lanzamiento-actions">
                                <span
                                    class="lanzamiento-price">€49.99</span>
                                <a href="producto.php?id=2"
                                    class="btn btn-secondary btn-sm">Pre-ordenar</a>
                                </div>
                            </div>
                        </div>
                                            <div class="lanzamiento-card">
                            <div class="lanzamiento-image">
                            <img src="fotosWeb/Cyberpunk 2077.png"
                                alt="Cyberpunk 2077">
                                <div class="lanzamiento-fecha">
                                    <i class="far fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="lanzamiento-content">
                                <h3>Cyberpunk 2077</h3>
                                <p>Futurista y con mecánicas RPG....</p>
                                <div class="lanzamiento-actions">
                                <span
                                    class="lanzamiento-price">€39.99</span>
                                <a href="producto.php?id=3"
                                    class="btn btn-secondary btn-sm">Pre-ordenar</a>
                                </div>
                            </div>
                        </div>
                                    </div>
            </div>
        </section>

        <!-- Mejor Valorados -->
        <section class="mejor-valorados">
            <div class="container">
                <h2 class="section-title">Los Mejor Valorados</h2>
                <div class="valorados-grid">
                                        <div class="valorado-card">
                            <div class="valorado-rating">
                            <span
                                class="rating-number">5.0</span>
                                <div class="stars">
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="fas fa-star"></i>
                                                                    </div>
                            </div>
                            <div class="valorado-image">
                            <img src="fotosWeb/God of War.png"
                                alt="God of War">
                            </div>
                            <div class="valorado-content">
                                <h3>God of War</h3>
                            <span
                                class="valorado-category">Aventura</span>
                                <div class="valorado-price">€49.99</div>
                            <a href="producto.php?id=7" class="btn btn-primary btn-sm">Ver
                                Detalles</a>
                            </div>
                        </div>
                                            <div class="valorado-card">
                            <div class="valorado-rating">
                            <span
                                class="rating-number">4.0</span>
                                <div class="stars">
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="fas fa-star"></i>
                                                                                <i class="far fa-star"></i>
                                                                        </div>
                            </div>
                            <div class="valorado-image">
                            <img src="fotosWeb/tarjeta_xbox_10.jpg"
                                alt="Tarjeta Xbox 10€">
                            </div>
                            <div class="valorado-content">
                                <h3>Tarjeta Xbox 10€</h3>
                            <span
                                class="valorado-category">Tarjeta XBOX</span>
                                <div class="valorado-price">€10.00</div>
                            <a href="producto.php?id=30" class="btn btn-primary btn-sm">Ver
                                Detalles</a>
                            </div>
                        </div>
                                    </div>
            </div>
        </section>
    <style>
        /* ===== GLOBAL STYLES ===== */
        :root {
            --primary: #7e22ce;
            --primary-dark: #6b21a8;
            --primary-light: #a855f7;
            --secondary: #2563eb;
            --secondary-dark: #1d4ed8;
            --accent: #f97316;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --gray-light: #cbd5e1;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }



        .video-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        #hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 2100px;
            height: 500px;
            object-fit: cover;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /*background: linear-gradient(to right, rgba(79, 70, 229, 0.8), rgba(99, 102, 241, 0.7));*/
        }

        .hero-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: white;
            text-align: center;
            padding: 0 20px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 1s ease-out;
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            max-width: 600px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease-out 0.3s;
            animation-fill-mode: both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeInUp 1s ease-out 0.6s;
            animation-fill-mode: both;
        }

        .categorias-visuales {
            padding: 17rem 0;
            position: relative;
            z-index: 20;
        }

        body {
            font-family: "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans",
                "Helvetica Neue", sans-serif;
            color: var(--dark);
            background-color: #f1f5f9;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
        }

        ul {
            list-style: none;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .text-center {
            text-align: center;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            box-shadow: var(--shadow);
        }





        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--dark);
            border: 2px solid var(--gray-light);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-link {
            background: none;
            box-shadow: none;
            padding: 0.5rem 0;
            color: var(--primary);
            position: relative;
            font-weight: 600;
        }

        .btn-link::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: var(--transition);
        }

        .btn-link:hover::after {
            width: 100%;
        }

        .btn-link i {
            margin-left: 0.5rem;
            transition: var(--transition);
        }

        .btn-link:hover i {
            transform: translateX(4px);
        }

        /* ===== HEADER ===== */
        .site-header {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .logo span {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            font-weight: 600;
            color: var(--dark);
            position: relative;
        }

        .nav-links a::after {
            content: "";
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            position: relative;
            font-size: 1.25rem;
            color: var(--dark);
            padding: 0.5rem;
            border-radius: 50%;
            transition: var(--transition);
        }

        .header-icon:hover {
            color: var(--primary);
            background-color: rgba(126, 34, 206, 0.1);
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            font-size: 0.625rem;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .balance-indicator {
            background-color: var(--success);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .search-container {
            position: relative;
        }

        .search-button {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--dark);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: var(--transition);
        }

        .search-button:hover {
            color: var(--primary);
            background-color: rgba(126, 34, 206, 0.1);
        }

        .search-form {
            position: absolute;
            top: 100%;
            right: 0;
            width: 300px;
            background-color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            z-index: 10;
        }

        .search-form input,
        .search-form select {
            padding: 0.5rem;
            border: 1px solid var(--gray-light);
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        .search-form button {
            margin-top: 0.5rem;
        }

        .user-profile {
            position: relative;
        }

        .profile-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: var(--transition);
        }

        .profile-button:hover {
            background-color: rgba(126, 34, 206, 0.1);
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .username {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: var(--shadow-lg);
            padding: 0.5rem 0;
            z-index: 10;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
        }

        .profile-dropdown:hover .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-content a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: var(--dark);
            transition: var(--transition);
        }

        .dropdown-content a:hover {
            background-color: rgba(126, 34, 206, 0.1);
            color: var(--primary);
        }

        .auth-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
        }

        /* ===== TIENDA HERO ===== */
        .tienda-hero {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url("../FotosWeb/hero-bg.jpg");
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
            text-align: center;
        }

        .tienda-hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .tienda-hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        .tienda-hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .tienda-hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        /* ===== SECTION STYLES ===== */
        section {
            padding: 4rem 0;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            color: var(--dark);
        }

        .section-title::after {
            content: "";
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 2px;
        }

        /* ===== CATEGORIAS VISUALES ===== */
        .categorias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .categoria-card {
            height: 200px;
            border-radius: 0.5rem;
            overflow: hidden;
            position: relative;
            background-size: cover;
            background-position: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .categoria-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .categoria-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.5));
            transition: var(--transition);
        }

        .categoria-card:hover .categoria-overlay {
            background: linear-gradient(to top, rgba(126, 34, 206, 0.9), rgba(126, 34, 206, 0.5));
        }

        .categoria-content {
            position: absolute;
            bottom: 1.5rem;
            left: 1.5rem;
            color: white;
            z-index: 1;
        }

        .categoria-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .categoria-content p {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* ===== OFERTAS ===== */
        .ofertas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .oferta-card {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .oferta-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .oferta-discount-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background-color: var(--danger);
            color: white;
            font-weight: 700;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            z-index: 1;
        }

        .oferta-image {
            height: 180px;
            overflow: hidden;
        }

        .oferta-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .oferta-card:hover .oferta-image img {
            transform: scale(1.05);
        }

        .oferta-content {
            padding: 1.5rem;
        }

        .oferta-content h3 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .oferta-prices {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .original-price {
            text-decoration: line-through;
            color: var(--gray);
            font-size: 0.875rem;
        }

        .discount-price {
            color: var(--danger);
            font-weight: 700;
            font-size: 1.125rem;
        }

        .oferta-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ===== PROXIMOS LANZAMIENTOS ===== */
        .lanzamientos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .lanzamiento-card {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .lanzamiento-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .lanzamiento-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .lanzamiento-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .lanzamiento-card:hover .lanzamiento-image img {
            transform: scale(1.05);
        }

        .lanzamiento-fecha {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: var(--dark);
            color: white;
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lanzamiento-content {
            padding: 1.5rem;
        }

        .lanzamiento-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .lanzamiento-content p {
            color: var(--gray);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .lanzamiento-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lanzamiento-price {
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--dark);
        }

        /* ===== MEJOR VALORADOS ===== */
        .valorados-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .valorado-card {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .valorado-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .valorado-rating {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: var(--dark);
            color: white;
            padding: 0.5rem;
            border-radius: 0.25rem;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .rating-number {
            font-weight: 700;
            font-size: 1.125rem;
        }

        .stars {
            color: var(--warning);
            font-size: 0.75rem;
        }

        .valorado-image {
            height: 180px;
            overflow: hidden;
        }

        .valorado-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .valorado-card:hover .valorado-image img {
            transform: scale(1.05);
        }

        .valorado-content {
            padding: 1.5rem;
        }

        .valorado-content h3 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .valorado-category {
            display: inline-block;
            background-color: var(--primary-light);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .valorado-price {
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        /* ===== EVENTOS GAMING ===== */
        .eventos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .evento-card {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .evento-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .evento-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .evento-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .evento-card:hover .evento-image img {
            transform: scale(1.05);
        }

        .evento-fecha {
            position: absolute;
            bottom: 0;
            left: 1rem;
            transform: translateY(50%);
            background-color: var(--primary);
            color: white;
            padding: 0.5rem;
            border-radius: 0.25rem;
            text-align: center;
            min-width: 60px;
        }

        .evento-dia {
            display: block;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .evento-mes {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .evento-content {
            padding: 1.5rem;
            padding-top: 2rem;
        }

        .evento-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .evento-info {
            margin-bottom: 1rem;
        }

        .evento-info p {
            font-size: 0.875rem;
            color: var(--gray);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .evento-descripcion {
            font-size: 0.875rem;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        /* ===== BLOG Y GUIAS ===== */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .blog-card {
            background-color: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .blog-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .blog-card:hover .blog-image img {
            transform: scale(1.05);
        }

        .blog-category {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background-color: var(--primary);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            z-index: 1;
        }

        .blog-content {
            padding: 1.5rem;
        }

        .blog-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.75rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .blog-meta span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .blog-content h3 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .blog-content p {
            font-size: 0.875rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        /* ===== COMUNIDAD GAMERS ===== */
        .comunidad-gamers {
            background-color: var(--dark);
            color: white;
        }

        .comunidad-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: center;
        }

        .comunidad-info p {
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
        }

        .comunidad-beneficios {
            margin-bottom: 2rem;
        }

        .comunidad-beneficios li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .comunidad-beneficios i {
            color: var(--success);
        }

        .comunidad-cta {
            display: flex;
            gap: 1rem;
        }

        .comunidad-imagen img {
            border-radius: 0.5rem;
            box-shadow: var(--shadow-lg);
        }

        /* ===== NEWSLETTER ===== */
        .newsletter {
            background-color: var(--primary);
            color: white;
            padding: 3rem 0;
        }

        .newsletter-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .newsletter-info h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .newsletter-form {
            flex: 1;
            max-width: 500px;
        }

        .form-group {
            display: flex;
            gap: 0.5rem;
        }

        .newsletter-form input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .newsletter-form button {
            background-color: var(--dark);
        }

        .newsletter-form button:hover {
            background-color: var(--dark-light);
        }

        /* ===== FOOTER ===== */
        .site-footer {
            background-color: var(--dark);
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .footer-logo img {
            width: 40px;
            height: 40px;
        }

        .footer-logo h3 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        .footer-links h4,
        .footer-contact h4,
        .footer-social h4 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .footer-links ul li {
            margin-bottom: 0.5rem;
        }

        .footer-links ul li a {
            color: var(--gray-light);
            transition: var(--transition);
        }

        .footer-links ul li a:hover {
            color: var(--primary-light);
        }

        .footer-contact p {
            margin-bottom: 0.5rem;
            color: var(--gray-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transition: var(--transition);
        }

        .social-icons a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--gray-light);
            font-size: 0.875rem;
        }

        /* ===== RESPONSIVE STYLES ===== */
        @media (max-width: 1024px) {
            
            .comunidad-content {
                grid-template-columns: 1fr;
            }

            .newsletter-content {
                flex-direction: column;
                text-align: center;
            }

            .newsletter-form {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {

            
            .tienda-hero h1 {
                font-size: 2.5rem;
            }

            .tienda-hero p {
                font-size: 1rem;
            }

            .tienda-hero-buttons {
                flex-direction: column;
                gap: 0.75rem;
            }

            .header-container {
                padding: 0.75rem 0;
            }

            nav {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .search-form {
                width: 250px;
            }

            .section-title {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 640px) {
            .header-actions {
                gap: 0.5rem;
            }

            .auth-buttons {
                display: none;
            }

            .username {
                display: none;
            }

            .categorias-grid,
            .ofertas-grid,
            .lanzamientos-grid,
            .valorados-grid,
            .eventos-grid,
            .blog-grid {
                grid-template-columns: 1fr;
            }

            .form-group {
                flex-direction: column;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-logo {
                justify-content: center;
            }

            .social-icons {
                justify-content: center;
            }

            .footer-contact p {
                justify-content: center;
            }
        }

        /* ===== ANIMATIONS ===== */
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


        .tienda-hero-content,
        .section-title,
        .categorias-grid,
        .ofertas-grid,
        .lanzamientos-grid,
        .valorados-grid,
        .eventos-grid,
        .blog-grid,
        .comunidad-content,
        .newsletter-content {
            animation: fadeIn 0.8s ease-out forwards;
        }

        /* ===== CUSTOM SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
        <!-- Eventos de Gaming -->
        <section class="eventos-gaming">
            <div class="container">
                <h2 class="section-title">Eventos de Gaming</h2>
                <div class="eventos-grid">
                    <div class="evento-card">
                        <div class="evento-image">
                            <img src="FotosWeb/evento-torneo.jpg" alt="Torneo MGames">
                            <div class="evento-fecha">
                                <span class="evento-dia">15</span>
                                <span class="evento-mes">JUN</span>
                            </div>
                        </div>
                        <div class="evento-content">
                            <h3>Torneo MGames 2023</h3>
                            <div class="evento-info">
                                <p><i class="fas fa-map-marker-alt"></i> Centro de Convenciones, Madrid</p>
                                <p><i class="far fa-clock"></i> 10:00 AM - 8:00 PM</p>
                            </div>
                        <p class="evento-descripcion">Participa en el mayor torneo de gaming del año con premios de
                            hasta 10.000€.</p>
                            <a href="eventos.php?id=1" class="btn btn-secondary btn-sm">Más Información</a>
                        </div>
                    </div>
                    <div class="evento-card">
                        <div class="evento-image">
                            <img src="FotosWeb/evento-lanzamiento.jpg" alt="Lanzamiento Exclusivo">
                            <div class="evento-fecha">
                                <span class="evento-dia">22</span>
                                <span class="evento-mes">JUL</span>
                            </div>
                        </div>
                        <div class="evento-content">
                            <h3>Lanzamiento Exclusivo</h3>
                            <div class="evento-info">
                                <p><i class="fas fa-map-marker-alt"></i> Tienda MGames, Barcelona</p>
                                <p><i class="far fa-clock"></i> 8:00 PM - 12:00 AM</p>
                            </div>
                        <p class="evento-descripcion">Sé el primero en probar el nuevo título de la saga más
                            esperada del año.</p>
                            <a href="eventos.php?id=2" class="btn btn-secondary btn-sm">Más Información</a>
                        </div>
                    </div>
                    <div class="evento-card">
                        <div class="evento-image">
                            <img src="FotosWeb/evento-convencion.jpg" alt="Convención Gamer">
                            <div class="evento-fecha">
                                <span class="evento-dia">10</span>
                                <span class="evento-mes">AGO</span>
                            </div>
                        </div>
                        <div class="evento-content">
                            <h3>Convención Gamer 2023</h3>
                            <div class="evento-info">
                                <p><i class="fas fa-map-marker-alt"></i> IFEMA, Madrid</p>
                                <p><i class="far fa-clock"></i> 9:00 AM - 7:00 PM</p>
                            </div>
                        <p class="evento-descripcion">La mayor convención de videojuegos con stands, charlas y
                            actividades para toda la familia.</p>
                            <a href="eventos.php?id=3" class="btn btn-secondary btn-sm">Más Información</a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-6">
                    <a href="eventos.php" class="btn btn-outline">Ver Todos los Eventos</a>
                </div>
            </div>
        </section>

        <!-- Blog y Guías -->
        <section class="blog-guias">
            <div class="container">
                <h2 class="section-title">Blog y Guías de Juegos</h2>
                <div class="blog-grid">
                    <article class="blog-card">
                        <div class="blog-image">
                            <img src="FotosWeb/blog-1.jpg" alt="Guía para principiantes">
                            <div class="blog-category">Guía</div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span><i class="far fa-calendar"></i> 10 Mayo, 2023</span>
                                <span><i class="far fa-user"></i> Admin</span>
                            </div>
                            <h3>Guía para principiantes: Cómo mejorar en juegos competitivos</h3>
                        <p>Aprende las estrategias básicas para mejorar tu rendimiento en los juegos competitivos
                            más populares.</p>
                            <a href="blog.php?id=1" class="btn btn-link">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>
                    <article class="blog-card">
                        <div class="blog-image">
                            <img src="FotosWeb/blog-2.jpg" alt="Análisis de juego">
                            <div class="blog-category">Análisis</div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span><i class="far fa-calendar"></i> 5 Mayo, 2023</span>
                                <span><i class="far fa-user"></i> GameExpert</span>
                            </div>
                            <h3>Análisis en profundidad: El último RPG que está revolucionando el género</h3>
                            <p>Un análisis detallado del juego que está cambiando las reglas de los RPG modernos.</p>
                            <a href="blog.php?id=2" class="btn btn-link">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>
                    <article class="blog-card">
                        <div class="blog-image">
                            <img src="FotosWeb/blog-3.jpg" alt="Noticias de la industria">
                            <div class="blog-category">Noticias</div>
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span><i class="far fa-calendar"></i> 1 Mayo, 2023</span>
                                <span><i class="far fa-user"></i> NewsGamer</span>
                            </div>
                            <h3>Las tendencias que definirán el futuro de los videojuegos en 2023</h3>
                        <p>Descubre las tecnologías y tendencias que marcarán el rumbo de la industria de los
                            videojuegos este año.</p>
                            <a href="blog.php?id=3" class="btn btn-link">Leer más <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>
                </div>
                <div class="text-center mt-6">
                    <a href="blog.php" class="btn btn-outline">Ver Todos los Artículos</a>
                </div>
            </div>
        </section>

        <!-- Comunidad de Gamers -->
        <section class="comunidad-gamers">
            <div class="container">
                <h2 class="section-title">Únete a Nuestra Comunidad</h2>
                <div class="comunidad-content">
                    <div class="comunidad-info">
                        <p>Forma parte de la comunidad de MGames y disfruta de beneficios exclusivos:</p>
                        <ul class="comunidad-beneficios">
                            <li><i class="fas fa-check-circle"></i> Acceso anticipado a nuevos lanzamientos</li>
                            <li><i class="fas fa-check-circle"></i> Descuentos exclusivos para miembros</li>
                            <li><i class="fas fa-check-circle"></i> Participación en torneos y eventos especiales</li>
                            <li><i class="fas fa-check-circle"></i> Foros de discusión con otros gamers</li>
                            <li><i class="fas fa-check-circle"></i> Guías y tutoriales exclusivos</li>
                        </ul>
                        <div class="comunidad-cta">
                            <a href="registro_comunidad.php" class="btn btn-primary">Unirse Ahora</a>
                            <a href="comunidad.php" class="btn btn-outline">Más Información</a>
                        </div>
                    </div>
                    <div class="comunidad-imagen">
                        <img src="FotosWeb/comunidad-gamers.jpg" alt="Comunidad de Gamers">
                    </div>
                </div>
            </div>
        </section>

        <!-- Suscripción a Newsletter -->
        <section class="newsletter">
            <div class="container">
                <div class="newsletter-content">
                    <div class="newsletter-info">
                        <h2>Mantente Informado</h2>
                    <p>Suscríbete a nuestro boletín para recibir las últimas noticias, ofertas exclusivas y
                        lanzamientos de juegos.</p>
                    </div>
                    <form class="newsletter-form" action="suscribir.php" method="post">
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Tu correo electrónico" required>
                            <button type="submit" class="btn btn-primary">Suscribirse</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <footer class="site-footer">
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
                        <li><a href="tienda.php">Tienda</a></li>
                        <li><a href="todos_productos.php">Todos los Productos</a></li>
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
                <p>&copy; 2025 MGames. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Guardar todas las categorías en variables JavaScript
        var todasLasCategorias = <?php echo json_encode($categorias); ?>;
        var categoriasCount = <?php echo json_encode($categorias_count); ?>;
        var colors = <?php echo json_encode($colors); ?>; // Pasamos también los colores si no están definidos en JS

        // Variable para controlar el estado
        var mostrandoTodas = false;

        // Función para alternar la visualización de categorías
        function toggleCategories() {
            const categoriesGrid = document.querySelector('.categorias-grid'); // Usamos el selector CSS
            const button = document.getElementById('toggle-categories');

            // Cambiar el estado
            mostrandoTodas = !mostrandoTodas;

            // Limpiar el grid actual
            categoriesGrid.innerHTML = '';

            // Determinar qué categorías mostrar
            var categoriasAMostrar = mostrandoTodas ? todasLasCategorias : todasLasCategorias.slice(0, 3);

            // Actualizar el texto del botón
            button.innerText = mostrandoTodas ? "Ocultar Categorías" : "Mostrar Todas las Categorías";

            // Añadir/Quitar clase para estilos condicionales (si es necesario, adaptar estilos CSS)
            if (mostrandoTodas) {
                 // categoriesGrid.classList.remove('initial-four'); // Si usaste esta clase en index.php
            } else {
                 // categoriesGrid.classList.add('initial-four'); // Si usaste esta clase en index.php
            }

            // Crear y añadir las tarjetas de categoría
            categoriasAMostrar.forEach(function(cat) {
                const categoryCard = document.createElement('a');
                categoryCard.href = "todos_productos.php?categoria=" + cat.id;
                // Buscamos el conteo de juegos para esta categoría
                 let current_cat_count = 0;
                 const count_data = categoriasCount.find(item => item.id === cat.id);
                 if (count_data) {
                     current_cat_count = count_data.count;
                 }

                // Asignamos la clase de color. Aquí podrías necesitar lógica más avanzada si los colores se asignan por índice en PHP
                // Por simplicidad, si los colores se corresponden 1 a 1 con el orden de las categorías, podrías hacer algo así:
                // let color_class = colors[categoriasAMostrar.indexOf(cat) % colors.length];
                // O si el color ya viene en el objeto cat desde PHP (como en index.php):
                 let color_class = ''// Aquí deberías obtener la clase de color si no viene en el objeto cat
                 // Si la clase de color ya está en el objeto cat (como en index.php después de la edición):
                 if(cat.color) { // Asegúrate de que la columna color se obtenga en la consulta PHP si la necesitas aquí
                     color_class = cat.color;
                 } else { // Si no se obtiene la clase de color, puedes asignarla basado en el índice o id si tienes un mapeo
                     // Lógica para asignar color si no viene en el objeto cat
                     // Ejemplo simple basado en índice (puede no coincidir con PHP si el orden cambia)
                     let index = todasLasCategorias.findIndex(item => item.id === cat.id);
                     if(index !== -1) {
                         color_class = colors[index % colors.length];
                     }
                 }

                categoryCard.className = "categoria-card " + color_class;

                // Añadir estilo de fondo para la imagen y centrarlo
                categoryCard.style.backgroundImage = "url('" + (cat.foto || '') + "')"; // Usar cat.foto y manejar si es nulo/vacío
                categoryCard.style.backgroundSize = "cover";
                categoryCard.style.backgroundPosition = "center";
                categoryCard.style.minHeight = "150px"; // Asegurar una altura mínima como en el CSS

                let cardContentHTML = `
                    <div class="categoria-content">
                `;

                // Mostrar el nombre de la categoría solo si no es una de las tarjetas
                if (cat.nombre !== 'Tarjeta Play' && cat.nombre !== 'Tarjeta XBOX' && cat.nombre !== 'Tarjeta Nintendo') {
                     cardContentHTML += `<h3>${cat.nombre}</h3>`;
                }

                cardContentHTML += `
                        <p>${current_cat_count} juegos</p>
                    </div>
                `;

                categoryCard.innerHTML = cardContentHTML;
                categoriesGrid.appendChild(categoryCard);
            });

            // Prevenir que la página se desplace
            return false;
        }

        // Ejecutar al cargar la página para mostrar solo las 3 primeras inicialmente
        document.addEventListener('DOMContentLoaded', function() {
            // Si quieres que al cargar la página ya se genere la vista de 3 con el script JS:
            const categoriesGrid = document.querySelector('.categorias-grid');
            categoriesGrid.innerHTML = ''; // Limpiar el contenido generado por PHP
            todasLasCategorias.slice(0, 3).forEach(function(cat) { // Usar slice(0, 3) aquí
                const categoryCard = document.createElement('a');
                categoryCard.href = "todos_productos.php?categoria=" + cat.id;
                
                // Buscamos el conteo de juegos para esta categoría
                let current_cat_count = 0;
                const count_data = categoriasCount.find(item => item.id === cat.id);
                if (count_data) {
                    current_cat_count = count_data.count;
                }

                // Asignamos la clase de color (adaptar según cómo obtengas el color en PHP)
                let color_class = '';
                if(cat.color) { // Si la clase de color ya está en el objeto cat (como en index.php después de la edición)
                    color_class = cat.color;
                } else { // Si no se obtiene la clase de color, puedes asignarla basado en el índice o id si tienes un mapeo
                    let index = todasLasCategorias.findIndex(item => item.id === cat.id);
                     if(index !== -1) {
                         color_class = colors[index % colors.length];
                     }
                }
                categoryCard.className = "categoria-card " + color_class;

                // Añadir estilo de fondo para la imagen y centrarlo
                categoryCard.style.backgroundImage = "url('" + (cat.foto || '') + "')";
                categoryCard.style.backgroundSize = "cover";
                categoryCard.style.backgroundPosition = "center";
                categoryCard.style.minHeight = "150px";

                let cardContentHTML = `
                    <div class="categoria-content">
                `;

                // Mostrar el nombre de la categoría solo si no es una de las tarjetas
                if (cat.nombre !== 'Tarjeta Play' && cat.nombre !== 'Tarjeta XBOX' && cat.nombre !== 'Tarjeta Nintendo') {
                    cardContentHTML += `<h3>${cat.nombre}</h3>`;
                }

                cardContentHTML += `
                        <p>${current_cat_count} juegos</p>
                    </div>
                `;

                categoryCard.innerHTML = cardContentHTML;
                categoriesGrid.appendChild(categoryCard);
            });
            // Asegurarse de que el estado inicial sea false (mostrando solo 3)
            mostrandoTodas = false;
            const button = document.getElementById('toggle-categories');
            if(button) { // Asegurarse de que el botón exista
                button.innerText = "Mostrar Todas las Categorías";
            }

        });

    </script>

</body>

</html>