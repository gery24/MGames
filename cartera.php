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

// Verificar si hay un parámetro de compra exitosa
$mostrarModal = isset($_GET['compra_exitosa']) && $_GET['compra_exitosa'] === 'true';
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
<style>
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

<body class="<?php echo $bodyClass; ?>">
    
    <!-- Modal de pago exitoso -->
    <div id="successModal" class="success-modal <?php echo $mostrarModal ? 'active' : ''; ?>">
        <div class="modal-content">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="modal-title">¡Pago Realizado con Éxito!</h2>
            <p class="modal-message">Tu compra ha sido procesada correctamente. Gracias por tu confianza.</p>
            <button class="modal-btn" onclick="closeModal()">Aceptar</button>
        </div>
    </div>
    
    <div class="wallet-container">
        <?php
        // Mostrar mensaje de éxito si existe en la sesión (mantenemos esto como respaldo)
        if (isset($_SESSION['mensaje'])) {
            echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . $_SESSION['mensaje'] . '</div>';
            unset($_SESSION['mensaje']); // Limpiar el mensaje después de mostrarlo
        }
        
        // Mostrar mensaje de error si existe
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Limpiar el error después de mostrarlo
        }
        ?>
        
        <div class="wallet-header">
            <h1><i class="fas fa-wallet"></i> Mi Cartera</h1>
            <div class="balance-card">
                <div class="balance-label">Saldo actual</div>
                <div class="balance-amount">€<?php echo number_format($saldo, 2); ?></div>
            </div>
        </div>

        <div class="wallet-actions">
            <!-- Formulario para agregar dinero -->
            <div class="action-card deposit-card">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i>
                    <h3>Agregar Dinero</h3>
                </div>
                <form class="transaction-form" method="POST" action="procesar_transaccion.php">
                    <div class="form-group">
                        <label for="deposit-amount"><i class="fas fa-euro-sign"></i> Monto</label>
                        <input type="number" id="deposit-amount" name="monto" placeholder="Cantidad a agregar" min="1" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="deposit-description"><i class="fas fa-comment-alt"></i> Descripción</label>
                        <input type="text" id="deposit-description" name="descripcion" placeholder="Motivo del ingreso" required>
                    </div>
                    <input type="hidden" name="origen" value="cartera">
                    <input type="hidden" name="tipo" value="deposito">
                    <button type="submit" class="btn-action btn-deposit">
                        <i class="fas fa-arrow-circle-up"></i> Agregar a la Cartera
                    </button>
                </form>
            </div>

            <!-- Formulario para retirar dinero -->
            <div class="action-card withdraw-card">
                <div class="card-header">
                    <i class="fas fa-minus-circle"></i>
                    <h3>Retirar Dinero</h3>
                </div>
                <form class="transaction-form" method="POST" action="procesar_retiro.php">
                    <div class="form-group">
                        <label for="withdraw-amount"><i class="fas fa-euro-sign"></i> Monto</label>
                        <input type="number" id="withdraw-amount" name="monto" placeholder="Cantidad a retirar" min="1" max="<?php echo $saldo; ?>" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="withdraw-description"><i class="fas fa-comment-alt"></i> Descripción</label>
                        <input type="text" id="withdraw-description" name="descripcion" placeholder="Motivo del retiro" required>
                    </div>
                    <input type="hidden" name="origen" value="cartera">
                    <button type="submit" class="btn-action btn-withdraw">
                        <i class="fas fa-arrow-circle-down"></i> Retirar de la Cartera
                    </button>
                </form>
            </div>
        </div>

        <div class="transactions-section">
            <h2><i class="fas fa-history"></i> Historial de Transacciones</h2>
            <?php if (empty($transacciones)): ?>
                <div class="empty-transactions">
                    <i class="fas fa-receipt"></i>
                    <p>No hay transacciones registradas.</p>
                </div>
            <?php else: ?>
                <div class="transactions-list">
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
                </div>
            <?php endif; ?>
        </div>
    </div>

<script>
// Función para cerrar el modal
function closeModal() {
    const modal = document.getElementById('successModal');
    modal.classList.remove('active');
    
    // Limpiar la URL para evitar que el modal aparezca de nuevo al recargar
    window.history.replaceState({}, document.title, window.location.pathname);
}

// Verificar si hay un parámetro de éxito en la URL (transacción de cartera)
document.addEventListener('DOMContentLoaded', function() {
    // Si el modal está activo, añadir listener para cerrar con Escape
    if (document.getElementById('successModal').classList.contains('active')) {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        
        // También cerrar al hacer clic fuera del modal
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }
    
    // Verificar si hay un parámetro de éxito en la URL (transacción de cartera)
    if (window.location.search.includes('success=true')) {
        // Mostrar mensaje de éxito
        const modal = document.getElementById('successModal');
        modal.classList.add('active');
        
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
});
</script>

<!-- Botón scroll arriba -->
<button id="scrollToTopBtn" aria-label="Volver arriba">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
       stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up">
    <polyline points="18 15 12 9 6 15"></polyline>
  </svg>
</button>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>
