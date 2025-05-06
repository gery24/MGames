<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

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
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a4af4;
            --secondary-color: #ff6600;
            --dark-color: #333333;
            --light-color: #f5f5f5;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .profile-container {
            display: flex;
            max-width: 1200px;
            margin: 30px auto;
            gap: 30px;
            padding: 0 20px;
        }
        
        /* Sidebar Styles */
        .sidebar {
            flex: 0 0 280px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            height: fit-content;
            position: sticky;
            top: 30px;
        }
        
        .sidebar h2 {
            color: var(--primary-color);
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }
        
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar li {
            margin-bottom: 5px;
        }
        
        .sidebar .menu-option {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--dark-color);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-weight: 500;
        }
        
        .sidebar .menu-option:hover {
            background-color: rgba(74, 74, 244, 0.1);
            color: var(--primary-color);
        }
        
        .sidebar .menu-option.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .sidebar .menu-option i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        .sidebar .danger {
            color: var(--danger-color);
        }
        
        .sidebar .danger:hover {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        /* Main Content Styles */
        .profile-info {
            flex: 1;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            min-height: 500px;
        }
        
        .profile-info h1 {
            color: var(--dark-color);
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            margin-right: 20px;
        }
        
        .profile-details h2 {
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        
        .profile-details p {
            margin: 0;
            color: #666;
        }
        
        .profile-badge {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 8px;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .input-container {
            position: relative;
        }
        
        .input-container input,
        .input-container select,
        .input-container textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }
        
        .input-container input:focus,
        .input-container select:focus,
        .input-container textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 74, 244, 0.1);
            outline: none;
        }
        
        .input-container input[readonly] {
            background-color: #f9f9f9;
            cursor: not-allowed;
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }
        
        button:hover {
            background-color: #3939d0;
        }
        
        button.secondary {
            background-color: #f0f0f0;
            color: var(--dark-color);
        }
        
        button.secondary:hover {
            background-color: #e0e0e0;
        }
        
        button.danger {
            background-color: var(--danger-color);
        }
        
        button.danger:hover {
            background-color: #c0392b;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-error {
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
        }
        
        /* Card Styles */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .card-content {
            padding: 15px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 10px 0;
            color: var(--dark-color);
        }
        
        .card-price {
            font-size: 20px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }
        
        .card-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        /* Table Styles */
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f9f9f9;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-completed {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }
        
        .status-processing {
            background-color: rgba(241, 196, 15, 0.1);
            color: #f39c12;
        }
        
        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        /* Wallet Section */
        .wallet-card {
            background: linear-gradient(135deg, #4a4af4, #3939d0);
            color: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(74, 74, 244, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .wallet-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .wallet-card::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .wallet-balance {
            font-size: 36px;
            font-weight: bold;
            margin: 15px 0;
            position: relative;
            z-index: 1;
        }
        
        .wallet-label {
            font-size: 14px;
            opacity: 0.8;
            position: relative;
            z-index: 1;
        }
        
        .wallet-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 30px;
            opacity: 0.2;
            z-index: 1;
        }
        
        .transaction-list {
            margin-top: 30px;
        }
        
        .transaction-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .transaction-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .transaction-item.withdrawal {
            background-color: rgba(231, 76, 60, 0.05);
            border-left: 3px solid var(--danger-color);
        }
        
        .transaction-item.deposit {
            background-color: rgba(46, 204, 113, 0.05);
            border-left: 3px solid var(--success-color);
        }
        
        .transaction-info {
            flex: 1;
        }
        
        .transaction-title {
            font-weight: 500;
            margin: 0 0 5px 0;
        }
        
        .transaction-date {
            font-size: 14px;
            color: #666;
        }
        
        .transaction-amount {
            font-weight: bold;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .transaction-amount.positive {
            color: var(--success-color);
        }
        
        .transaction-amount.positive::before {
            content: '+';
            margin-right: 2px;
        }
        
        .transaction-amount.negative {
            color: var(--danger-color);
        }
        
        /* Transaction Forms */
        .transaction-forms {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .form-container {
            flex: 1;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .form-container h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
            position: relative;
            padding-left: 30px;
        }
        
        .form-container h3 i {
            position: absolute;
            left: 0;
            top: 3px;
        }
        
        .deposit {
            background-color: rgba(74, 74, 244, 0.03);
            border-top: 4px solid var(--primary-color);
        }
        
        .deposit h3 {
            color: var(--primary-color);
        }
        
        .withdraw {
            background-color: rgba(231, 76, 60, 0.03);
            border-top: 4px solid var(--danger-color);
        }
        
        .withdraw h3 {
            color: var(--danger-color);
        }
        
        .btn-deposit {
            background-color: var(--primary-color);
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        
        .btn-deposit:hover {
            background-color: #3939d0;
        }
        
        .btn-withdraw {
            background-color: var(--danger-color);
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        
        .btn-withdraw:hover {
            background-color: #c0392b;
        }
        
        /* Loading Animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
        
        .loading-spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid var(--primary-color);
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .profile-container {
                flex-direction: column;
            }
            
            .sidebar {
                flex: none;
                width: 100%;
                position: static;
                margin-bottom: 20px;
            }
            
            .sidebar ul {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .sidebar li {
                margin-bottom: 0;
            }
            
            .sidebar .menu-option {
                padding: 10px 15px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .card-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .transaction-forms {
                flex-direction: column;
            }
        }
        
        @media (max-width: 576px) {
            .profile-info {
                padding: 20px;
            }
            
            .sidebar ul {
                flex-direction: column;
            }
            
            .card-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .transaction-item {
            animation: fadeIn 0.3s ease-out;
        }
        
        .transaction-item:nth-child(2) { animation-delay: 0.1s; }
        .transaction-item:nth-child(3) { animation-delay: 0.2s; }
        .transaction-item:nth-child(4) { animation-delay: 0.3s; }
        .transaction-item:nth-child(5) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="content">
        <div class="profile-container">
            <!-- Sidebar con opciones de menú -->
            <div class="sidebar">
                <h2>Mi Cuenta</h2>
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
                                </div  required>
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