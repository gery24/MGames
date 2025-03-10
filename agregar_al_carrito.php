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
            // Consulta para obtener el producto
            $stmt = $pdo->prepare("SELECT * FROM segunda_mano WHERE id = ?");
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
                // Manejar el caso en que el producto no se encuentra
                $_SESSION['error'] = 'Producto no encontrado';
                header('Location: carrito.php');
                exit;
            }
        } catch (PDOException $e) {
            // Manejar errores de base de datos
            $_SESSION['error'] = 'Error de base de datos: ' . $e->getMessage();
            header('Location: carrito.php');
            exit;
        }
    } else {
        // Manejar el caso en que no se proporciona el ID
        $_SESSION['error'] = 'ID de producto no proporcionado';
        header('Location: carrito.php');
        exit;
    }
} else {
    // Manejar el caso en que no se recibe una solicitud POST
    $_SESSION['error'] = 'Método de solicitud no válido';
    header('Location: carrito.php');
    exit;
}
?>