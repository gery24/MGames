<?php
require_once 'config/database.php';

try {
    // Probar una consulta simple
    $stmt = $pdo->query("SELECT * FROM categorias");
    $categorias = $stmt->fetchAll();
    
    echo "<h2>Categorías en la base de datos:</h2>";
    echo "<ul>";
    foreach($categorias as $categoria) {
        echo "<li>" . htmlspecialchars($categoria['nombre']) . "</li>";
    }
    echo "</ul>";
    
    // Probar otra tabla
    $stmt = $pdo->query("SELECT * FROM productos LIMIT 5");
    $productos = $stmt->fetchAll();
    
    echo "<h2>Algunos productos:</h2>";
    echo "<ul>";
    foreach($productos as $producto) {
        echo "<li>" . htmlspecialchars($producto['nombre']) . " - €" . 
             number_format($producto['precio'], 2) . "</li>";
    }
    echo "</ul>";

} catch(PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?> 