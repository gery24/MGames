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

    // Aquí podrías agregar una consulta para obtener las transacciones si las tienes
    $stmt_transacciones = $pdo->prepare("SELECT * FROM transacciones WHERE usuario_id = ?");
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

    <form class="transaction-form" method="POST" action="procesar_transaccion.php">
        <input type="number" name="monto" placeholder="Monto a agregar" required>
        <input type="text" name="descripcion" placeholder="Descripción" required>
        <button type="submit">Agregar a la Cartera</button>
    </form>

    <div class="transaction-list">
        <h2>Transacciones</h2>
        <?php if (empty($transacciones)): ?>
            <p>No hay transacciones registradas.</p>
        <?php else: ?>
            <?php foreach ($transacciones as $transaccion): ?>
                <div class="transaction-item">
                    <p><?php echo htmlspecialchars($transaccion['descripcion']); ?></p>
                    <p class="amount">€<?php echo number_format($transaccion['monto'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 