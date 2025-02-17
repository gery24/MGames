-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS tienda_videojuegos;
USE tienda_videojuegos;

-- Eliminar las tablas si existen para evitar conflictos
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;

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
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
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