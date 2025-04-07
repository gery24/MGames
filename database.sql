-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS tienda_videojuegos;
USE tienda_videojuegos;

-- Eliminar las tablas si existen para evitar conflictos
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS lista_deseos;
DROP TABLE IF EXISTS transacciones;

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
    reqmin TEXT,
    reqmax TEXT,
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
    rol ENUM('ADMIN', 'USER') DEFAULT 'USER',
    cartera DECIMAL(10, 2) DEFAULT 0.00
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

-- Tabla de transacciones
CREATE TABLE transacciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    descripcion VARCHAR(255),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
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

-- Modificar la tabla productos para usar los nombres correctos de las columnas
ALTER TABLE productos
DROP COLUMN requisitos_minimos,
DROP COLUMN requisitos_recomendados,
ADD COLUMN reqmin TEXT AFTER descripcion,
ADD COLUMN reqmax TEXT AFTER reqmin;

-- Actualizar los productos existentes con los requisitos
UPDATE productos 
SET reqmin = 'SO: Windows 10 64-bit
Procesador: Intel Core i5-2500K | AMD FX-6300
Memoria: 8 GB RAM
Gráficos: NVIDIA GTX 770 2GB | AMD Radeon R9 280
DirectX: Versión 11
Almacenamiento: 50 GB',
reqmax = 'SO: Windows 10 64-bit
Procesador: Intel Core i7-4770K | AMD Ryzen 5 1500X
Memoria: 16 GB RAM
Gráficos: NVIDIA GTX 1060 6GB | AMD RX 580 8GB
DirectX: Versión 12
Almacenamiento: 50 GB SSD'
WHERE nombre = 'The Last of Us';

UPDATE productos 
SET reqmin = 'SO: Windows 10 64-bit
Procesador: Intel Core i3-6100 | AMD Ryzen 3 1200
Memoria: 8 GB RAM
Gráficos: NVIDIA GTX 950 | AMD Radeon RX 560
DirectX: Versión 11
Almacenamiento: 30 GB',
reqmax = 'SO: Windows 10 64-bit
Procesador: Intel Core i5-8400 | AMD Ryzen 5 2600
Memoria: 16 GB RAM
Gráficos: NVIDIA GTX 1660 | AMD RX 5600 XT
DirectX: Versión 12
Almacenamiento: 30 GB SSD'
WHERE nombre = 'FIFA 24'; 