<?php
session_start();
require_once 'config/database.php';

// Función para enviar respuesta JSON (para solicitudes AJAX)
function sendJsonResponse($status, $data) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status] + $data);
    exit;
}

// Función para manejar errores (común para ambos tipos de solicitud)
function handleError($message) {
    error_log("Error en agregar_al_carrito.php: " . $message);
    
    // Detectar si es una solicitud AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        sendJsonResponse('error', ['message' => $message]);
    } else {
        // Para solicitudes de formulario normales, usar sesión y redirigir
        $_SESSION['error'] = $message;
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header('Location: ' . $referer);
        exit;
    }
}

// Función para manejar éxito (común para ambos tipos de solicitud)
function handleSuccess($message) {
    // Detectar si es una solicitud AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        sendJsonResponse('success', ['message' => $message]);
    } else {
        // Para solicitudes de formulario normales, usar sesión y redirigir
        $_SESSION['mensaje'] = $message;
        
        // Comprobar si la página de origen es producto.php o detalle_segunda_mano.php
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'producto.php') !== false || strpos($referer, 'detalle_segunda_mano.php') !== false) {
            header('Location: carrito.php');
        } else {
            // Si no viene de producto.php, redirigir a la página de origen o al carrito por defecto
            header('Location: ' . ($referer ?: 'carrito.php'));
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se han pasado el ID, tipo de producto y cantidad
    // La validación de cantidad > 0 se hace más adelante.
    if (isset($_POST['id']) && isset($_POST['tipo_producto']) && isset($_POST['cantidad'])) {
        $producto_id = intval($_POST['id']);
        $tipo_producto = $_POST['tipo_producto']; // 'productos' o 'segunda_mano'
        $cantidad = intval($_POST['cantidad']);

        // Validar la cantidad después de obtenerla
        if ($cantidad <= 0) {
            handleError("La cantidad debe ser un número positivo.");
        }
        
        $tabla = '';

        // Determinar la tabla
        if ($tipo_producto === 'productos') {
            $tabla = 'productos';
        } elseif ($tipo_producto === 'segunda_mano') {
            $tabla = 'segunda_mano';
        } else {
            handleError("Tipo de producto no válido: " . htmlspecialchars($tipo_producto));
        }

        if (empty($tabla)) {
             handleError("Tipo de producto no especificado o vacío.");
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
                $cart_item_key = $tipo_producto . '_' . $producto_id;

                // Agregar el producto al carrito o incrementar la cantidad
                if (isset($_SESSION['carrito'][$cart_item_key])) {
                    // Incrementar la cantidad existente
                    $_SESSION['carrito'][$cart_item_key]['cantidad'] += $cantidad;
                } else {
                    // Añadir el producto con la cantidad especificada
                    $producto['cantidad'] = $cantidad;
                    $producto['tipo_producto'] = $tipo_producto; // Guardar el tipo de producto en el carrito
                    $_SESSION['carrito'][$cart_item_key] = $producto;
                }
                
                // Usar handleSuccess que ahora maneja la redirección o JSON
                handleSuccess('Producto agregado al carrito correctamente');

            } else {
                handleError("Producto no encontrado en la tabla '" . $tabla . "' con ID " . htmlspecialchars($producto_id));
            }
        } catch (PDOException $e) {
            handleError("Error de base de datos al buscar producto: " . $e->getMessage());
        }
    } else {
        // Mensaje de error más específico si falta algún campo
        $missing_fields = [];
        if (!isset($_POST['id'])) $missing_fields[] = 'ID de producto';
        if (!isset($_POST['tipo_producto'])) $missing_fields[] = 'tipo de producto';
        if (!isset($_POST['cantidad'])) $missing_fields[] = 'cantidad';
        handleError("Faltan campos necesarios: " . implode(", ", $missing_fields));
    }
} else {
    handleError("Método de solicitud no válido.");
}

?>