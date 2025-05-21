<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['cantidad'])) {
    $producto_id = intval($_POST['id']);
    $cantidad = intval($_POST['cantidad']);
    
    // Validar que la cantidad sea positiva
    if ($cantidad > 0 && $cantidad <= 10) {
        // Verificar que el producto existe en el carrito
        if (isset($_SESSION['carrito'][$producto_id])) {
            // Actualizar la cantidad
            $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
            
            // Recalcular el resumen del carrito
            $subtotal = 0;
            foreach ($_SESSION['carrito'] as $item_id => $item_data) {
                // Asegurarse de que el producto existe en la base de datos para obtener el precio y descuento correctos
                // Esto requiere una consulta a la base de datos, lo cual puede impactar el rendimiento si el carrito es muy grande.
                // Una alternativa sería almacenar el precio con descuento y el descuento original en la sesión.
                // Por ahora, asumiremos que los datos en la sesión son suficientes o que haremos una consulta ligera.
                // **Nota:** Para una implementación robusta con descuento, se debería consultar la DB aquí o almacenar más datos en sesión.

                // Usaremos los datos disponibles en la sesión para simplificar.
                $precio_unitario = $item_data['precio'];
                $cantidad_item = $item_data['cantidad'];
                // Si se almacenara el descuento en sesión, se aplicaría aquí:
                // $descuento_porcentaje = $item_data['descuento'] ?? 0;
                // $precio_con_descuento_unitario = $precio_unitario * (1 - ($descuento_porcentaje / 100));
                // $subtotal += $precio_con_descuento_unitario * $cantidad_item;
                
                // Cálculo simple basado en el precio almacenado (que debería ser el final si se manejó el descuento al añadir)
                $subtotal += $precio_unitario * $cantidad_item;
            }
            
            $total = $subtotal; // Asumiendo que no hay otros cargos como envío, etc.

            echo json_encode([
                'success' => true,
                'subtotal' => number_format($subtotal, 2), // Formatear para mostrar
                'total' => number_format($total, 2) // Formatear para mostrar
            ]);
            exit;
        }
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Error al actualizar la cantidad o producto no encontrado.'
]);
?>