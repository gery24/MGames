<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión']);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos del formulario
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validar que los campos no estén vacíos
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

// Validar que las contraseñas nuevas coincidan
if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas nuevas no coinciden']);
    exit;
}

// Validar la complejidad de la contraseña nueva
if (strlen($new_password) < 8) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres']);
    exit;
}

if (!preg_match('/[a-z]/', $new_password) || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe incluir al menos una letra minúscula, una mayúscula y un número']);
    exit;
}

try {
    // Obtener usuario de la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    // Usar la columna correcta "contraseña" con tilde
    $columna_password = 'contraseña';
    
    // Verificar la contraseña actual con la columna correcta
    if (!password_verify($current_password, $usuario[$columna_password])) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
        exit;
    }
    
    // Actualizar la contraseña
    $hash_nueva_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE usuarios SET `contraseña` = ? WHERE id = ?");
    $stmt->execute([$hash_nueva_password, $_SESSION['usuario']['id']]);
    
    // Crear mensaje de éxito con título y timestamp
    $timestamp = date('d/m/Y H:i:s');
    $mensaje_exito = [
        'title' => 'Cambio de contraseña exitoso.',
        'text' => "¡Contraseña actualizada correctamente! Tu cuenta ahora está más segura. ({$timestamp})"
    ];
    
    echo json_encode(['success' => true, 'message' => $mensaje_exito]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
} 