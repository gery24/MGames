<?php
// Verificar si el usuario está logueado
$isLoggedIn = isset($_SESSION['usuario']);
// Verificar si el usuario es admin
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MGames</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body class="<?php echo $isAdmin ? 'admin' : ''; ?>">
    <header class="site-header">
        <div class="container">
            <a href="index.php" class="logo">
                <span>MGames</span>
            </a>
            
            <nav class="main-nav">
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="tienda.php">Tienda</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <a href="lista_deseos.php" class="header-icon" aria-label="Lista de deseos">
                    <i class="fas fa-heart"></i>
                    <?php if(isset($_SESSION['wishlist_count']) && $_SESSION['wishlist_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['wishlist_count']; ?></span>
                    <?php endif; ?>
                </a>
                <div class="search-dropdown">
                    <button id="searchToggle" class="header-icon" aria-label="Buscar">
                        <i class="fas fa-search"></i>
                    </button>
                    <div id="searchDropdownContent" class="dropdown-content">
                        <input type="text" id="searchInput" placeholder="Buscar juegos o páginas...">
                        <div id="searchResults"></div>
                    </div>
                </div>
                <a href="carrito.php" class="header-icon" aria-label="Carrito de compras">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if(isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="badge"><?php echo $_SESSION['cart_count']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="cartera.php" class="header-icon" aria-label="Mi cartera">
                    <i class="fas fa-wallet"></i>
                    <?php if(isset($_SESSION['user_balance'])): ?>
                        <span class="balance-indicator"><?php echo number_format($_SESSION['user_balance'], 2); ?>€</span>
                    <?php endif; ?>
                </a>
                
                <?php if($isLoggedIn): ?>
                    <div class="profile-dropdown">
                        <button class="profile-button">
                            <div class="avatar-circle <?php echo $isAdmin ? 'admin-avatar' : ''; ?>">
                                <?php 
                                // Obtener la primera letra del nombre de usuario
                                $initial = strtoupper(substr($_SESSION['usuario']['nombre'], 0, 1));
                                echo $initial;
                                ?>
                            </div>
                        </button>
                        <div id="profileDropdownContent" class="dropdown-content <?php echo $isAdmin ? 'admin-dropdown' : ''; ?>">
                            <a href="perfil.php"><i class="fas fa-user"></i> Mi Perfil</a>
                            <?php if($isAdmin): ?>
                                <a href="panel_admin.php" class="admin-link"><i class="fas fa-shield-alt"></i> Panel Admin</a>
                            <?php endif; ?>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-outline">INICIAR SESIÓN</a>
                        <a href="register.php" class="btn btn-primary">REGISTRARSE</a>
                    </div>
                <?php endif; ?>
                
                <button id="menuToggle" class="mobile-menu-toggle" aria-label="Menú móvil">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
</body>
<script>
    console.log('Script de header cargado');

    const searchToggle = document.getElementById('searchToggle');
    const searchDropdownContent = document.getElementById('searchDropdownContent');
    const searchInput = document.getElementById('searchInput');
    const profileButton = document.querySelector('.profile-button');

    // Obtener el desplegable del perfil por su nuevo ID
    const profileDropdownContent = document.getElementById('profileDropdownContent');

    console.log('searchToggle:', searchToggle);
    console.log('searchDropdownContent:', searchDropdownContent);
    console.log('profileButton:', profileButton);
    console.log('profileDropdownContent:', profileDropdownContent);

    // Toggle search dropdown visibility
    if (searchToggle && searchDropdownContent) {
        searchToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Botón de búsqueda clicado');
            if (profileDropdownContent) profileDropdownContent.style.display = 'none';
            
            console.log('Estado de display ANTES (search):', searchDropdownContent.style.display);
            searchDropdownContent.style.display = searchDropdownContent.style.display === 'block' ? 'none' : 'block';
            console.log('Estado de display DESPUÉS (search):', searchDropdownContent.style.display);
            if (searchDropdownContent.style.display === 'block') {
                searchInput.focus();
            }
        });
    }

    // Profile dropdown functionality
    if (profileButton && profileDropdownContent) {
        profileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Botón de perfil clicado');
            
            console.log('Estado de display ANTES (profile):', profileDropdownContent.style.display);
            profileDropdownContent.style.display = profileDropdownContent.style.display === 'block' ? 'none' : 'block';
            console.log('Estado de display DESPUÉS (profile):', profileDropdownContent.style.display);
        });
    }

    // Close all dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const isClickInsideDropdownArea = e.target.closest('.search-dropdown, .profile-dropdown');
        
        if (!isClickInsideDropdownArea) {
            if (searchDropdownContent) searchDropdownContent.style.display = 'none';
            if (profileDropdownContent) profileDropdownContent.style.display = 'none';
        }
    });

    // Basic navigation logic based on input
    const searchResultsDiv = document.getElementById('searchResults');

    const searchableItems = [
        { name: 'Inicio', url: 'index.php', type: 'page' },
        { name: 'Tienda', url: 'tienda.php', type: 'page' },
        { name: 'Contacto', url: 'contacto.php', type: 'page' },
        { name: 'Carrito', url: 'carrito.php', type: 'page' },
        { name: 'Sobre Nosotros', url: 'about.php', type: 'page' }, // Asumiendo about.php
        // Lista completa de productos con sus IDs
        { name: 'Grand Theft Auto V', url: 'producto.php?id=1', type: 'game' },
        { name: 'Red Dead Redemption 2', url: 'producto.php?id=2', type: 'game' },
        { name: 'Cyberpunk 2077', url: 'producto.php?id=3', type: 'game' },
        { name: 'Doom Eternal', url: 'producto.php?id=4', type: 'game' },
        { name: 'Far Cry 6', url: 'producto.php?id=5', type: 'game' },
        { name: 'Zelda: Breath of the Wild', url: 'producto.php?id=6', type: 'game' },
        { name: 'God of War', url: 'producto.php?id=7', type: 'game' },
        { name: 'Uncharted 4', url: 'producto.php?id=8', type: 'game' },
        { name: 'Tomb Raider', url: 'producto.php?id=9', type: 'game' },
        { name: "Assassin's Creed Valhalla", url: 'producto.php?id=10', type: 'game' },
        { name: 'The Witcher 3', url: 'producto.php?id=11', type: 'game' },
        { name: 'Dark Souls III', url: 'producto.php?id=12', type: 'game' },
        { name: 'Elden Ring', url: 'producto.php?id=13', type: 'game' },
        { name: 'Tarjeta PlayStation Store $10', url: 'producto.php?id=14', type: 'other' },
        { name: 'Diablo IV', url: 'producto.php?id=15', type: 'game' },
        { name: 'FIFA 24', url: 'producto.php?id=16', type: 'game' },
        { name: 'Madden NFL 24', url: 'producto.php?id=17', type: 'game' },
        { name: 'Gran Turismo 7', url: 'producto.php?id=18', type: 'game' },
        { name: "Tony Hawk's Pro Skater 1+2", url: 'producto.php?id=19', type: 'game' },
        { name: 'Forza Horizon 5', url: 'producto.php?id=20', type: 'game' },
        { name: 'Tarjeta Xbox Live $25', url: 'producto.php?id=21', type: 'other' },
        { name: 'Need for Speed Heat', url: 'producto.php?id=22', type: 'game' },
        { name: 'Mario Kart 8 Deluxe', url: 'producto.php?id=23', type: 'game' },
        { name: 'F1 23', url: 'producto.php?id=24', type: 'game' },
        { name: 'Dirt 5', url: 'producto.php?id=25', type: 'game' },
        { name: 'Tarjeta Nintendo eShop $20', url: 'producto.php?id=26', type: 'other' },
        { name: 'Tarjeta Steam Wallet $50', url: 'producto.php?id=27', type: 'other' },
        { name: 'Tarjeta Play 30€', url: 'producto.php?id=28', type: 'other' },
        { name: 'Tarjeta Play 50€', url: 'producto.php?id=29', type: 'other' },
        { name: 'Tarjeta Xbox 10€', url: 'producto.php?id=30', type: 'other' },
        { name: 'Tarjeta Xbox 25€', url: 'producto.php?id=31', type: 'other' },
        { name: 'Tarjeta Xbox 30€', url: 'producto.php?id=32', type: 'other' },
        { name: 'Tarjeta Xbox 50€', url: 'producto.php?id=33', type: 'other' },
        { name: 'Tarjeta Nintendo 5€', url: 'producto.php?id=34', type: 'other' },
        { name: 'Tarjeta Nintendo 10€', url: 'producto.php?id=35', type: 'other' },
        { name: 'Tarjeta Nintendo 20€', url: 'producto.php?id=36', type: 'other' },
        { name: 'Tarjeta Nintendo 25€', url: 'producto.php?id=37', type: 'other' },
        { name: 'Tarjeta Nintendo 50€', url: 'producto.php?id=38', type: 'other' },
        { name: 'Hearts of Iron IV', url: 'producto.php?id=100', type: 'game' },
        { name: 'Age of Empires IV', url: 'producto.php?id=101', type: 'game' },
        { name: 'Power Wash Simulator', url: 'producto.php?id=102', type: 'game' },
        { name: 'Euro Truck Simulator 2', url: 'producto.php?id=103', type: 'game' },
        { name: "Tom Clancy's Rainbow Six Siege", url: 'producto.php?id=104', type: 'game' },
        { name: 'Call of Duty®: Modern Warfare', url: 'producto.php?id=105', type: 'game' },
        { name: 'UFC 5', url: 'producto.php?id=106', type: 'game' },
        { name: 'WWE 2K24', url: 'producto.php?id=107', type: 'game' },
        { name: 'Silent Hill 2', url: 'producto.php?id=108', type: 'game' },
        { name: 'Resident Evil 2 Remake', url: 'producto.php?id=109', type: 'game' },
    ];

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        searchResultsDiv.innerHTML = ''; // Limpiar resultados anteriores

        if (query.length === 0) {
            return; // No mostrar nada si el campo está vacío
        }

        const filteredItems = searchableItems.filter(item =>
            item.name.toLowerCase().includes(query)
        );

        if (filteredItems.length > 0) {
            filteredItems.forEach(item => {
                const resultElement = document.createElement('div');
                resultElement.classList.add('search-result-item'); // Clase para estilizar resultados
                resultElement.innerHTML = `<a href="${item.url}">${item.name}</a>`;
                searchResultsDiv.appendChild(resultElement);
            });
        } else {
            searchResultsDiv.innerHTML = '<div class="no-results">No se encontraron resultados</div>';
        }
    });

    // Opcional: Ocultar resultados al perder el foco del input, con un pequeño retardo
    searchInput.addEventListener('blur', () => {
        setTimeout(() => {
            searchResultsDiv.innerHTML = ''; // Ocultar resultados
        }, 100);
    });

</script>
</html>
