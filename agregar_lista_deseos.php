<?php
session_start();
require_once 'config/database.php';

// Función para manejar errores
function handleError($message) {
    error_log($message);
    // Usar un parámetro GET en lugar de sesión para el mensaje, más sencillo tras redirigir
    header('Location: lista_deseos.php?error=' . urlencode($message));
    exit;
}

// Función para manejar éxito
function handleSuccess($message) {
    // Usar un parámetro GET en lugar de sesión para el mensaje
    header('Location: lista_deseos.php?success=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
        handleError("Debes iniciar sesión para añadir productos a la lista de deseos.");
    }

    $usuario_id = $_SESSION['usuario']['id'];

    // Obtener el ID del ítem y el tipo
    if (isset($_POST['id']) && isset($_POST['tipo'])) {
        $item_id = intval($_POST['id']);
        $item_tipo = $_POST['tipo']; // 'producto' o 'segunda_mano'

        // Validar que el tipo sea uno de los esperados
        if ($item_tipo !== 'producto' && $item_tipo !== 'segunda_mano') {
             handleError("Tipo de ítem no válido.");
        }

        try {
            // --- Lógica para añadir a la base de datos --- //

            // Verificar si el ítem ya está en la lista de deseos
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM lista_deseos WHERE usuario_id = ? AND producto_id = ? AND tipo = ?");
            $stmt_check->execute([$usuario_id, $item_id, $item_tipo]);
            $exists = $stmt_check->fetchColumn();

            if ($exists > 0) {
                // El ítem ya existe en la lista de deseos del usuario
                handleSuccess('El ítem ya está en la lista de deseos.');
            } else {
                // Insertar el ítem en la tabla lista_deseos
                $stmt_insert = $pdo->prepare("INSERT INTO lista_deseos (usuario_id, tipo, producto_id) VALUES (?, ?, ?)");
                $stmt_insert->execute([$usuario_id, $item_tipo, $item_id]);

                handleSuccess('Ítem añadido a la lista de deseos.');
            }

            // --- Fin Lógica para añadir a la base de datos --- //

        } catch (PDOException $e) {
            handleError("Error de base de datos al añadir a la lista de deseos: " . $e->getMessage());
        }
    } else {
        handleError("ID o tipo de ítem no proporcionado.");
    }
} else {
    handleError("Método de solicitud no válido.");
}
?>
