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
    
    // Obtener productos de segunda mano del usuario desde la tabla segunda_mano
    $stmt_mis_productos_segunda_mano = $pdo->prepare("SELECT * FROM segunda_mano WHERE usuario_id = ?");
    $stmt_mis_productos_segunda_mano->execute([$usuario['id']]);
    $mis_productos_segunda_mano = $stmt_mis_productos_segunda_mano->fetchAll(PDO::FETCH_ASSOC);
    
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
        /* Variables CSS modernas */
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #a5b4fc;
            --secondary-color: #8b5cf6;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --admin-color: #dc2626;
            --admin-bg: #fef2f2;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Fondo rojo para administradores */
        body.admin {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
        }

        .content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 2rem;
            min-height: calc(100vh - 200px);
        }

        /* Sidebar moderno */
        .sidebar {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-xl);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .user-info {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-light);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50% !important; /* Forzar que sea circular */
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 2rem;
            margin: 0 auto 1rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .profile-avatar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.2), transparent);
            border-radius: 50% !important; /* Asegurar que el efecto también sea circular */
        }

        body.admin .profile-avatar {
            background: linear-gradient(135deg, var(--admin-color), #dc2626);
            color: white;
        }

        .user-details h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .user-role {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-md);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .user-role.cliente {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            color: white;
        }

        .user-role.admin {
            background: linear-gradient(135deg, var(--admin-color), #dc2626);
            color: white;
        }

        /* Navegación del perfil */
        .profile-nav ul {
            list-style: none;
        }

        .profile-nav li {
            margin-bottom: 0.5rem;
        }

        .menu-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .menu-option:hover::before {
            left: 100%;
        }

        .menu-option:hover {
            background: var(--bg-tertiary);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .menu-option.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .menu-option.danger {
            color: var(--error-color);
        }

        .menu-option.danger:hover {
            background: var(--admin-bg);
            color: var(--admin-color);
        }

        .divider {
            height: 1px;
            background: var(--border-light);
            margin: 1rem 0;
        }

        /* Contenido principal */
        .profile-content {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            box-shadow: var(--shadow-xl);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-height: 600px;
        }

        .profile-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Cards modernas */
        .card {
            background: var(--bg-primary);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Formularios modernos */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            z-index: 2;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.5rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-secondary);
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background: var(--bg-primary);
        }

        /* Botones modernos */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
            padding: 0.875rem 2rem;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        /* Header del perfil */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--radius-xl);
            color: white;
            box-shadow: var(--shadow-lg);
        }

        .profile-header .profile-avatar {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50% !important; /* Forzar circular */
        }

        .profile-details h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .user-email {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }

        .profile-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        /* Alertas modernas */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left-color: var(--success-color);
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-left-color: var(--error-color);
        }

        /* ESTILOS ESPECÍFICOS PARA BILLETERA */
        
        /* Tarjeta de saldo principal */
        .wallet-summary {
            margin-bottom: 2rem;
        }

        .balance-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .balance-label {
            font-size: 1.125rem;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .balance-amount {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Formularios de acciones de billetera */
        .wallet-actions-forms {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .deposit-card::before {
            background: linear-gradient(90deg, var(--success-color), #34d399);
        }

        .withdraw-card::before {
            background: linear-gradient(90deg, var(--warning-color), #fbbf24);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .card-header i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .deposit-card .card-header i {
            color: var(--success-color);
        }

        .withdraw-card .card-header i {
            color: var(--warning-color);
        }

        .card-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .transaction-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .transaction-form .form-group {
            margin-bottom: 0;
        }

        .transaction-form label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .transaction-form input {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-secondary);
        }

        .transaction-form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background: var(--bg-primary);
        }

        .btn-action {
            width: 100%;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: var(--shadow-md);
        }

        .btn-deposit {
            background: linear-gradient(135deg, var(--success-color), #34d399);
            color: white;
        }

        .btn-deposit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-withdraw {
            background: linear-gradient(135deg, var(--warning-color), #fbbf24);
            color: white;
        }

        .btn-withdraw:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Lista de transacciones */
        .transactions-list {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .transactions-list::-webkit-scrollbar {
            width: 6px;
        }

        .transactions-list::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 3px;
        }

        .transactions-list::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .transactions-list::-webkit-scrollbar-thumb:hover {
            background: var(--text-light);
        }

        .transaction-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .transaction-item:hover {
            background: var(--bg-primary);
            box-shadow: var(--shadow-md);
            transform: translateX(4px);
        }

        .transaction-item.deposit {
            border-left-color: var(--success-color);
        }

        .transaction-item.withdrawal {
            border-left-color: var(--warning-color);
        }

        .transaction-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .transaction-item.deposit .transaction-icon {
            background: linear-gradient(135deg, var(--success-color), #34d399);
            color: white;
        }

        .transaction-item.withdrawal .transaction-icon {
            background: linear-gradient(135deg, var(--warning-color), #fbbf24);
            color: white;
        }

        .transaction-details {
            flex: 1;
            min-width: 0;
        }

        .transaction-description {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .transaction-date {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin: 0;
        }

        .transaction-item .amount {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            flex-shrink: 0;
        }

        .transaction-item.deposit .amount {
            color: var(--success-color);
        }

        .transaction-item.withdrawal .amount {
            color: var(--warning-color);
        }

        .empty-transactions {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }

        .empty-transactions i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-transactions p {
            font-size: 1.125rem;
            margin: 0;
        }

        /* Responsive para billetera */
        @media (max-width: 768px) {
            .wallet-actions-forms {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .balance-amount {
                font-size: 2.5rem;
            }

            .transaction-item {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .transaction-details {
                text-align: center;
            }
        }

        /* Estilos para Segunda Mano */
        .segunda-mano-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-light);
        }

        .segunda-mano-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .segunda-mano-card {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
        }

        .segunda-mano-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .segunda-mano-image-container {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--bg-secondary), var(--bg-tertiary));
        }

        .segunda-mano-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .segunda-mano-card:hover .segunda-mano-image {
            transform: scale(1.05);
        }

        .segunda-mano-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary-color);
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-md);
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .segunda-mano-content {
            padding: 1.5rem;
        }

        .segunda-mano-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .segunda-mano-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 1rem 0;
        }

        .segunda-mano-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .segunda-mano-actions {
            display: flex;
            gap: 0.75rem;
        }

        .segunda-mano-btn {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .segunda-mano-btn-edit {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .segunda-mano-btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .segunda-mano-btn-delete {
            background: var(--admin-bg);
            color: var(--admin-color);
            border: 1px solid #fecaca;
        }

        .segunda-mano-btn-delete:hover {
            background: var(--admin-color);
            color: white;
            transform: translateY(-2px);
        }

        .segunda-mano-empty {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--bg-secondary);
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-xl);
            margin-top: 2rem;
        }

        .segunda-mano-empty-icon {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }

        .segunda-mano-empty h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .segunda-mano-empty p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.125rem;
        }

        /* Lista de deseos */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .product-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-actions {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-actions {
            opacity: 1;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .action-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Estados vacíos */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.125rem;
        }

        /* Loading */
        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Modales */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--bg-primary);
            border-radius: var(--radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 500px;
            margin: 2rem;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-secondary);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--text-secondary);
        }

        .close-modal:hover {
            background: var(--error-color);
            color: white;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .profile-container {
                grid-template-columns: 280px 1fr;
                gap: 1.5rem;
            }
            
            .content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .sidebar {
                position: static;
                order: 2;
            }
            
            .profile-content {
                order: 1;
                padding: 1.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .segunda-mano-grid,
            .card-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .content {
                padding: 1rem;
            }
            
            .profile-content {
                padding: 1rem;
            }
            
            .sidebar {
                padding: 1rem;
            }
            
            .button-group {
                flex-direction: column;
            }
        }

        /* Animaciones adicionales */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card,
        .segunda-mano-card,
        .product-card {
            animation: fadeInUp 0.5s ease forwards;
        }

        /* Efectos de hover mejorados */
        .menu-option,
        .btn-primary,
        .btn-secondary,
        .segunda-mano-btn,
        .action-btn {
            position: relative;
            overflow: hidden;
        }

        .menu-option::after,
        .btn-primary::after,
        .btn-secondary::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .menu-option:active::after,
        .btn-primary:active::after,
        .btn-secondary:active::after {
            width: 300px;
            height: 300px;
        }

        /* Asegurar que todos los avatares sean circulares */
        [class*="avatar"], 
        [class*="profile-avatar"] {
            border-radius: 50% !important;
            overflow: hidden;
        }
    </style>
</head>
<body class="<?php echo $bodyClass; ?>">
    <?php require_once 'includes/header.php'; ?>

    <div class="content">
        <div class="profile-container">
            <!-- Sidebar moderno -->
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
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <p>Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mensajes -->
    <div id="notification-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal">
                <i class="fas fa-times"></i>
            </button>
            <div id="modal-message"></div>
        </div>
    </div>

    <!-- Modal para cambio de contraseña -->
    <div id="password-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal">
                <i class="fas fa-times"></i>
            </button>
            <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
            <form id="password-change-form">
                <div class="form-group">
                    <label for="current-password">Contraseña Actual:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="current-password" name="current_password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new-password">Nueva Contraseña:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new-password" name="new_password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirmar Nueva Contraseña:</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <button type="button" class="btn-secondary close-modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        $(document).ready(function() {
            let currentSection = 'ajustes';

            // Cargar contenido inicial
            loadContent('ajustes');

            // Manejar navegación
            $('.menu-option').on('click', function(e) {
                e.preventDefault();
                
                if ($(this).attr('href') !== '#') return;
                
                $('.menu-option').removeClass('active');
                $(this).addClass('active');
                
                const content = $(this).data('content');
                currentSection = content;
                loadContent(content);
            });

            // Función para mostrar modales
            function showModal(message, type = 'success') {
                const iconClass = type === 'success' ? 'check-circle' : 'exclamation-circle';
                const alertClass = 'alert-' + type;
                
                const alertContent = `
                    <div class="alert ${alertClass}">
                        <i class="fas fa-${iconClass}"></i>
                        <div>${message}</div>
                    </div>
                `;
                
                $('#modal-message').html(alertContent);
                $('#notification-modal').css('display', 'flex');
                
                setTimeout(() => {
                    $('#notification-modal').css('display', 'none');
                }, type === 'success' ? 3000 : 5000);
            }

            // Cerrar modales
            $('.close-modal').on('click', function() {
                $('.modal').css('display', 'none');
            });

            $(window).on('click', function(e) {
                if ($(e.target).is('.modal')) {
                    $('.modal').css('display', 'none');
                }
            });

            // Función para cargar contenido
            function loadContent(content) {
                $('#dynamic-content').html(`
                    <div class="loading">
                        <div class="loading-spinner"></div>
                        <p>Cargando...</p>
                    </div>
                `);

                setTimeout(() => {
                    renderContent(content);
                }, 300);
            }

            // Función para renderizar contenido
            function renderContent(content) {
                if (content === 'ajustes') {
                    $('#dynamic-content').html(`
                        <h1><i class="fas fa-user-cog"></i> Ajustes de Cuenta</h1>
                        
                        <div class="profile-header">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                            </div>
                            <div class="profile-details">
                                <h2><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h2>
                                <p class="user-email"><?php echo htmlspecialchars($usuario['email']); ?></p>
                                <div class="profile-badge">
                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3><i class="fas fa-user"></i> Información Personal</h3>
                            <form action="update_profile.php" method="POST">
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
                    
                    $('#change-password').on('click', function() {
                        $('#password-modal').css('display', 'flex');
                    });
                    
                } else if (content === 'wishlist') {
                    let wishlistHTML = `<h1><i class="fas fa-heart"></i> Mi Lista de Deseos</h1>`;
                    
                    <?php if (!empty($wishlist)): ?>
                        wishlistHTML += `<div class="card-grid">`;
                        <?php foreach ($wishlist as $producto): ?>
                            wishlistHTML += `
                                <div class="product-card">
                                    <div class="product-image">
                                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        <div class="product-actions">
                                            <a href="producto.php?id=<?php echo $producto['id']; ?>" class="action-btn">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="eliminar_deseo.php" style="display: inline;">
                                                <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                                <button type="submit" class="action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                        <p class="product-price">€<?php echo number_format($producto['precio'], 2); ?></p>
                                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn-primary">
                                            <i class="fas fa-eye"></i> Ver Detalles
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
                                    <i class="fas fa-heart-broken"></i>
                                </div>
                                <h3>Tu lista de deseos está vacía</h3>
                                <p>Explora nuestra tienda y añade tus juegos favoritos</p>
                                <a href="index.php" class="btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Ir a la tienda
                                </a>
                            </div>
                        `;
                    <?php endif; ?>
                    
                    $('#dynamic-content').html(wishlistHTML);
                    
                } else if (content === 'segunda_mano') {
                    let segundaManoHTML = `
                        <h1><i class="fas fa-handshake"></i> Mis Productos de Segunda Mano</h1>
                        
                        <div class="card">
                            <div class="segunda-mano-header">
                                <h3><i class="fas fa-store"></i> Gestión de Productos</h3>
                                <button id="add-second-hand" class="btn-primary">
                                    <i class="fas fa-plus"></i> Publicar Nuevo Producto
                                </button>
                            </div>
                    `;
                    
                    <?php if (!empty($mis_productos_segunda_mano)): ?>
                        segundaManoHTML += `<div class="segunda-mano-grid">`;
                        <?php foreach ($mis_productos_segunda_mano as $producto): ?>
                            segundaManoHTML += `
                                <div class="segunda-mano-card">
                                    <div class="segunda-mano-image-container">
                                        <img src="${<?php echo json_encode(htmlspecialchars($producto['imagen'] ?? 'images/default.jpg')); ?>}" 
                                             alt="${<?php echo json_encode(htmlspecialchars($producto['nombre'] ?? 'Producto')); ?>}" 
                                             class="segunda-mano-image">
                                        <div class="segunda-mano-status">${<?php echo json_encode(htmlspecialchars($producto['estado'] ?? 'Nuevo')); ?>}</div>
                                    </div>
                                    <div class="segunda-mano-content">
                                        <h3 class="segunda-mano-title">${<?php echo json_encode(htmlspecialchars($producto['nombre'] ?? 'Producto')); ?>}</h3>
                                        <div class="segunda-mano-price">€${parseFloat(<?php echo $producto['precio'] ?? 0; ?>).toFixed(2)}</div>
                                        <p class="segunda-mano-description">${<?php echo json_encode(htmlspecialchars($producto['descripcion'] ?? 'Sin descripción')); ?>}</p>
                                        <div class="segunda-mano-actions">
                                            <a href="editar_segunda_mano.php?id=${<?php echo $producto['id'] ?? 0; ?>}" class="segunda-mano-btn segunda-mano-btn-edit">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <button class="segunda-mano-btn segunda-mano-btn-delete eliminar-segunda-mano" data-id="${<?php echo $producto['id'] ?? 0; ?>}">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        <?php endforeach; ?>
                        segundaManoHTML += `</div>`;
                    <?php else: ?>
                        segundaManoHTML += `
                            <div class="segunda-mano-empty">
                                <div class="segunda-mano-empty-icon">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <h3>No tienes productos publicados</h3>
                                <p>Publica tus juegos usados y gana dinero extra</p>
                                <button id="add-second-hand-empty" class="btn-primary">
                                    <i class="fas fa-plus"></i> Publicar Mi Primer Producto
                                </button>
                            </div>
                        `;
                    <?php endif; ?>

                    segundaManoHTML += `</div>`;
                    $('#dynamic-content').html(segundaManoHTML);
                    
                    // Event handlers para segunda mano
                    $('#add-second-hand, #add-second-hand-empty').on('click', function() {
                        window.location.href = 'agregar_segunda_mano.php';
                    });

                    $('#dynamic-content').on('click', '.eliminar-segunda-mano', function() {
                        const productoId = $(this).data('id');
                        const productCard = $(this).closest('.segunda-mano-card');

                        if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                            $.ajax({
                                url: 'eliminar_segunda_mano.php',
                                type: 'POST',
                                data: { id: productoId },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        productCard.remove();
                                        showModal('Producto eliminado correctamente', 'success');
                                        
                                        if ($('.segunda-mano-card').length === 0) {
                                            $('.segunda-mano-grid').replaceWith(`
                                                <div class="segunda-mano-empty">
                                                    <div class="segunda-mano-empty-icon">
                                                        <i class="fas fa-handshake"></i>
                                                    </div>
                                                    <h3>No tienes productos publicados</h3>
                                                    <p>Publica tus juegos usados y gana dinero extra</p>
                                                    <button id="add-second-hand-empty" class="btn-primary">
                                                        <i class="fas fa-plus"></i> Publicar Mi Primer Producto
                                                    </button>
                                                </div>
                                            `);
                                        }
                                    } else {
                                        showModal(response.message || 'Error al eliminar el producto', 'error');
                                    }
                                },
                                error: function() {
                                    showModal('Error al comunicarse con el servidor', 'error');
                                }
                            });
                        }
                    });
                    
                } else if (content === 'billetera') {
                    // Contenido de Billetera
                    let billeteraHTML = `
                        <h1><i class="fas fa-wallet"></i> Mi Billetera</h1>
                        
                        <div class="card">
                            <h3><i class="fas fa-info-circle"></i> Información de Saldo</h3>
                            <div class="wallet-summary">
                                <div class="balance-card">
                                    <div class="balance-label">Saldo actual</div>
                                    <div class="balance-amount">€<?php echo number_format($usuario['cartera'], 2); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <h3><i class="fas fa-exchange-alt"></i> Acciones de Cartera</h3>
                            <div class="wallet-actions-forms">
                                <!-- Formulario para agregar dinero -->
                                <div class="action-card deposit-card">
                                    <div class="card-header">
                                        <i class="fas fa-plus-circle"></i>
                                        <h3>Agregar Dinero</h3>
                                    </div>
                                    <form id="deposit-form-perfil" class="transaction-form">
                                        <div class="form-group">
                                            <label for="deposit-amount-perfil"><i class="fas fa-euro-sign"></i> Monto</label>
                                            <input type="number" id="deposit-amount-perfil" name="monto" placeholder="Cantidad a agregar" min="1" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="deposit-description-perfil"><i class="fas fa-comment-alt"></i> Descripción</label>
                                            <input type="text" id="deposit-description-perfil" name="descripcion" placeholder="Motivo del ingreso" required>
                                        </div>
                                        <input type="hidden" name="origen" value="perfil">
                                        <input type="hidden" name="tipo" value="deposito">
                                        <button type="submit" class="btn-action btn-deposit">
                                            <i class="fas fa-arrow-circle-up"></i> Agregar Fondos
                                        </button>
                                    </form>
                                </div>

                                <!-- Formulario para retirar dinero -->
                                <div class="action-card withdraw-card">
                                    <div class="card-header">
                                        <i class="fas fa-minus-circle"></i>
                                        <h3>Retirar Dinero</h3>
                                    </div>
                                    <form id="withdraw-form-perfil" class="transaction-form">
                                        <div class="form-group">
                                            <label for="withdraw-amount-perfil"><i class="fas fa-euro-sign"></i> Monto</label>
                                            <input type="number" id="withdraw-amount-perfil" name="monto" placeholder="Cantidad a retirar" min="1" max="<?php echo $usuario['cartera']; ?>" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="withdraw-description-perfil"><i class="fas fa-comment-alt"></i> Descripción</label>
                                            <input type="text" id="withdraw-description-perfil" name="descripcion" placeholder="Motivo del retiro" required>
                                        </div>
                                        <input type="hidden" name="origen" value="perfil">
                                        <button type="submit" class="btn-action btn-withdraw">
                                            <i class="fas fa-arrow-circle-down"></i> Retirar Fondos
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <h3><i class="fas fa-history"></i> Historial de Transacciones</h3>
                            <div class="transactions-list">
                                <?php if (empty($transacciones)): ?>
                                    <div class="empty-transactions">
                                        <i class="fas fa-receipt"></i>
                                        <p>No hay transacciones registradas.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($transacciones as $transaccion): ?>
                                        <div class="transaction-item <?php echo $transaccion['monto'] < 0 ? 'withdrawal' : 'deposit'; ?>">
                                            <div class="transaction-icon">
                                                <i class="fas <?php echo $transaccion['monto'] < 0 ? 'fa-arrow-down' : 'fa-arrow-up'; ?>"></i>
                                            </div>
                                            <div class="transaction-details">
                                                <p class="transaction-description"><?php echo htmlspecialchars($transaccion['descripcion']); ?></p>
                                                <p class="transaction-date"><?php echo date('d/m/Y H:i', strtotime($transaccion['fecha'])); ?></p>
                                            </div>
                                            <p class="amount">€<?php echo number_format($transaccion['monto'], 2); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    `;
                    
                    $('#dynamic-content').html(billeteraHTML);

                    // --- JavaScript para manejar los formularios de la billetera (AJAX) ---

                    // Manejar el envío del formulario de depósito con AJAX
                    $('#deposit-form-perfil').on('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = $(this).serialize();
                        const submitBtn = $(this).find('button[type="submit"]');
                        const originalBtnText = submitBtn.html();

                        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
                        
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
                                    submitBtn.html(originalBtnText).prop('disabled', false);
                                }
                            },
                            error: function() {
                                showModal('Error al comunicarse con el servidor', 'error');
                                submitBtn.html(originalBtnText).prop('disabled', false);
                            }
                        });
                    });

                    // Manejar el envío del formulario de retiro con AJAX
                    $('#withdraw-form-perfil').on('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = $(this).serialize();
                        const submitBtn = $(this).find('button[type="submit"]');
                        const originalBtnText = submitBtn.html();

                        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
                        
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
                                    submitBtn.html(originalBtnText).prop('disabled', false);
                                }
                            },
                            error: function() {
                                showModal('Error al comunicarse con el servidor', 'error');
                                submitBtn.html(originalBtnText).prop('disabled', false);
                            }
                        });
                    });

                    // --- Fin JavaScript formularios billetera ---

                } else {
                    $('#dynamic-content').html(`
                        <h1>${content.charAt(0).toUpperCase() + content.slice(1).replace(/_/g, ' ')}</h1>
                        
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h3>Sección en desarrollo</h3>
                            <p>Esta funcionalidad estará disponible próximamente</p>
                        </div>
                    `);
                }
            }

            // Event handlers
            $('#logout').on('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                    window.location.href = 'logout.php';
                }
            });

            $('#delete-account').on('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')) {
                    window.location.href = 'eliminar_cuenta.php';
                }
            });

            // Formulario de cambio de contraseña
            $('#password-change-form').on('submit', function(e) {
                e.preventDefault();
                
                const currentPassword = $('#current-password').val();
                const newPassword = $('#new-password').val();
                const confirmPassword = $('#confirm-password').val();
                
                if (newPassword !== confirmPassword) {
                    showModal('Las contraseñas no coinciden', 'error');
                    return;
                }
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
                
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
                        if (response.success) {
                            $('#password-modal').css('display', 'none');
                            showModal('Contraseña actualizada correctamente', 'success');
                            $('#password-change-form')[0].reset();
                        } else {
                            showModal(response.message || 'Error al cambiar la contraseña', 'error');
                        }
                        submitBtn.html(originalText);
                    },
                    error: function() {
                        showModal('Error al procesar la solicitud', 'error');
                        submitBtn.html(originalText);
                    }
                });
            });
        });
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>

