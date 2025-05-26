<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    
    if ($id && $cantidad && is_numeric($cantidad) && $cantidad > 0) {
        if (isset($_SESSION['carrito'][$id])) {
            // Actualizar la cantidad
            $_SESSION['carrito'][$id]['cantidad'] = (int)$cantidad;
            
            // Calcular el nuevo total
            $total = 0;
            $subtotales = [];
            
            foreach ($_SESSION['carrito'] as $cart_item_key => $producto) {
                if (is_array($producto) && isset($producto['precio'])) {
                    $precio_unitario = $producto['precio'];
                    $cant = $producto['cantidad'] ?? 1;
                    $descuento = $producto['descuento'] ?? 0;
                    
                    // Calcular precio con descuento
                    $precio_con_descuento = $precio_unitario * (1 - ($descuento / 100));
                    $subtotal_linea = $precio_con_descuento * $cant;
                    
                    $total += $subtotal_linea;
                    
                    // Guardar subtotal de cada producto usando la clave completa del carrito
                    $subtotales[$cart_item_key] = [
                        'subtotal' => number_format($subtotal_linea, 2, '.', ''),
                        'cantidad' => $cant,
                        'nombre' => $producto['nombre'] ?? '',
                        'id' => explode('_', $cart_item_key)[1] ?? null
                    ];
                }
            }
            
            // Devolver éxito y los nuevos totales
            echo json_encode([
                'success' => true,
                'total' => number_format($total, 2, '.', ''),
                'subtotales' => $subtotales,
            ]);
            exit;
        }
    }
}

// Si llegamos aquí, algo falló
echo json_encode([
    'success' => false,
    'message' => 'Error al actualizar la cantidad o producto no encontrado.'
]);
exit;
?>
