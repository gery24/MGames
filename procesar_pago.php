<?php
session_start();
require_once 'config/database.php';

// Función para manejar errores de validación y redirigir
function setValidationError($message) {
    $_SESSION['error_pago'] = $message;
    header('Location: pago.php');
    exit;
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar el método de pago
    $metodo_pago = $_POST['metodo_pago'] ?? '';
    
    if ($metodo_pago === 'visa') {
        // Obtener datos de la tarjeta
        $numero_tarjeta = $_POST['numero_tarjeta'] ?? '';
        $titular_tarjeta = $_POST['titular_tarjeta'] ?? '';
        $fecha_expiracion = $_POST['fecha_expiracion'] ?? '';
        $cvv = $_POST['cvv'] ?? '';
        
        // --- Validaciones --- 

        // Validar Nombre Completo (al menos un espacio)
        if (strpos(trim($titular_tarjeta), ' ') === false) {
            setValidationError('Por favor, introduce el nombre completo del titular de la tarjeta.');
        }
        
        // Validar Fecha de Caducidad (formato MM/AA y fecha futura)
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $fecha_expiracion)) {
            setValidationError('Formato de fecha de caducidad inválido. Usa MM/AA y asegúrate que el mes sea válido.');
        }
        
        list($mes, $año) = explode('/', $fecha_expiracion);
        $año = 2000 + intval($año); // Asume que AA es para años 2000+
        $mes = intval($mes);
        
        // Comprobar si el mes es válido (aunque el regex ya ayuda, esta es una doble verificación)
        if ($mes < 1 || $mes > 12) {
             setValidationError('El mes de caducidad no es válido.');
        }

        // Obtener fecha actual
        $fecha_actual = new DateTime();
        $año_actual = intval($fecha_actual->format('Y'));
        $mes_actual = intval($fecha_actual->format('m'));
        
        // Crear objeto DateTime para la fecha de caducidad
        // Usamos el último día del mes para la comparación
        $fecha_caducidad_dt = DateTime::createFromFormat('Y-m-t', sprintf('%d-%02d-%02d', $año, $mes, 1));

        if (!$fecha_caducidad_dt || $fecha_caducidad_dt < $fecha_actual) {
             setValidationError('La fecha de caducidad de la tarjeta no es válida.');
        }

        // Validar CVV (básico: 3 o 4 dígitos numéricos)
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            setValidationError('CVV inválido.');
        }

        // Validar Número de Tarjeta (básico: solo números y espacios, longitud)
         if (!preg_match('/^[\d ]{13,19}$/', $numero_tarjeta)) {
             setValidationError('Número de tarjeta inválido.');
         }

        // --- Si todas las validaciones pasan para Visa, procesar pago (simulado) ---

        // Aquí iría la lógica real de procesamiento de pago con pasarela de pago

        // Simulación de éxito en el pago
        $_SESSION['mensaje'] = 'Pago con Tarjeta realizado con éxito. Gracias por tu compra!';
        // Limpiar el carrito después del pago
        unset($_SESSION['carrito']);
        header('Location: index.php'); 
        exit;

    } elseif ($metodo_pago === 'bizum') {
        // Obtener datos de Bizum
        $numero_bizum = $_POST['numero_bizum'] ?? '';
        
        // Validar número de Bizum (ejemplo básico: 9 dígitos numéricos)
        if (!preg_match('/^\d{9}$/', $numero_bizum)) {
            setValidationError('Número de Bizum inválido.');
        }

        // --- Si todas las validaciones pasan para Bizum, procesar pago (simulado) ---

        // Aquí iría la lógica real de procesamiento de pago con Bizum

        // Simulación de éxito en el pago
        $_SESSION['mensaje'] = 'Pago con Bizum realizado con éxito. Gracias por tu compra!';
        // Limpiar el carrito después del pago
        unset($_SESSION['carrito']);
        header('Location: index.php'); 
        exit;

    } elseif ($metodo_pago === 'paypal') {
        // Lógica para PayPal (normalmente redirige a la plataforma de PayPal)

        // Si hay un campo oculto 'paypal' y su valor es '1', asumimos que se seleccionó PayPal
        if (isset($_POST['paypal']) && $_POST['paypal'] === '1') {
             // Aquí iría la lógica para iniciar la transacción con PayPal
             // Esto usualmente implica crear un pago en la API de PayPal y redirigir al usuario
             
             // Por ahora, solo simulamos el éxito y redirigimos
             $_SESSION['mensaje'] = 'Redirigiendo a PayPal... Simulación de pago con PayPal exitoso.';
             // Limpiar el carrito después de simular la redirección y el pago exitoso
             unset($_SESSION['carrito']);
             header('Location: index.php'); // O a una página de confirmación después de la vuelta de PayPal
             exit;
        } else {
            setValidationError('Método de pago PayPal no seleccionado correctamente.');
        }
        
    } else {
         setValidationError('Método de pago no especificado o inválido.');
    }

} else {
    die("Método de solicitud no válido.");
}

?> 