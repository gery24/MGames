<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $id = $_SESSION['usuario']['id'];

    // Actualizar la información en la base de datos
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ? WHERE id = ?");
        $stmt->execute([$nombre, $apellido, $email, $id]);

        // Actualizar la sesión
        $_SESSION['usuario']['nombre'] = $nombre;
        $_SESSION['usuario']['apellido'] = $apellido;
        $_SESSION['usuario']['email'] = $email;

        header('Location: perfil.php?success=1');
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?> 