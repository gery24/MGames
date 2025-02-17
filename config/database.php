<?php
$host = 'localhost'; // o la dirección de tu servidor
$db = 'tienda_videojuegos'; // nombre de tu base de datos
$user = 'root'; // tu usuario de base de datos
$pass = ''; // tu contraseña de base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}
?>
