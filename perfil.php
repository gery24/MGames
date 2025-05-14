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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/perfil.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Actualizando la tipografía para que coincida con index.php */
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --secondary-color: #6366f1;
            --accent-color: #818cf8;
            --text-color: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 0.5rem;
            --admin-color: #ff0000;
            --admin-dark: #cc0000;
            --admin-bg-light: #fff0f0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
            color: var(--text-color);
            line-height: 1.3;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            color: var(--text-color);
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        
        /* Estilos adicionales para botones consistentes */
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        /* Ajuste para los textos de las tarjetas */
        .card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        
        .card p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}
    </style>
</head>
<body class="<?php echo $bodyClass; ?>">
    <?php require_once 'includes/header.php'; ?>

    <div class="content">
        <div class="profile-container">
            <!-- Sidebar con opciones de menú -->
            <div class="sidebar">
                <div class="user-info">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h3>
                        <span class="user-role <?php echo strtolower($usuario['rol']); ?>">
                            <?php echo htmlspecialchars($usuario['rol']); ?>
                        </span>
                    </div>
                </div>
                
                <nav class="profile-nav">
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
                        <li class="divider"></li>
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
                </nav>
            </div>

            <!-- Contenido principal -->
            <div class="profile-content" id="dynamic-content">
                <!-- El contenido se cargará dinámicamente con JavaScript -->
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <p>Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mensajes de éxito o error -->
    <div id="notification-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modal-message"></div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Variable para almacenar la sección actual
            let currentSection = 'ajustes';

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
                currentSection = content; // Actualizar la sección actual
                loadContent(content);
            });

            // Verificar si hay un parámetro de error en la URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const errorMsg = urlParams.get('error');
                showModal('Error: ' + decodeURIComponent(errorMsg), 'error');
                // Limpiar la URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Función para mostrar el modal con mensajes
            function showModal(message, type = 'success') {
                $('#modal-message').html(`
                    <div class="alert alert-${type}">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        ${message}
                    </div>
                `);
                $('#notification-modal').css('display', 'flex');
                
                // Cerrar automáticamente después de 3 segundos
                setTimeout(function() {
                    $('#notification-modal').css('display', 'none');
                }, 3000);
            }

            // Cerrar el modal al hacer clic en la X
            $('.close-modal').on('click', function() {
                $('#notification-modal').css('display', 'none');
            });

            // Cerrar el modal al hacer clic fuera de él
            $(window).on('click', function(e) {
                if ($(e.target).is('#notification-modal')) {
                    $('#notification-modal').css('display', 'none');
                }
            });

            // Función para cargar contenido dinámicamente
            function loadContent(content) {
                $('#dynamic-content').html(`
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        <p>Cargando...</p>
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
                            <form id="deposit-form" class="transaction-form">
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
                            <form id="withdraw-form" class="transaction-form">
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
                            '<p class="empty-message">No hay transacciones registradas.</p>' : 
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

                // Manejar el envío del formulario de depósito con AJAX
                $('#deposit-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = $(this).serialize();
                    
                    // Mostrar indicador de carga
                    $(this).find('button').html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
                    $(this).find('button').prop('disabled', true);
                    
                    $.ajax({
                        url: 'procesar_transaccion.php',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showModal('Fondos agregados correctamente', 'success');
                                // Recargar la sección de billetera para mostrar el saldo actualizado
                                loadContent('billetera');
                            } else {
                                showModal(response.message || 'Error al procesar la transacción', 'error');
                                // Restaurar el botón
                                $('#deposit-form').find('button').html('<i class="fas fa-plus"></i> Agregar Fondos');
                                $('#deposit-form').find('button').prop('disabled', false);
                            }
                        },
                        error: function() {
                            showModal('Error al procesar la transacción', 'error');
                            // Restaurar el botón
                            $('#deposit-form').find('button').html('<i class="fas fa-plus"></i> Agregar Fondos');
                            $('#deposit-form').find('button').prop('disabled', false);
                        }
                    });
                });

                // Manejar el envío del formulario de retiro con AJAX
                $('#withdraw-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = $(this).serialize();
                    
                    // Mostrar indicador de carga
                    $(this).find('button').html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
                    $(this).find('button').prop('disabled', true);
                    
                    $.ajax({
                        url: 'procesar_retiro.php',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showModal('Fondos retirados correctamente', 'success');
                                // Recargar la sección de billetera para mostrar el saldo actualizado
                                loadContent('billetera');
                            } else {
                                showModal(response.message || 'Error al procesar el retiro', 'error');
                                // Restaurar el botón
                                $('#withdraw-form').find('button').html('<i class="fas fa-minus"></i> Retirar Fondos');
                                $('#withdraw-form').find('button').prop('disabled', false);
                            }
                        },
                        error: function() {
                            showModal('Error al procesar el retiro', 'error');
                            // Restaurar el botón
                            $('#withdraw-form').find('button').html('<i class="fas fa-minus"></i> Retirar Fondos');
                            $('#withdraw-form').find('button').prop('disabled', false);
                        }
                    });
                });
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
                            <div class="profile-avatar large">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                            </div>
                            <div class="profile-details">
                                <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h2>
                                <p class="user-email"><?php echo htmlspecialchars($usuario['email']); ?></p>
                                <div class="profile-badge <?php echo strtolower($usuario['rol']); ?>">
                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>Información Personal</h3>
                            <form action="update_profile.php" method="POST" class="profile-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="nombre">Nombre:</label>
                                        <div class="input-container">
                                            <i class="fas fa-user"></i>
                                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="apellido">Apellido:</label>
                                        <div class="input-container">
                                            <i class="fas fa-user"></i>
                                            <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <div class="input-container">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="rol">Rol:</label>
                                    <div class="input-container">
                                        <i class="fas fa-user-tag"></i>
                                        <input type="text" id="rol" name="rol" value="<?php echo htmlspecialchars($usuario['rol']); ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="button-group">
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Información
                                    </button>
                                    <button type="button" class="btn-secondary" id="change-password">
                                        <i class="fas fa-key"></i> Cambiar Contraseña
                                    </button>
                                </div>
                            </form>
                        </div>
                    `);
                    
                    // Manejar clic en "Cambiar Contraseña"
                    $('#change-password').on('click', function() {
                        // Aquí puedes mostrar un modal o redirigir a una página de cambio de contraseña
                        alert('Funcionalidad de cambio de contraseña');
                    });
                    
                } else if (content === 'wishlist') {
                    // Contenido de Lista de Deseos - Ajustado para que sea más amplio
                    let wishlistHTML = `
                        <h1>Mi Lista de Deseos</h1>
                    `;
                    
                    <?php if (!empty($wishlist)): ?>
                        wishlistHTML += `<div class="card-grid">`;
                        
                        <?php foreach ($wishlist as $producto): ?>
                            wishlistHTML += `
                                <div class="product-card">
                                    <div class="product-image">
                                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        <div class="product-actions">
                                            <a href="producto.php?id=<?php echo $producto['id']; ?>" class="action-btn view">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="eliminar_deseo.php" class="action-form">
                                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                                <button type="submit" class="action-btn remove">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                        <p class="product-price">€<?php echo number_format($producto['precio'], 2); ?></p>
                                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn-primary btn-sm">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            `;
                        <?php endforeach; ?>
                        
                        wishlistHTML += `</div>`;
                    <?php else: ?>
                        wishlistHTML += `
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <h3>Tu lista de deseos está vacía</h3>
                                <p>Explora nuestra tienda y añade tus juegos favoritos a la lista de deseos.</p>
                                <a href="index.php" class="btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Ir a la tienda
                                </a>
                            </div>
                        `;
                    <?php endif; ?>
                    
                    $('#dynamic-content').html(wishlistHTML);
                } else if (content === 'pedidos') {
                    // Contenido de Pedidos
                    let pedidosHTML = `
                        <h1>Mis Pedidos</h1>
                    `;
                    
                    <?php if (!empty($pedidos)): ?>
                        pedidosHTML += `
                            <div class="card">
                                <div class="table-container">
                                    <table class="data-table">
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
                        
                        pedidosHTML += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    <?php else: ?>
                        pedidosHTML += `
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <h3>No tienes pedidos realizados</h3>
                                <p>Cuando realices compras, tus pedidos aparecerán aquí.</p>
                                <a href="index.php" class="btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Ir a la tienda
                                </a>
                            </div>
                        `;
                    <?php endif; ?>
                    
                    $('#dynamic-content').html(pedidosHTML);
                    
                } else if (content === 'segunda_mano') {
                    // Contenido de Segunda Mano
                    $('#dynamic-content').html(`
                        <h1>Mis Productos de Segunda Mano</h1>
                        
                        <div class="card">
                            <div class="card-header">
                                <h3>Gestión de Productos</h3>
                                <button id="add-second-hand" class="btn-primary">
                                    <i class="fas fa-plus"></i> Publicar Nuevo Producto
                                </button>
                            </div>
                            
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <h3>No tienes productos de segunda mano publicados</h3>
                                <p>Publica tus juegos usados para venderlos a otros usuarios.</p>
                            </div>
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
                        <div class="card">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <h3>Sección en desarrollo</h3>
                                <p>Esta funcionalidad estará disponible próximamente.</p>
                            </div>
                        </div>
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
</body>
</html>
