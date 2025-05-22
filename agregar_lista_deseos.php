<?php
session_start();
require_once 'config/database.php';

// Función para manejar errores
function handleError($message) {
    error_log($message);
    $_SESSION['error'] = $message;
    // Redirigir a la página anterior o a una página de error genérica
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header('Location: ' . $referer);
    exit;
}

// Asegurarse de que el usuario esté logueado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    // Si no está logueado, redirigir al login o mostrar un mensaje
    $_SESSION['error'] = 'Debes iniciar sesión para añadir productos a la lista de deseos.';
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        // Lógica para añadir a la lista de deseos
        $producto_id = intval($_POST['id']);

        try {
            // Verificar si el producto ya está en la lista de deseos para este usuario
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM lista_deseos WHERE usuario_id = ? AND producto_id = ?");
            $stmt_check->execute([$usuario_id, $producto_id]);
            $count = $stmt_check->fetchColumn();

            if ($count == 0) {
                // Si el producto no está en la lista, añadirlo
                $stmt_insert = $pdo->prepare("INSERT INTO lista_deseos (usuario_id, producto_id, fecha_agregado) VALUES (?, ?, CURRENT_TIMESTAMP)");
                $stmt_insert->execute([$usuario_id, $producto_id]);
                $_SESSION['success'] = 'Producto añadido a la lista de deseos correctamente.';
            } else {
                // Si ya está en la lista, informar al usuario
                $_SESSION['warning'] = 'El producto ya está en tu lista de deseos.';
            }

            // Redirigir a la página de lista de deseos o de vuelta al producto
            $redirect_to = $_POST['redirect_to'] ?? 'lista_deseos.php';
            header('Location: ' . $redirect_to);
            exit;

        } catch (PDOException $e) {
            handleError("Error de base de datos al añadir a la lista de deseos: " . $e->getMessage());
        }

    } elseif (isset($_POST['eliminar_id'])) {
        // Esta parte parece que no se usa con el script de lista_deseos.php,
        // que usa un fetch API a eliminar_de_lista.php. La mantenemos por si acaso.
        $producto_id = intval($_POST['eliminar_id']);

        try {
            // Eliminar el producto de la lista de deseos para este usuario
            $stmt_delete = $pdo->prepare("DELETE FROM lista_deseos WHERE usuario_id = ? AND producto_id = ?");
            $stmt_delete->execute([$usuario_id, $producto_id]);

            if ($stmt_delete->rowCount() > 0) {
                $_SESSION['success'] = 'Producto eliminado de la lista de deseos correctamente.';
            } else {
                $_SESSION['warning'] = 'El producto no se encontró en tu lista de deseos.';
            }

             $redirect_to = $_POST['redirect_to'] ?? 'lista_deseos.php';
            header('Location: ' . $redirect_to);
            exit;

        } catch (PDOException $e) {
             handleError("Error de base de datos al eliminar de la lista de deseos: " . $e->getMessage());
        }

    } else {
        handleError("ID de producto no proporcionado");
    }
} else {
    handleError("Método de solicitud no válido");
}
?>
