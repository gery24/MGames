<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'tienda_videojuegos';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Verificar la conexión con una consulta simple
    $test = $pdo->query("SELECT 1");
    if($test) {
        echo "Conexión exitosa a la base de datos<br>";
    }
} catch(PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    echo "<br>Archivo: " . $e->getFile();
    echo "<br>Línea: " . $e->getLine();
    die();
}
?> 