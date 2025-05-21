<?php
session_start();
require_once 'config/database.php';

// Función para manejar errores y redirigir con mensaje
function handleError($message, $redirect_page = 'carrito.php') {
    error_log("Error en agregar_al_carrito.php: " . $message);
    $_SESSION['error'] = $message;
    header('Location: ' . $redirect_page);
    exit;
}

// Función para manejar éxito y redirigir con mensaje
function handleSuccess($message, $redirect_page = 'carrito.php') {
    $_SESSION['mensaje'] = $message;
    header('Location: ' . $redirect_page);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['tipo_producto'])) {
        $producto_id = intval($_POST['id']);
        $tipo_producto = $_POST['tipo_producto']; // 'productos' o 'segunda_mano'
        
        $tabla = '';
        $redirect_page = 'carrito.php'; // Página por defecto para redirigir después de agregar al carrito

        // Determinar la tabla y la página de origen/redirección
        if ($tipo_producto === 'productos') {
            $tabla = 'productos';
            // Podrías querer redirigir de vuelta a la página del producto normal
            // $redirect_page = 'producto.php?id=' . $producto_id;
        } elseif ($tipo_producto === 'segunda_mano') {
            $tabla = 'segunda_mano';
            // Podrías querer redirigir de vuelta a la página del producto de segunda mano
            // $redirect_page = 'detalle_segunda_mano.php?id=' . $producto_id;
        } else {
            handleError("Tipo de producto no válido: " . htmlspecialchars($tipo_producto), $_SERVER['HTTP_REFERER'] ?? 'index.php');
        }

        if (empty($tabla)) {
             handleError("Tipo de producto no especificado o vacío.", $_SERVER['HTTP_REFERER'] ?? 'index.php');
        }

        try {
            // Consulta para obtener el producto de la tabla correcta
            // Seleccionamos solo las columnas necesarias para el carrito
            $stmt = $pdo->prepare("SELECT id, nombre, precio, imagen FROM " . $tabla . " WHERE id = ?");
            $stmt->execute([$producto_id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($producto) {
                // Inicializar el carrito si no existe
                if (!isset($_SESSION['carrito'])) {
                    $_SESSION['carrito'] = [];
                }
                
                // Generar una clave única para el producto en el carrito
                // Usamos el ID y el tipo de producto para diferenciar
                $cart_item_key = $tipo_producto . '_' . $producto_id;

                // Agregar el producto al carrito o incrementar la cantidad
                if (isset($_SESSION['carrito'][$cart_item_key])) {
                    $_SESSION['carrito'][$cart_item_key]['cantidad']++;
                } else {
                    $producto['cantidad'] = 1;
                    $producto['tipo_producto'] = $tipo_producto; // Guardar el tipo de producto en el carrito
                    $_SESSION['carrito'][$cart_item_key] = $producto;
                }
                
                // Redirigir de vuelta a la página desde donde se añadió o al carrito
                $referer = $_SERVER['HTTP_REFERER'] ?? 'carrito.php';
                handleSuccess('Producto agregado al carrito correctamente', $referer);

            } else {
                handleError("Producto no encontrado en la tabla '" . $tabla . "' con ID " . htmlspecialchars($producto_id), $_SERVER['HTTP_REFERER'] ?? 'index.php');
            }
        } catch (PDOException $e) {
            handleError("Error de base de datos al buscar producto: " . $e->getMessage(), $_SERVER['HTTP_REFERER'] ?? 'index.php');
        }
    } else {
        handleError("ID de producto o tipo de producto no proporcionado.", $_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
} else {
    handleError("Método de solicitud no válido.", $_SERVER['HTTP_REFERER'] ?? 'index.php');
}

?>