<?php
session_start();
require_once 'config/database.php';

// Verificar si hay productos en el carrito
$productos_en_carrito = $_SESSION['carrito'] ?? [];
if (empty($productos_en_carrito)) {
    $_SESSION['error_pago'] = "No hay productos en el carrito para procesar el pago.";
    header('Location: carrito.php');
    exit;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    $_SESSION['error_pago'] = "Debes iniciar sesión para realizar un pago.";
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['usuario']['id'];
$metodo_pago = $_POST['metodo_pago'] ?? '';

// Calcular el total a pagar
$total = 0;
foreach ($productos_en_carrito as $producto) {
    $precio = $producto['precio'] ?? 0;
    $cantidad = $producto['cantidad'] ?? 1;
    $total += $precio * $cantidad;
}

// Validar según el método de pago
$errores = [];

if ($metodo_pago === 'visa') {
    // Validar tarjeta de crédito
    $numero_tarjeta = preg_replace('/\s+/', '', $_POST['numero_tarjeta'] ?? '');
    $titular_tarjeta = $_POST['titular_tarjeta'] ?? '';
    $fecha_expiracion = $_POST['fecha_expiracion'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // Validar número de tarjeta (debe tener 16 dígitos)
    if (empty($numero_tarjeta) || !preg_match('/^\d{16}$/', $numero_tarjeta)) {
        $errores[] = "El número de tarjeta debe tener 16 dígitos.";
    }

    // Validar titular
    if (empty($titular_tarjeta)) {
        $errores[] = "El nombre del titular es obligatorio.";
    }

    // Validar fecha de expiración (formato MM/AA)
    if (empty($fecha_expiracion) || !preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $fecha_expiracion)) {
        $errores[] = "La fecha de expiración debe tener el formato MM/AA.";
    } else {
        // Verificar que la fecha no esté expirada
        list($mes, $anio) = explode('/', $fecha_expiracion);
        $anio = '20' . $anio; // Convertir a formato de 4 dígitos
        $fecha_actual = new DateTime();
        $fecha_tarjeta = new DateTime("$anio-$mes-01");
        
        if ($fecha_tarjeta < $fecha_actual) {
            $errores[] = "La tarjeta ha expirado.";
        }
    }

    // Validar CVV (3 dígitos)
    if (empty($cvv) || !preg_match('/^\d{3}$/', $cvv)) {
        $errores[] = "El CVV debe tener 3 dígitos.";
    }
} elseif ($metodo_pago === 'bizum') {
    // Validar Bizum
    $numero_bizum = $_POST['numero_bizum'] ?? '';
    
    // Validar número de teléfono (9 dígitos para España)
    if (empty($numero_bizum) || !preg_match('/^\d{9}$/', $numero_bizum)) {
        $errores[] = "El número de teléfono para Bizum debe tener 9 dígitos.";
    }
} elseif ($metodo_pago === 'paypal') {
    // Para PayPal solo verificamos que se haya seleccionado
    // En un caso real, aquí iría la redirección a PayPal
} elseif ($metodo_pago === 'cartera') {
    // Validar pago con cartera
    $saldo_cartera = 0;
    // Obtener el saldo actual del usuario desde la base de datos
    try {
        $stmt = $pdo->prepare("SELECT cartera FROM usuarios WHERE id = ?");
        $stmt->execute([$userId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario) {
            $saldo_cartera = $usuario['cartera'];
        }
    } catch (PDOException $e) {
        // Manejar el error de base de datos si es necesario
        $errores[] = "Error al verificar el saldo de la cartera.";
    }

    // Verificar si el saldo es suficiente
    if ($saldo_cartera < $total) {
        $errores[] = "Saldo insuficiente en la cartera digital.";
    }
} else {
    $errores[] = "Debes seleccionar un método de pago válido.";
}

// Si hay errores, redirigir de vuelta al formulario
if (!empty($errores)) {
    $_SESSION['error_pago'] = implode("<br>", $errores);
    header('Location: pago.php');
    exit;
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Preparar la descripción de la compra con los nombres de los juegos
    $nombres_juegos = [];
    foreach ($productos_en_carrito as $producto) {
        if (is_array($producto) && isset($producto['nombre'])) {
            $nombres_juegos[] = $producto['nombre'];
        }
    }
    
    // Si hay más de un juego, mostrar el primero y el número de juegos adicionales
    if (count($nombres_juegos) > 1) {
        $descripcion = $nombres_juegos[0] . " y " . (count($nombres_juegos) - 1) . " juego(s) más - Método: " . ucfirst($metodo_pago);
    } elseif (count($nombres_juegos) == 1) {
        $descripcion = $nombres_juegos[0] . " - Método: " . ucfirst($metodo_pago);
    } else {
        $descripcion = "Compra de productos - Método: " . ucfirst($metodo_pago);
    }
    
    // 1. Registrar la compra en la tabla de transacciones
    $stmt = $pdo->prepare("INSERT INTO transacciones (usuario_id, monto, descripcion, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $total, $descripcion]);
    $transaccion_id = $pdo->lastInsertId();
    
    // 2. Actualizar el saldo de la cartera (si es necesario)
    // Esto depende de tu lógica de negocio. Si quieres descontar de la cartera:
    /*
    $stmt = $pdo->prepare("UPDATE usuarios SET cartera = cartera - ? WHERE id = ?");
    $stmt->execute([$total, $userId]);
    */
    
    // Si el método de pago es cartera, descontar el monto
    if ($metodo_pago === 'cartera') {
        $stmt = $pdo->prepare("UPDATE usuarios SET cartera = cartera - ? WHERE id = ?");
        $stmt->execute([$total, $userId]);
        
        // Actualizar también el saldo en la sesión
        $_SESSION['usuario']['cartera'] -= $total;
    }

    // 3. Registrar los productos comprados (opcional)
    foreach ($productos_en_carrito as $producto_id => $producto) {
        if (is_array($producto) && isset($producto['nombre'], $producto['precio'])) {
            $cantidad = $producto['cantidad'] ?? 1;
            $precio = $producto['precio'];
            
            // Aquí podrías insertar en una tabla de detalles_compra o similar
            // $stmt = $pdo->prepare("INSERT INTO detalles_compra (transaccion_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
            // $stmt->execute([$transaccion_id, $producto_id, $cantidad, $precio]);
        }
    }
    
    // Confirmar transacción
    $pdo->commit();
    
    // Limpiar el carrito
    unset($_SESSION['carrito']);
    
    // Mensaje de éxito
    $_SESSION['mensaje'] = "¡Pago realizado con éxito! Gracias por tu compra.";
    
    // Redirigir a la página de cartera con parámetro para mostrar el modal
    header('Location: cartera.php?compra_exitosa=true');
    exit;
    
} catch (PDOException $e) {
    // Revertir transacción en caso de error
    $pdo->rollBack();
    
    $_SESSION['error_pago'] = "Error al procesar el pago: " . $e->getMessage();
    header('Location: pago.php');
    exit;
}
