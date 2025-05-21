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
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_pago']) . '</div>';
            unset($_SESSION['error_pago']); // Limpiar el error después de mostrarlo
        }
        // Mostrar mensaje de éxito si existe
        if (isset($_SESSION['mensaje'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['mensaje']) . '</div>';
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
            
            <form method="POST" action="procesar_pago.php">
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
                    </div>
                    <div class="payment-option">
                        <label for="titular_tarjeta">Titular de la tarjeta:</label>
                        <input type="text" id="titular_tarjeta" name="titular_tarjeta" required>
                    </div>
                    <div class="payment-row">
                        <div class="payment-option half">
                            <label for="fecha_expiracion">Fecha de caducidad:</label>
                            <input type="text" id="fecha_expiracion" name="fecha_expiracion" required placeholder="MM/AA" oninput="formatExpirationDate(this)">
                        </div>
                        <div class="payment-option half">
                            <label for="cvv">CVV:</label>
                            <input type="text" id="cvv" name="cvv" required maxlength="3" placeholder="***">
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
                        <input type="text" id="numero_bizum" name="numero_bizum" required placeholder="Ej: 612345678">
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
        // Ocultar todos los formularios al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.payment-form').forEach(form => {
                form.style.display = 'none';
            });

            // --- Lógica para mostrar mensaje de éxito y redirigir ---
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                alert(successAlert.textContent);
                window.location.href = 'index.php'; // Redirigir al índice
            }
             // --- Fin Lógica para mostrar mensaje de éxito y redirigir ---
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
            document.getElementById('formulario_' + metodo).style.display = 'block';
            
            // Establecer el valor del método de pago en el campo oculto
            document.getElementById('metodo_pago').value = metodo;
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
    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