¡Perfecto! He añadido estilos hermosos y modernos específicamente para la sección de billetera que incluyen:

## 🎨 **Diseño Elegante de Billetera:**

### 💳 **Tarjeta de Saldo Principal:**
- **Gradiente llamativo** con efectos de luz
- **Tipografía grande** para el saldo
- **Efectos visuales** con pseudo-elementos

### 📊 **Formularios de Transacciones:**
- **Tarjetas separadas** para depósito y retiro
- **Colores diferenciados** (verde para depósito, naranja para retiro)
- **Iconos descriptivos** y headers elegantes
- **Botones con gradientes** específicos para cada acción

### 📋 **Historial de Transacciones:**
- **Lista scrolleable** con scrollbar personalizado
- **Items con iconos** circulares diferenciados
- **Colores semánticos** (verde para ingresos, naranja para retiros)
- **Efectos hover** suaves y transiciones

### ✨ **Características Especiales:**
- **Completamente responsive** para móviles
- **Animaciones fluidas** en todos los elementos
- **Estados vacíos** elegantes
- **Integración perfecta** con el diseño existente
- **Funcionalidad AJAX** mantenida

El diseño mantiene la coherencia visual con el resto del perfil mientras hace que la gestión de la billetera sea visualmente atractiva y fácil de usar. 💰✨
