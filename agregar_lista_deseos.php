<?php
session_start();
require_once 'config/database.php';

// Función para manejar errores
function handleError($message) {
    error_log($message);
    $_SESSION['error'] = $message;
    header('Location: lista_deseos.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $producto_id = intval($_POST['id']);
        
        try {
            // Consulta para obtener los datos del producto
            $stmt = $pdo->prepare("SELECT id, nombre, descripcion, precio, imagen, estado, segunda_mano FROM productos WHERE id = ?");
            $stmt->execute([$producto_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto) {
                // Inicializar la lista de deseos si no existe
                if (!isset($_SESSION['lista_deseos'])) {
                    $_SESSION['lista_deseos'] = [];
                }
                
                // Agregar el producto si no está en la lista
                if (!isset($_SESSION['lista_deseos'][$producto_id])) {
                    $_SESSION['lista_deseos'][$producto_id] = $producto;
                    $_SESSION['mensaje'] = 'Producto añadido a la lista de deseos';
                } else {
                    $_SESSION['mensaje'] = 'El producto ya está en la lista de deseos';
                }
                
                header('Location: lista_deseos.php');
                exit;
            } else {
                handleError("Producto no encontrado");
            }
        } catch (PDOException $e) {
            handleError("Error de base de datos: " . $e->getMessage());
        }
    } elseif (isset($_POST['eliminar_id'])) {
        $producto_id = intval($_POST['eliminar_id']);
        
        // Verificar si el producto está en la lista de deseos
        if (isset($_SESSION['lista_deseos'][$producto_id])) {
            unset($_SESSION['lista_deseos'][$producto_id]);
            $_SESSION['mensaje'] = 'Producto eliminado de la lista de deseos';
        } else {
            $_SESSION['error'] = 'El producto no estaba en la lista de deseos';
        }
        
        header('Location: lista_deseos.php');
        exit;
    } else {
        handleError("ID de producto no proporcionado");
    }
} else {
    handleError("Método de solicitud no válido");
}
?>
