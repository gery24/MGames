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

$userId = $_SESSION['usuario']['id'];

try {
    // Obtener el saldo de la cartera del usuario
    $stmt = $pdo->prepare("SELECT cartera FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuario no encontrado.");
    }

    $saldo = $usuario['cartera'];

    // Obtener las transacciones del usuario
    $stmt_transacciones = $pdo->prepare("SELECT * FROM transacciones WHERE usuario_id = ? ORDER BY fecha DESC");
    $stmt_transacciones->execute([$userId]);
    $transacciones = $stmt_transacciones->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}

$titulo = "Cartera - MGames";
 require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/cartera.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body class="<?php echo $bodyClass; ?>">
    
    <div class="content">
        <h1>Cartera</h1>
        <div class="balance">Saldo actual: €<?php echo number_format($saldo, 2); ?></div>

        <div class="transaction-forms">
            <!-- Formulario para agregar dinero -->
            <div class="form-container deposit">
                <h3>Agregar Dinero</h3>
                <form class="transaction-form" method="POST" action="procesar_transaccion.php">
                    <input type="number" name="monto" placeholder="Monto a agregar" min="1" step="0.01" required>
                    <input type="text" name="descripcion" placeholder="Descripción" required>
                    <input type="hidden" name="origen" value="cartera">
                    <input type="hidden" name="tipo" value="deposito">
                    <button type="submit" class="btn-deposit">Agregar a la Cartera</button>
                </form>
            </div>

            <!-- Formulario para retirar dinero -->
            <div class="form-container withdraw">
                <h3>Retirar Dinero</h3>
                <form class="transaction-form" method="POST" action="procesar_retiro.php">
                    <input type="number" name="monto" placeholder="Monto a retirar" min="1" max="<?php echo $saldo; ?>" step="0.01" required>
                    <input type="text" name="descripcion" placeholder="Descripción" required>
                    <input type="hidden" name="origen" value="cartera">
                    <button type="submit" class="btn-withdraw">Retirar de la Cartera</button>
                </form>
            </div>
        </div>

        <div class="transaction-list">
            <h2>Transacciones</h2>
            <?php if (empty($transacciones)): ?>
                <p>No hay transacciones registradas.</p>
            <?php else: ?>
                <?php foreach ($transacciones as $transaccion): ?>
                    <div class="transaction-item <?php echo $transaccion['monto'] < 0 ? 'withdrawal' : 'deposit'; ?>">
                        <p><?php echo htmlspecialchars($transaccion['descripcion']); ?></p>
                        <p class="amount">€<?php echo number_format($transaccion['monto'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

<style>
/* Estilos del header */
.site-header {
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 8px 0;
    position: relative;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: #7e22ce;
    text-decoration: none;
}

body.admin .logo span {
    color: #ff0000;
}

.main-nav {
    display: flex;
    align-items: center;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 30px;
    margin: 0;
    padding: 0;
}

.nav-links li a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.nav-links li a:hover {
    color: #7e22ce;
}

body.admin .nav-links li a:hover {
    color: #ff0000;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-icon {
    color: #333;
    font-size: 1.2rem;
    transition: color 0.3s;
    text-decoration: none;
    position: relative;
}

.header-icon:hover {
    color: #7e22ce;
}

body.admin .header-icon:hover {
    color: #ff0000;
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #7e22ce;
    color: white;
    font-size: 0.75rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

body.admin .badge {
    background-color: #ff0000;
}

.balance-indicator {
    position: absolute;
    top: -8px;
    right: -20px;
    background-color: #10b981;
    color: white;
    font-size: 0.75rem;
    padding: 0.1rem 0.4rem;
    border-radius: 0.25rem;
    white-space: nowrap;
}

.profile-dropdown {
    position: relative;
}



/* Estilo para el avatar - ahora siempre redondo */
.avatar-square {
    width: 32px;
    height: 32px;
    border-radius: 50%; /* Redondo en lugar de cuadrado */
    background-color: #7e22ce;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    border: none;
}

.admin-avatar {
    background-color: #ff0000;
}

.dropdown-content {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 0.5rem;
    padding: 0.5rem 0;
    z-index: 100;
    display: none;
}

.profile-dropdown:hover .dropdown-content {
    display: block;
}

.dropdown-content a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: #1f2937;
    text-decoration: none;
    transition: background-color 0.3s;
}

.dropdown-content a:hover {
    background-color: #f3f4f6;
    color: #7e22ce;
}

body.admin .dropdown-content a:hover {
    color: #ff0000;
}

.dropdown-content a i {
    width: 20px;
    text-align: center;
}

.admin-dropdown {
    border: 2px solid #ff0000;
}

.admin-dropdown a.admin-link {
    background-color: #ff0000;
    color: white;
}

.admin-dropdown a.admin-link:hover {
    background-color: #cc0000;
    color: white;
}

.auth-buttons {
    display: flex;
    gap: 10px;
}

.auth-buttons .btn {
    padding: 6px 20px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    width: auto;
}

.auth-buttons .btn-primary {
    background-color: #7e22ce;
    color: white;
    border: none;
}

.auth-buttons .btn-primary:hover {
    background-color: #6b21a8;
}

.auth-buttons .btn-outline {
    background-color: transparent;
    color: #7e22ce;
    border: 1px solid #7e22ce;
}

.auth-buttons .btn-outline:hover {
    background-color: rgba(126, 34, 206, 0.1);
}

.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: #333;
    font-size: 1.5rem;
    cursor: pointer;
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: block;
    }
    
    .nav-links {
        display: none;
    }
    
    .header-actions {
        gap: 0.5rem;
    }
    
    .auth-buttons {
        display: none;
    }
}

