<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

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

<link rel="stylesheet" href="css/cartera.css">

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
/* Estilos adicionales para los formularios de transacción */
.transaction-forms {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
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
}

.btn-withdraw {
    background-color: #e74c3c;
    color: white;
}

.transaction-item.withdrawal {
    background-color: rgba(231, 76, 60, 0.05);
}

.transaction-item.deposit {
    background-color: rgba(46, 204, 113, 0.05);
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