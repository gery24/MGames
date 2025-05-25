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

        /* Estilos para el avatar del perfil */
        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Versión grande del avatar para la sección de perfil */
        .profile-avatar.large {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        /* Si el usuario es admin, cambiamos el color del avatar */
        body.admin .profile-avatar {
            background-color: var(--admin-color);
        }

        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            position: relative;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: var(--text-color);
        }

        /* Estilos para el formulario de cambio de contraseña */
        .password-form h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: var(--text-color);
            font-size: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 15px;
        }

        .password-form .form-group {
            margin-bottom: 20px;
        }

        .password-form label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-color);
            font-weight: 600;
        }

        .password-form .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-form .input-container i {
            position: absolute;
            left: 10px;
            color: var(--text-light);
        }

        .password-form input[type="password"],
        .password-form input[type="text"] {
            width: 100%;
            padding: 10px 40px 10px 35px;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius);
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .password-form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--text-color);
        }

        /* Indicador de fuerza de contraseña */
        .password-strength {
            margin-top: 10px;
        }

        .strength-bar {
            height: 5px;
            background-color: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-indicator {
            height: 100%;
            width: 0;
            background-color: #ff4d4d;
            transition: width 0.3s, background-color 0.3s;
        }

        #strength-text {
            font-size: 12px;
            color: var(--text-light);
        }

        /* Botones del formulario */
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .btn-cancel {
            background-color: #e5e7eb;
            color: var(--text-color);
            padding: 10px 20px;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: #d1d5db;
        }

        /* Alertas */
        .alert {
            padding: 15px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .alert i {
            font-size: 24px;
            margin-top: 2px;
        }

        .alert strong {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .alert p {
            margin: 0;
            font-size: 14px;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #d1fae5;
        }

        .alert-success::before {
            background-color: #10b981;
        }

        .alert-success i {
            color: #10b981;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fee2e2;
        }

        .alert-error::before {
            background-color: #ef4444;
        }

        .alert-error i {
            color: #ef4444;
        }

        /* Animación del mensaje de éxito */
        @keyframes alertSuccess {
            0% { transform: translateY(-10px); opacity: 0; }
            10% { transform: translateY(0); opacity: 1; }
            90% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(-10px); opacity: 0; }
        }

        #notification-modal .alert-success {
            animation: alertSuccess 5s ease-in-out;
        }

        /* Estilos específicos para el modal de notificación */
        .notification-content {
            max-width: 400px;
            padding: 20px;
            border-left: 5px solid #10b981;
        }

        /* Estilos para el botón de confirmar cambio de contraseña */
        #password-change-form .btn-primary {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        #password-change-form .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* Estilos específicos para el modal de notificación */
        #notification-modal {
            z-index: 2000; /* Asegurar que esté por encima de otros modales */
        }
        
        #notification-modal .modal-content {
            max-width: 450px;
            text-align: left;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        #notification-modal .alert {
            margin-bottom: 0;
            padding: 20px;
            border-radius: 8px;
            border-width: 0;
            border-left-width: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
        <div class="modal-content" style="max-width: 450px; padding: 25px; background-color: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); margin: 0 20px;">
            <span class="close-modal">&times;</span>
            <div id="modal-message"></div>
        </div>
    </div>

    <!-- Modal para cambio de contraseña -->
    <div id="password-modal" class="modal">
        <div class="modal-content password-form">
            <span class="close-modal">&times;</span>
            <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
            <form id="password-change-form">
                <div class="form-group">
                    <label for="current-password">Contraseña Actual:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="current-password" name="current_password" required>
                        <button type="button" class="toggle-password" data-target="current-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new-password">Nueva Contraseña:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new-password" name="new_password" required>
                        <button type="button" class="toggle-password" data-target="new-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-indicator" id="strength-indicator"></div>
                        </div>
                        <span id="strength-text">Fuerza de la contraseña</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirmar Nueva Contraseña:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                        <button type="button" class="toggle-password" data-target="confirm-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <button type="button" class="btn-cancel close-modal">Cancelar</button>
                </div>
            </form>
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
                // Crear el contenido HTML del mensaje
                let iconClass = type === 'success' ? 'check-circle' : 'exclamation-circle';
                let alertClass = 'alert-' + type;
                let alertContent = '';
                
                if (typeof message === 'string') {
                    // Si el mensaje es una cadena simple
                    alertContent = `
                        <div class="alert ${alertClass}">
                            <i class="fas fa-${iconClass}"></i>
                            <div>${message}</div>
                        </div>
                    `;
                } else if (typeof message === 'object') {
                    // Si el mensaje es un objeto con título y texto
                    alertContent = `
                        <div class="alert ${alertClass}">
                            <i class="fas fa-${iconClass}"></i>
                            <div>
                                <strong>${message.title || ''}</strong>
                                <p>${message.text || ''}</p>
                            </div>
                        </div>
                    `;
                }
                
                // Actualizar el contenido del modal
                $('#modal-message').html(alertContent);
                
                // Mostrar el modal
                $('#notification-modal').css('display', 'flex');
                
                // Cerrar automáticamente después de un tiempo
                const timeout = type === 'success' ? 5000 : 7000; // Más tiempo para errores
                
                clearTimeout(window.modalTimeout); // Limpiar timeout previo si existe
                window.modalTimeout = setTimeout(function() {
                    $('#notification-modal').css('display', 'none');
                }, timeout);
                
                // Registrar en la consola para depuración
                console.log('Mostrando modal:', type, message);
            }

            // Cerrar modal al hacer clic en la X o fuera de él
            $('.close-modal').on('click', function() {
                $('#notification-modal').css('display', 'none');
                $('#password-modal').css('display', 'none');
            });

            $(window).on('click', function(e) {
                if ($(e.target).is('.modal')) {
                    $('.modal').css('display', 'none');
                }
            });

            // Mostrar/ocultar contraseña
            $('.toggle-password').on('click', function() {
                const targetId = $(this).data('target');
                const passwordInput = $('#' + targetId);
                const icon = $(this).find('i');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Validación de fuerza de contraseña
            $('#new-password').on('input', function() {
                const password = $(this).val();
                let strength = 0;
                let strengthText = '';
                let color = '';

                // Verificar longitud
                if (password.length >= 8) strength += 1;

                // Verificar letras mayúsculas y minúsculas
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;

                // Verificar números
                if (password.match(/\d/)) strength += 1;

                // Verificar caracteres especiales
                if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

                // Establecer mensaje y color según la fuerza
                switch(strength) {
                    case 0:
                        strengthText = 'Muy débil';
                        color = '#ff4d4d';
                        break;
                    case 1:
                        strengthText = 'Débil';
                        color = '#ffa64d';
                        break;
                    case 2:
                        strengthText = 'Media';
                        color = '#ffff4d';
                        break;
                    case 3:
                        strengthText = 'Fuerte';
                        color = '#4dff4d';
                        break;
                    case 4:
                        strengthText = 'Muy fuerte';
                        color = '#4d4dff';
                        break;
                }

                // Actualizar indicador visual
                $('#strength-indicator').css({
                    'width': (strength * 25) + '%',
                    'background-color': color
                });
                $('#strength-text').text(strengthText).css('color', color);
            });

            // Manejar envío del formulario de cambio de contraseña
            $('#password-change-form').on('submit', function(e) {
                e.preventDefault();
                
                const currentPassword = $('#current-password').val();
                const newPassword = $('#new-password').val();
                const confirmPassword = $('#confirm-password').val();
                
                // Validar que las contraseñas coincidan
                if (newPassword !== confirmPassword) {
                    showModal('Las contraseñas nuevas no coinciden', 'error');
                    return;
                }
                
                // Deshabilitar el botón durante el proceso
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
                
                // Enviar datos al servidor
                $.ajax({
                    url: 'cambiar_password.php',
                    type: 'POST',
                    data: {
                        current_password: currentPassword,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Respuesta recibida:', response); // Depuración
                        
                        if (response.success) {
                            // Cerrar el modal de contraseña
                            $('#password-modal').css('display', 'none');
                            
                            // Mostrar notificación directamente
                            const notificationModal = $('#notification-modal');
                            
                            // Preparar el mensaje de éxito con icono más grande
                            const successMessage = `
                                <div class="alert alert-success" style="display: flex; align-items: center; border-left: 5px solid #10b981;">
                                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-right: 15px; color: #10b981;"></i>
                                    <div>
                                        <strong style="font-size: 1.2rem; display: block; margin-bottom: 5px;">¡Contraseña actualizada correctamente!</strong>
                                        <p>Tu nueva contraseña ha sido guardada de forma segura.</p>
                                    </div>
                                </div>
                            `;
                            
                            // Actualizar el contenido y mostrar el modal
                            $('#modal-message').html(successMessage);
                            notificationModal.css({
                                'display': 'flex',
                                'align-items': 'center',
                                'justify-content': 'center'
                            });
                            
                            // Registrar en la consola para depuración
                            console.log('Modal mostrado - Contraseña actualizada:', response);
                            
                            // Mantener el modal visible por 5 segundos
                            setTimeout(function() {
                                notificationModal.css('display', 'none');
                            }, 5000);
                            
                            // Limpiar el formulario
                            $('#password-change-form')[0].reset();
                        } else {
                            // Mostrar mensaje de error
                            const errorMessage = `
                                <div class="alert alert-error" style="display: flex; align-items: center; border-left: 5px solid #ef4444;">
                                    <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-right: 15px; color: #ef4444;"></i>
                                    <div>
                                        <strong style="font-size: 1.2rem; display: block; margin-bottom: 5px;">Error</strong>
                                        <p>${response.message || 'Error al cambiar la contraseña'}</p>
                                    </div>
                                </div>
                            `;
                            
                            $('#modal-message').html(errorMessage);
                            $('#notification-modal').css({
                                'display': 'flex',
                                'align-items': 'center',
                                'justify-content': 'center'
                            });
                            
                            // Mantener el modal visible por 7 segundos para mensajes de error
                            setTimeout(function() {
                                $('#notification-modal').css('display', 'none');
                            }, 7000);
                        }
                        
                        // Restaurar el botón
                        submitBtn.html(originalBtnText).prop('disabled', false);
                    },
                    error: function() {
                        showModal('Error al procesar la solicitud', 'error');
                        submitBtn.html(originalBtnText).prop('disabled', false);
                    }
                });
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
                        // Mostrar el modal de cambio de contraseña
                        $('#password-modal').css('display', 'flex');
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
                } else if (content === 'billetera') {
                    // Contenido de Billetera
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
  background-color: var(--primary-color); /* Usa la variable CSS primaria */
  color: white;
  border: none;
  border-radius: 50%;
  display: none; /* Oculto por defecto */
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: background-color 0.3s, transform 0.3s;
  z-index: 1000;
}

#scrollToTopBtn:hover {
  background-color: var(--primary-dark); /* Usa la variable CSS primaria oscura */
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