/* Estilos adicionales para los formularios de transacción */
.transaction-forms {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: purple;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.form-container {
    flex: 1;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.form-container h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
}

.deposit {
    background-color: #e0f7fa;
    border-left: 4px solid #2563eb;
}

.withdraw {
    background-color: #fff5f5;
    border-left: 4px solid #e74c3c;
}

.transaction-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn-deposit {
    background-color: #2563eb;
    color: white;
    padding: 0.8rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-withdraw {
    background-color: #e74c3c;
    color: white;
    padding: 0.8rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.transaction-item.withdrawal {
    background-color: rgba(231, 76, 60, 0.05);
}

.transaction-item.deposit {
    background-color: rgba(46, 204, 113, 0.05);
}

/* Estilos para administradores */
body.admin h1 {
    color: #ff0000;
}

body.admin .balance {
    background-color: #fff0f0;
}

body.admin .deposit {
    border-left: 4px solid #ff0000;
    background-color: #fff0f0;
}

body.admin .withdraw {
    border-left: 4px solid #cc0000;
}

body.admin .btn-deposit {
    background-color: #ff0000;
}

body.admin .btn-deposit:hover {
    background-color: #cc0000;
}

body.admin .btn-withdraw {
    background-color: #cc0000;
}

body.admin .btn-withdraw:hover {
    background-color: #990000;
}

body.admin .transaction-item .amount {
    color: #ff0000;
}

body.admin .transaction-item.deposit {
    background-color: rgba(255, 0, 0, 0.05);
    border-left: 3px solid #ff0000;
}

body.admin .transaction-item.withdrawal {
    background-color: rgba(204, 0, 0, 0.05);
    border-left: 3px solid #cc0000;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .transaction-forms {
        flex-direction: column;
    }
}
</style>

<script>
// Verificar si hay un parámetro de éxito en la URL
if (window.location.search.includes('success=true')) {
    // Mostrar mensaje de éxito
    alert('Transacción realizada con éxito');
    
    // Limpiar la URL
    window.history.replaceState({}, document.title, window.location.pathname);
}

// Verificar si hay un parámetro de error en la URL
if (window.location.search.includes('error=')) {
    // Obtener el mensaje de error
    const urlParams = new URLSearchParams(window.location.search);
    const errorMsg = urlParams.get('error');
    
    // Mostrar mensaje de error
    alert('Error: ' + decodeURIComponent(errorMsg));
    
    // Limpiar la URL
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>

<?php require_once 'includes/footer.php'; ?>
