<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Verificar si el usuario es admin para añadir la clase 'admin' al body
$isAdmin = isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'ADMIN';
$bodyClass = $isAdmin ? 'admin' : '';

$error = '';
$success = '';
$usuario = $_SESSION['usuario'];

// Obtener información adicional del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario['id']]);
    $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($datosUsuario) {
        // Actualizar solo si encontramos datos
        foreach ($datosUsuario as $key => $value) {
            $usuario[$key] = $value;
        }
        $_SESSION['usuario'] = $usuario;
    }
    
    // Obtener pedidos del usuario
    $stmt_pedidos = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
    $stmt_pedidos->execute([$usuario['id']]);
    $pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener lista de deseos
    $stmt_wishlist = $pdo->prepare("
        SELECT p.* FROM productos p
        JOIN lista_deseos ld ON p.id = ld.producto_id
        WHERE ld.usuario_id = ?
    ");
    $stmt_wishlist->execute([$usuario['id']]);
    $wishlist = $stmt_wishlist->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener transacciones del usuario
    $stmt_transacciones = $pdo->prepare("SELECT * FROM transacciones WHERE usuario_id = ? ORDER BY fecha DESC");
    $stmt_transacciones->execute([$usuario['id']]);
    $transacciones = $stmt_transacciones->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = 'Error al obtener información del usuario';
}

// Mostrar mensaje de éxito si viene de actualizar perfil
if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
    $success = 'Tu información ha sido actualizada correctamente';
}

$titulo = "Mi Perfil - MGames";
?>

