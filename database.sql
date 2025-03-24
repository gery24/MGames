-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS tienda_videojuegos;
USE tienda_videojuegos;

-- Eliminar las tablas si existen para evitar conflictos
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS lista_deseos;

-- Tabla de categorías
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla de productos
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    categoria_id INT NOT NULL,
    segunda_mano BOOLEAN DEFAULT FALSE,
    estado VARCHAR(50) DEFAULT 'Nuevo',
    imagen VARCHAR(255) DEFAULT 'images/default.jpg',
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('ADMIN', 'USER') DEFAULT 'USER'
);

-- Tabla de lista de deseos
CREATE TABLE IF NOT EXISTS lista_deseos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    producto_id INT NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    UNIQUE KEY unique_wishlist (usuario_id, producto_id)
);

-- Insertar algunas categorías de ejemplo
INSERT INTO categorias (nombre) VALUES 
('Acción'),
('Aventura'),
('RPG'),
('Deportes'),
('Estrategia');

-- Insertar algunos productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, categoria_id) VALUES 
('The Last of Us', 'Juego de acción y aventura post-apocalíptico', 59.99, 1),
('FIFA 24', 'Simulador de fútbol', 69.99, 4),
('Final Fantasy XVI', 'RPG de fantasía', 79.99, 3),
('Assassin''s Creed Valhalla', 'Aventura vikinga', 49.99, 2),
('Age of Empires IV', 'Juego de estrategia en tiempo real', 39.99, 5);

-- Insertar algunos productos de segunda mano
INSERT INTO productos (nombre, descripcion, precio, categoria_id, segunda_mano, estado, imagen) VALUES 
('God of War', 'Juego de acción y aventura', 29.99, 1, 1, 'Buen estado', 'images/gow.jpg'),
('Spider-Man', 'Juego de superhéroes', 35.99, 2, 1, 'Como nuevo', 'images/spiderman.jpg');

-- Actualizar el usuario admin existente o crearlo si no existe
INSERT INTO usuarios (nombre, apellido, email, contraseña, rol) VALUES 
('Admin', 'Admin', 'administrador@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN')
ON DUPLICATE KEY UPDATE rol = 'ADMIN';

-- Modificar la tabla usuarios para incluir el rol
ALTER TABLE usuarios
ADD COLUMN rol ENUM('ADMIN', 'USER') DEFAULT 'USER';

-- Actualizar el usuario admin existente o crearlo si no existe
INSERT INTO usuarios (nombre, apellido, email, contraseña, rol) VALUES 
('Admin', 'Admin', 'administrador@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN')
ON DUPLICATE KEY UPDATE rol = 'ADMIN'; 