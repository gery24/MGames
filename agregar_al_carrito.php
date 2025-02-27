<?php
session_start();
require_once 'config/database.php';

// Función para manejar errores
function handleError($message) {
    error_log($message);
    $_SESSION['error'] = $message;
    header('Location: carrito.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $producto_id = intval($_POST['id']);
        
        try {
            // Consulta ajustada a la estructura de la tabla productos
            $stmt = $pdo->prepare("SELECT id, nombre, descripcion, precio, imagen, estado, segunda_mano FROM productos WHERE id = ?");
            $stmt->execute([$producto_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto) {
                // Inicializar el carrito si no existe
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }
                
                // Agregar el producto al carrito o incrementar la cantidad
                if (isset($_SESSION['carrito'][$producto_id])) {
                    $_SESSION['carrito'][$producto_id]['cantidad']++;
                } else {
                    $producto['cantidad'] = 1;
                    $_SESSION['carrito'][$producto_id] = $producto;
                }
                
                $_SESSION['mensaje'] = 'Producto agregado al carrito correctamente';
                header('Location: carrito.php');
                exit;
            } else {
                handleError("Producto no encontrado");
            }
        } catch (PDOException $e) {
            handleError("Error de base de datos: " . $e->getMessage());
        }
    } else {
        handleError("ID de producto no proporcionado");
    }
} else {
    handleError("Método de solicitud no válido");
}
?>