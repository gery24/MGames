<?php
$host = 'localhost';
$db   = 'tienda_videojuegos';  // Nombre de la base de datos que se ve en phpMyAdmin
$user = 'root';                // Usuario por defecto en instalaciones locales
$pass = '';                    // Contraseña por defecto en instalaciones locales
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>