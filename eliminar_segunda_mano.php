<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Verificar si la solicitud es POST y si el usuario está logueado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario'])) {
    $usuario_id = $_SESSION['usuario']['id'];
    $producto_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if ($producto_id) {
        try {
            // Verificar que el producto pertenece al usuario logueado antes de eliminar
            $stmt_check = $pdo->prepare("SELECT id FROM segunda_mano WHERE id = ? AND usuario_id = ?");
            $stmt_check->execute([$producto_id, $usuario_id]);
            $producto = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                // El producto pertenece al usuario, proceder con la eliminación
                $stmt_delete = $pdo->prepare("DELETE FROM segunda_mano WHERE id = ?");
                if ($stmt_delete->execute([$producto_id])) {
                    $response['success'] = true;
                    $response['message'] = 'Producto eliminado correctamente.';
                } else {
                    $response['message'] = 'Error al eliminar el producto.';
                }
            } else {
                $response['message'] = 'Producto no encontrado o no tienes permisos para eliminarlo.';
            }

        } catch (PDOException $e) {
            // Registrar el error en lugar de mostrarlo directamente al usuario
            error_log('Error al eliminar producto de segunda mano: ' . $e->getMessage());
            $response['message'] = 'Error interno del servidor.';
        }
    } else {
        $response['message'] = 'ID de producto no válido.';
    }
} else {
    $response['message'] = 'Solicitud no válida.';
}

echo json_encode($response);
?> 