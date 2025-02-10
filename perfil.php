<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$usuario = $_SESSION['usuario'];
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'menu';

// Obtener información adicional del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario['id']]);
    $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($datosUsuario) {
        // Actualizar solo si encontramos datos
        foreach ($datosUsuario as $key => $value) {
            $usuario[$key] = $value;
        }
        $_SESSION['usuario'] = $usuario;
    }
} catch(PDOException $e) {
    $error = 'Error al obtener información del usuario';
}

// Resto del código igual...
?>

<!DOCTYPE html>
<!-- Resto del código HTML igual... --> 