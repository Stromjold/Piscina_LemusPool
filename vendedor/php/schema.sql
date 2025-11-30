CREATE DATABASE IF NOT EXISTS AquaLink_db;
USE AquaLink_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    template_id VARCHAR(50) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE plantillas (
    id VARCHAR(50) PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    img_url VARCHAR(255)
);

CREATE TABLE solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    plantilla_interes VARCHAR(50),
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE
);

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id VARCHAR(50) NOT NULL,
    nombre_cliente VARCHAR(255) NOT NULL,
    fecha_reserva DATE NOT NULL,
    personas INT NOT NULL,
    estado VARCHAR(50) DEFAULT 'Pendiente', -- Pendiente, Confirmada, Cancelada
    FOREIGN KEY (template_id) REFERENCES plantillas(id)
);

CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(50),
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (template_id) REFERENCES plantillas(id)
);

CREATE TABLE transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id VARCHAR(50) NOT NULL,
    tipo ENUM('ingreso', 'gasto') NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(100),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES plantillas(id)
);

-- Insertar usuario administrador principal
INSERT INTO usuarios (username, password, template_id, is_admin) VALUES ('admin', 'password', 'principal', TRUE);

-- Insertar usuarios para cada plantilla
INSERT INTO usuarios (username, password, template_id) VALUES 
('admin@lemus.com', 'password', 'lemuspool'),
('admin@arzopa.com', 'password', 'arzopa'),
('admin@family.com', 'password', 'family'),
('admin@luxury.com', 'password', 'luxury'),
('admin@nature.com', 'password', 'nature'),
('admin@retro.com', 'password', 'retro'),
('admin@tropical.com', 'password', 'tropical');

-- Usuario adicional solicitado
INSERT INTO usuarios (username, password, template_id) VALUES ('adminAqua@correolink.es', '123456', 'arzopa');

-- Insertar plantillas
INSERT INTO plantillas (id, nombre) VALUES
('principal', 'AquaLink Devs'),
('lemuspool', 'LemusPool'),
('arzopa', 'Arzopa Aqua'),
('family', 'FamilyFun'),
('luxury', 'Luxury'),
('nature', 'Nature Pool'),
('retro', 'Pool Discoteca'),
('tropical', 'TropicalVibes');
