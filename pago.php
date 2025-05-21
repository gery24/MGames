<?php
session_start();
require_once 'config/database.php';

// Verificar si hay productos en el carrito
$productos_en_carrito = $_SESSION['carrito'] ?? [];
if (empty($productos_en_carrito)) {
    header('Location: carrito.php');
    exit;
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pago</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/pago.css">
</head>
<body>
    <div class="content">
        <h1>Detalles de Pago</h1>
        
        <?php
        // Mostrar mensaje de error si existe
        if (isset($_SESSION['error_pago'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_pago'] . '</div>';
            unset($_SESSION['error_pago']); // Limpiar el error después de mostrarlo
        }
        // Mostrar mensaje de éxito si existe
        if (isset($_SESSION['mensaje'])) {
            echo '<div class="alert alert-success">' . $_SESSION['mensaje'] . '</div>';
            unset($_SESSION['mensaje']); // Limpiar el mensaje después de mostrarlo
        }
        ?>
        
        <div class="payment-container">
            <div class="payment-selector">
                <h2>Selecciona un método de pago</h2>
                <div class="payment-methods">
                    <div class="payment-method-option visa" onclick="seleccionarMetodo('visa')">
                        <img src="images/visa.png" alt="Visa" class="payment-icon">
                        <span>Tarjeta de crédito</span>
                    </div>
                    <div class="payment-method-option bizum" onclick="seleccionarMetodo('bizum')">
                        <img src="images/bizum.png" alt="Bizum" class="payment-icon">
                        <span>Bizum</span>
                    </div>
                    <div class="payment-method-option paypal" onclick="seleccionarMetodo('paypal')">
                        <img src="images/paypal.png" alt="PayPal" class="payment-icon">
                        <span>PayPal</span>
                    </div>
                </div>
            </div>
            
            <form id="payment-form" method="POST" action="procesar_pago.php" onsubmit="return validarFormulario()">
                <input type="hidden" id="metodo_pago" name="metodo_pago" value="">
                
                <!-- Formulario para Visa -->
                <div id="formulario_visa" class="payment-form visa">
                    <div class="payment-form-header">
                        <h2>Pago con Tarjeta</h2>
                        <img src="images/visa.png" alt="Visa" class="payment-form-icon">
                    </div>
                    <div class="payment-option">
                        <label for="numero_tarjeta">Número de tarjeta:</label>
                        <input type="text" id="numero_tarjeta" name="numero_tarjeta" maxlength="19" placeholder="**** **** **** ****" oninput="formatCardNumber(this)">
                        <span class="error-message" id="error-numero-tarjeta"></span>
                    </div>
                    <div class="payment-option">
                        <label for="titular_tarjeta">Titular de la tarjeta:</label>
                        <input type="text" id="titular_tarjeta" name="titular_tarjeta">
                        <span class="error-message" id="error-titular-tarjeta"></span>
                    </div>
                    <div class="payment-row">
                        <div class="payment-option half">
                            <label for="fecha_expiracion">Fecha de caducidad:</label>
                            <input type="text" id="fecha_expiracion" name="fecha_expiracion" placeholder="MM/AA" oninput="formatExpirationDate(this)">
                            <span class="error-message" id="error-fecha-expiracion"></span>
                        </div>
                        <div class="payment-option half">
                            <label for="cvv">CVV:</label>
                            <input type="text" id="cvv" name="cvv" maxlength="3" placeholder="***">
                            <span class="error-message" id="error-cvv"></span>
                        </div>
                    </div>
                    <button type="submit" class="btn">Pagar ahora</button>
                </div>

                <!-- Formulario para Bizum -->
                <div id="formulario_bizum" class="payment-form bizum">
                    <div class="payment-form-header">
                        <h2>Pago con Bizum</h2>
                        <img src="images/bizum.png" alt="Bizum" class="payment-form-icon">
                    </div>
                    <p class="payment-info">Por favor, ingresa tu número de teléfono asociado a Bizum.</p>
                    <div class="payment-option">
                        <label for="numero_bizum">Número de teléfono:</label>
                        <input type="text" id="numero_bizum" name="numero_bizum" placeholder="Ej: 612345678" maxlength="9" oninput="validarNumeroBizum(this)" inputmode="numeric" pattern="[0-9]*">
                        <span class="error-message" id="error-numero-bizum"></span>
                    </div>
                    <button type="submit" class="btn">Pagar con Bizum</button>
                </div>

                <!-- Formulario para PayPal -->
                <div id="formulario_paypal" class="payment-form paypal">
                    <div class="payment-form-header">
                        <h2>Pago con PayPal</h2>
                        <img src="images/paypal.png" alt="PayPal" class="payment-form-icon">
                    </div>
                    <p class="payment-info">Serás redirigido a PayPal para completar tu pago de forma segura.</p>
                    <input type="hidden" name="paypal" value="1">
                    <button type="submit" class="btn">Continuar a PayPal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.payment-form').forEach(form => {
                form.style.display = 'none';
            });

            // Ocultar todos los mensajes de error al cargar la página
            document.querySelectorAll('.error-message').forEach(el => {
                el.style.display = 'none';
            });

            // Seleccionar Visa por defecto al cargar la página
            seleccionarMetodo('visa');
        });

        function seleccionarMetodo(metodo) {
            // Ocultar todos los formularios de pago
            document.querySelectorAll('.payment-form').forEach(form => {
                form.style.display = 'none';
            });

            // Quitar la clase activa de todas las opciones
            document.querySelectorAll('.payment-method-option').forEach(option => {
                option.classList.remove('active');
            });

            // Añadir la clase activa a la opción seleccionada
            document.querySelectorAll('.payment-method-option').forEach(option => {
                if (option.querySelector('span').textContent.toLowerCase().includes(metodo) ||
                    (metodo === 'visa' && option.querySelector('span').textContent.includes('Tarjeta'))) {
                    option.classList.add('active');
                }
            });

            // Mostrar el formulario correspondiente
            const formToShow = document.getElementById('formulario_' + metodo);
            if (formToShow) {
                formToShow.style.display = 'block';
            } else {
                console.error('Formulario no encontrado para el método:', metodo);
            }

            // Establecer el valor del método de pago en el campo oculto
            document.getElementById('metodo_pago').value = metodo;
            console.log('Método de pago seleccionado:', metodo);
            
            // Limpiar mensajes de error previos
            document.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
                el.style.display = 'none'; // Ocultar los contenedores de mensajes de error
            });
            
            // Quitar clases de error de los campos
            document.querySelectorAll('.payment-option input').forEach(input => {
                input.classList.remove('error');
            });
        }

        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '').substring(0, 16);
            let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
            input.value = formattedValue;
        }

        function formatExpirationDate(input) {
            let value = input.value.replace(/\D/g, '').substring(0, 4);
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            }
            input.value = value;
        }

        function validarNumeroBizum(input) {
    const valor = input.value.trim();
    const errorElement = document.getElementById('error-numero-bizum');
    
    // Limpiar mensaje de error previo
    errorElement.textContent = '';
    errorElement.style.display = 'none';
    input.classList.remove('error');
    
    // Si el campo está vacío, no mostrar error
    if (!valor) return;
    
    // Validar que solo contenga números
    if (!/^\d*$/.test(valor)) {
        errorElement.textContent = 'Introduce solo números, sin espacios ni caracteres especiales';
        errorElement.style.display = 'block';
        input.classList.add('error');
        
        // Eliminar caracteres no numéricos
        input.value = valor.replace(/\D/g, '');
        return;
    }
    
    // Si ya tiene 9 dígitos, validar el prefijo
    if (valor.length === 9) {
        if (!/^[6789]/.test(valor)) {
            errorElement.textContent = 'El número debe comenzar con 6, 7, 8 o 9';
            errorElement.style.display = 'block';
            input.classList.add('error');
        }
    }
    // Si tiene más de 9 dígitos, truncar
    else if (valor.length > 9) {
        input.value = valor.substring(0, 9);
    }
}
        
        function validarFormulario() {
    // Obtener el método de pago seleccionado
    const metodoPago = document.getElementById('metodo_pago').value;
    let esValido = true;
    
    // Limpiar mensajes de error previos y quitar clases de error
    document.querySelectorAll('.error-message').forEach(el => {
        el.textContent = '';
        el.style.display = 'none'; // Ocultar todos los contenedores de mensajes de error
    });
    document.querySelectorAll('.payment-option input').forEach(input => {
        input.classList.remove('error');
    });
    
    if (metodoPago === 'visa') {
        // Validar tarjeta de crédito
        const numeroTarjeta = document.getElementById('numero_tarjeta').value.replace(/\s+/g, '');
        const titularTarjeta = document.getElementById('titular_tarjeta').value.trim();
        const fechaExpiracion = document.getElementById('fecha_expiracion').value.trim();
        const cvv = document.getElementById('cvv').value.trim();
        
        // Validar número de tarjeta
        if (!numeroTarjeta || numeroTarjeta.length !== 16 || !/^\d+$/.test(numeroTarjeta)) {
            const errorElement = document.getElementById('error-numero-tarjeta');
            errorElement.textContent = 'Introduce un número de tarjeta válido de 16 dígitos';
            errorElement.style.display = 'block'; // Mostrar solo este mensaje de error
            document.getElementById('numero_tarjeta').classList.add('error');
            esValido = false;
        }
        
        // Validar titular
        if (!titularTarjeta) {
            const errorElement = document.getElementById('error-titular-tarjeta');
            errorElement.textContent = 'El nombre del titular es obligatorio';
            errorElement.style.display = 'block'; // Mostrar solo este mensaje de error
            document.getElementById('titular_tarjeta').classList.add('error');
            esValido = false;
        }
        
        // Validar fecha de expiración
        if (!fechaExpiracion || !/^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(fechaExpiracion)) {
            const errorElement = document.getElementById('error-fecha-expiracion');
            errorElement.textContent = 'Formato inválido. Usa MM/AA';
            errorElement.style.display = 'block'; // Mostrar solo este mensaje de error
            document.getElementById('fecha_expiracion').classList.add('error');
            esValido = false;
        } else {
            // Verificar que la fecha no esté expirada
            const [mes, anio] = fechaExpiracion.split('/');
            const fechaActual = new Date();
            const anioActual = fechaActual.getFullYear() % 100; // Últimos 2 dígitos del año
            const mesActual = fechaActual.getMonth() + 1; // getMonth() devuelve 0-11
            
            if (parseInt(anio) < anioActual || (parseInt(anio) === anioActual && parseInt(mes) < mesActual)) {
                const errorElement = document.getElementById('error-fecha-expiracion');
                errorElement.textContent = 'La tarjeta ha expirado';
                errorElement.style.display = 'block'; // Mostrar solo este mensaje de error
                document.getElementById('fecha_expiracion').classList.add('error');
                esValido = false;
            }
        }
        
        // Validar CVV
        if (!cvv || cvv.length !== 3 || !/^\d+$/.test(cvv)) {
            const errorElement = document.getElementById('error-cvv');
            errorElement.textContent = 'El CVV debe tener 3 dígitos';
            errorElement.style.display = 'block'; // Mostrar solo este mensaje de error
            document.getElementById('cvv').classList.add('error');
            esValido = false;
        }
        
    } else if (metodoPago === 'bizum') {
        // Validar Bizum
        const numeroBizum = document.getElementById('numero_bizum').value.trim();
        
        // Validar que sea un número
        if (!/^\d+$/.test(numeroBizum)) {
            const errorElement = document.getElementById('error-numero-bizum');
            errorElement.textContent = 'Introduce solo números, sin espacios ni caracteres especiales';
            errorElement.style.display = 'block';
            document.getElementById('numero_bizum').classList.add('error');
            esValido = false;
        }
        // Validar que tenga 9 dígitos (formato estándar de teléfono español)
        else if (numeroBizum.length !== 9) {
            const errorElement = document.getElementById('error-numero-bizum');
            errorElement.textContent = 'El número debe tener exactamente 9 dígitos';
            errorElement.style.display = 'block';
            document.getElementById('numero_bizum').classList.add('error');
            esValido = false;
        }
        // Validar que comience con un prefijo válido para España (6 o 7 para móviles, 8 o 9 para fijos)
        else if (!/^[6789]/.test(numeroBizum)) {
            const errorElement = document.getElementById('error-numero-bizum');
            errorElement.textContent = 'El número debe comenzar con 6, 7, 8 o 9';
            errorElement.style.display = 'block';
            document.getElementById('numero_bizum').classList.add('error');
            esValido = false;
        }
    }
    
    return esValido;
}
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
