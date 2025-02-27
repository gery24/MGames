<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Aseguramos que sea un número entero

    if (isset($_SESSION['lista_deseos'])) {
        foreach ($_SESSION['lista_deseos'] as $index => $producto) {
            if ($producto['id'] === $id) {
                unset($_SESSION['lista_deseos'][$index]); // Eliminamos el producto
                $_SESSION['lista_deseos'] = array_values($_SESSION['lista_deseos']); // Reindexamos el array
                break;
            }
        }
    }
}

// Redirigimos de vuelta a la lista de deseos
header("Location: lista_deseos.php");
exit;