<?php require_once 'includes/header.php'; ?>

    <div class="content">
        <div class="profile-container">
            <!-- Sidebar con opciones de menú -->
            <div class="sidebar">
                <h2>Mi Cuenta <?php if($isAdmin): ?><span class="admin-badge">ADMIN</span><?php endif; ?></h2>
                <ul>
                    <li>
                        <a href="#" class="menu-option active" data-content="ajustes">
                            <i class="fas fa-user-cog"></i> Ajustes de Cuenta
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-option" data-content="pedidos">
                            <i class="fas fa-shopping-bag"></i> Mis Pedidos
                        </a>
                    </li>
                    <?php if ($usuario['rol'] !== 'ADMIN'): ?>
                    <li>
                        <a href="#" class="menu-option" data-content="wishlist">
                            <i class="fas fa-heart"></i> Lista de Deseos
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-option" data-content="segunda_mano">
                            <i class="fas fa-handshake"></i> Segunda Mano
                        </a>
                    </li>
                    <li>
                        <a href="#" class="menu-option" data-content="billetera">
                            <i class="fas fa-wallet"></i> Billetera
                        </a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="panel_admin.php" class="menu-option">
                            <i class="fas fa-gamepad"></i> Añadir Juegos
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="#" class="menu-option danger" id="logout">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </li>
                    <?php if ($usuario['rol'] !== 'ADMIN'): ?>
                    <li>
                        <a href="#" class="menu-option danger" id="delete-account">
                            <i class="fas fa-user-times"></i> Eliminar Cuenta
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Contenido principal -->
            <div class="profile-info" id="dynamic-content">
                <!-- El contenido se cargará dinámicamente con JavaScript -->
                <div class="loading">
                    <div class="loading-spinner"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Cargar automáticamente el contenido de "Ajustes de Cuenta" al entrar
            loadContent('ajustes');

            // Manejar clics en las opciones del menú
            $('.menu-option').on('click', function(e) {
                e.preventDefault();
                
                // No hacer nada si es un enlace externo
                if ($(this).attr('href') !== '#') return;
                
                // Actualizar clases activas
                $('.menu-option').removeClass('active');
                $(this).addClass('active');
                
                const content = $(this).data('content');
                loadContent(content);
            });

            // Verificar si hay un parámetro de error en la URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const errorMsg = urlParams.get('error');
                alert('Error: ' + decodeURIComponent(errorMsg));
                // Limpiar la URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Función para cargar contenido dinámicamente
            function loadContent(content) {
                $('#dynamic-content').html(`
                    <div class="loading">
                        <div class="loading-spinner"></div>
                    </div>
                `);

                // Para la billetera, obtenemos los datos actualizados
                if (content === 'billetera') {
                    // Obtener los datos más recientes del usuario
                    $.ajax({
                        url: 'get_wallet_data.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.error) {
                                $('#dynamic-content').html(`
                                    <h1>Mi Billetera</h1>
                                    <div class="alert alert-error">
                                        <i class="fas fa-exclamation-circle"></i> ${data.error}
                                    </div>
                                `);
                            } else {
                                renderBilletera(data.saldo, data.transacciones);
                            }
                        },
                        error: function() {
                            $('#dynamic-content').html(`
                                <h1>Mi Billetera</h1>
                                <div class="alert alert-error">
                                    <i class="fas fa-exclamation-circle"></i> Error al cargar los datos de la billetera.
                                </div>
                            `);
                        }
                    });
                } else {
                    // Para el resto de contenidos, los renderizamos directamente
                    setTimeout(function() {
                        renderContent(content);
                    }, 300);
                }
            }

            // Función para renderizar el contenido de la billetera
            function renderBilletera(saldo, transacciones) {
                $('#dynamic-content').html(`
                    <h1>Mi Billetera</h1>
                    
                    <div class="wallet-card">
                        <div class="wallet-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="wallet-label">Saldo disponible</div>
                        <div class="wallet-balance">€${parseFloat(saldo).toFixed(2)}</div>
                        <div class="wallet-label">Última actualización: ${new Date().toLocaleDateString()}</div>
                    </div>

                    <div class="transaction-forms">
                        <!-- Formulario para agregar dinero -->
                        <div class="form-container deposit">
                            <h3><i class="fas fa-plus-circle"></i> Agregar Dinero</h3>
                            <form class="transaction-form" method="POST" action="procesar_transaccion.php">
                                <div class="form-group">
                                    <label for="monto-deposito">Monto:</label>
                                    <div class="input-container">
                                        <input type="number" id="monto-deposito" name="monto" placeholder="Monto a agregar" min="1" step="0.01" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion-deposito">Descripción:</label>
                                    <div class="input-container">
                                        <input type="text" id="descripcion-deposito" name="descripcion" placeholder="Ej: Recarga de saldo" required>
                                    </div>
                                </div>
                                <input type="hidden" name="origen" value="perfil">
                                <input type="hidden" name="tipo" value="deposito">
                                <button type="submit" class="btn-deposit">
                                    <i class="fas fa-plus"></i> Agregar Fondos
                                </button>
                            </form>
                        </div>

                        <!-- Formulario para retirar dinero -->
                        <div class="form-container withdraw">
                            <h3><i class="fas fa-minus-circle"></i> Retirar Dinero</h3>
                            <form class="transaction-form" method="POST" action="procesar_retiro.php">
                                <div class="form-group">
                                    <label for="monto-retiro">Monto:</label>
                                    <div class="input-container">
                                        <input type="number" id="monto-retiro" name="monto" placeholder="Monto a retirar" min="1" max="${saldo}" step="0.01" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion-retiro">Descripción:</label>
                                    <div class="input-container">
                                        <input type="text" id="descripcion-retiro" name="descripcion" placeholder="Ej: Retiro a cuenta bancaria" required>
                                    </div>
                                </div>
                                <input type="hidden" name="origen" value="perfil">
                                <button type="submit" class="btn-withdraw">
                                    <i class="fas fa-minus"></i> Retirar Fondos
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="transaction-list">
                        <h2><i class="fas fa-history"></i> Historial de Transacciones</h2>
                        ${transacciones.length === 0 ? 
                            '<p>No hay transacciones registradas.</p>' : 
                            transacciones.map(transaccion => `
                                <div class="transaction-item ${parseFloat(transaccion.monto) < 0 ? 'withdrawal' : 'deposit'}">
                                    <div class="transaction-info">
                                        <p class="transaction-title">
                                            <i class="fas ${parseFloat(transaccion.monto) < 0 ? 'fa-arrow-down' : 'fa-arrow-up'}"></i> 
                                            ${transaccion.descripcion}
                                        </p>
                                        <p class="transaction-date">${transaccion.fecha || 'Fecha no disponible'}</p>
                                    </div>
                                    <p class="transaction-amount ${parseFloat(transaccion.monto) >= 0 ? 'positive' : 'negative'}">
                                        €${parseFloat(transaccion.monto).toFixed(2)}
                                    </p>
                                </div>
                            `).join('')
                        }
                    </div>
                `);
            }

            // Función para renderizar el resto de contenidos
            function renderContent(content) {
                if (content === 'ajustes') {
                    // Contenido de Ajustes de Cuenta
                    $('#dynamic-content').html(`
                        <h1>Ajustes de Cuenta</h1>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="profile-header">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                            </div>
                            <div class="profile-details">
                                <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h2>
                                <p><?php echo htmlspecialchars($usuario['email']); ?></p>
                                <div class="profile-badge">
                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <form action="update_profile.php" method="POST">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <div class="input-container">
                                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="apellido">Apellido:</label>
                                <div class="input-container">
                                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <div class="input-container">
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rol">Rol:</label>
                                <div class="input-container">
                                    <input type="text" id="rol" name="rol" value="<?php echo htmlspecialchars($usuario['rol']); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="button-group">
                                <button type="submit">Actualizar Información</button>
                                <button type="button" class="secondary" id="change-password">Cambiar Contraseña</button>
                            </div>
                        </form>
                    `);
                    
                    // Manejar clic en "Cambiar Contraseña"
                    $('#change-password').on('click', function() {
                        // Aquí puedes mostrar un modal o redirigir a una página de cambio de contraseña
                        alert('Funcionalidad de cambio de contraseña');
                    });
                    
                } else if (content === 'pedidos') {
                    // Contenido de Pedidos
                    let pedidosHTML = `
                        <h1>Mis Pedidos</h1>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID Pedido</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    <?php if (!empty($pedidos)): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            pedidosHTML += `
                                <tr>
                                    <td>#<?php echo $pedido['id']; ?></td>
                                    <td><?php echo htmlspecialchars($pedido['fecha']); ?></td>
                                    <td>€<?php echo number_format($pedido['total'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($pedido['estado']); ?>">
                                            <?php echo htmlspecialchars($pedido['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="detalle_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn-link">
                                            <i class="fas fa-eye"></i> Ver detalles
                                        </a>
                                    </td>
                                </tr>
                            `;
                        <?php endforeach; ?>
                    <?php else: ?>
                        pedidosHTML += `
                            <tr>
                                <td colspan="5" style="text-align: center;">No tienes pedidos realizados.</td>
                            </tr>
                        `;
                    <?php endif; ?>
                    
                    pedidosHTML += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    $('#dynamic-content').html(pedidosHTML);
                    
                } else if (content === 'wishlist') {
                    // Contenido de Lista de Deseos
                    let wishlistHTML = `
                        <h1>Mi Lista de Deseos</h1>
                    `;
                    
                    <?php if (!empty($wishlist)): ?>
                        wishlistHTML += `<div class="card-grid">`;
                        
                        <?php foreach ($wishlist as $producto): ?>
                            wishlistHTML += `
                                <div class="card">
                                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="card-image">
                                    <div class="card-content">
                                        <h3 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                        <p class="card-price">€<?php echo number_format($producto['precio'], 2); ?></p>
                                        <div class="card-actions">
                                            <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn-link">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <form method="POST" action="eliminar_deseo.php" style="display: inline;">
                                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                                <button type="submit" class="btn-link danger">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            `;
                        <?php endforeach; ?>
                        
                        wishlistHTML += `</div>`;
                    <?php else: ?>
                        wishlistHTML += `
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No tienes productos en tu lista de deseos.
                            </div>
                            <p>Explora nuestra tienda y añade tus juegos favoritos a la lista de deseos.</p>
                            <a href="index.php" class="btn-link">
                                <i class="fas fa-shopping-cart"></i> Ir a la tienda
                            </a>
                        `;
                    <?php endif; ?>
                    
                    $('#dynamic-content').html(wishlistHTML);
                    
                } else if (content === 'segunda_mano') {
                    // Contenido de Segunda Mano
                    $('#dynamic-content').html(`
                        <h1>Mis Productos de Segunda Mano</h1>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aquí puedes gestionar tus productos de segunda mano.
                        </div>
                        
                        <button id="add-second-hand" class="btn-primary">
                            <i class="fas fa-plus"></i> Publicar Nuevo Producto
                        </button>
                        
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No tienes productos de segunda mano publicados.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `);
                    
                    // Manejar clic en "Publicar Nuevo Producto"
                    $('#add-second-hand').on('click', function() {
                        window.location.href = 'publicar_segunda_mano.php';
                    });
                } else {
                    // Contenido por defecto o no encontrado
                    $('#dynamic-content').html(`
                        <h1>${content.charAt(0).toUpperCase() + content.slice(1).replace(/_/g, ' ')}</h1>
                        <p>Contenido en desarrollo.</p>
                    `);
                }
            }

            // Manejar clic en "Cerrar Sesión"
            $('#logout').on('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                    window.location.href = 'logout.php';
                }
            });

            // Manejar clic en "Eliminar Cuenta"
            $('#delete-account').on('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')) {
                    window.location.href = 'eliminar_cuenta.php';
                }
            });
        });
    </script>
</body>
</html>
