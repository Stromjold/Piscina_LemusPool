CREATE DATABASE IF NOT EXISTS AquaLink_db;
USE AquaLink_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    template_id VARCHAR(50) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS plantillas (
    id VARCHAR(50) PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    img_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    plantilla_interes VARCHAR(50),
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id VARCHAR(50) NOT NULL,
    nombre_cliente VARCHAR(255) NOT NULL,
    fecha_reserva DATE NOT NULL,
    personas INT NOT NULL,
    estado VARCHAR(50) DEFAULT 'Pendiente', -- Pendiente, Confirmada, Cancelada
    FOREIGN KEY (template_id) REFERENCES plantillas(id)
);

CREATE TABLE IF NOT EXISTS mensajes (
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

CREATE TABLE IF NOT EXISTS transacciones (
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
INSERT IGNORE INTO usuarios (username, password, template_id, is_admin) VALUES ('admin', 'password', 'principal', TRUE);

-- Insertar usuarios para cada plantilla
INSERT IGNORE INTO usuarios (username, password, template_id) VALUES 
('admin@lemus.com', 'password', 'lemuspool'),
('admin@arzopa.com', 'password', 'arzopa'),
('admin@family.com', 'password', 'family'),
('admin@luxury.com', 'password', 'luxury'),
('admin@nature.com', 'password', 'nature'),
('admin@retro.com', 'password', 'retro'),
('admin@tropical.com', 'password', 'tropical');

-- Usuario adicional solicitado
INSERT IGNORE INTO usuarios (username, password, template_id) VALUES ('adminAqua@correolink.es', '123456', 'arzopa');

-- Insertar/actualizar administrador global solicitado: admin@aqualink.com / 123456
-- Usamos ON DUPLICATE KEY UPDATE para evitar errores si ya existe el usuario
INSERT INTO usuarios (username, password, template_id, is_admin) VALUES
('admin@aqualink.com', '123456', 'principal', TRUE)
ON DUPLICATE KEY UPDATE password = VALUES(password), template_id = VALUES(template_id), is_admin = VALUES(is_admin);

-- Insertar plantillas
INSERT IGNORE INTO plantillas (id, nombre) VALUES
('principal', 'AquaLink Devs'),
('lemuspool', 'LemusPool'),
('arzopa', 'Arzopa Aqua'),
('family', 'FamilyFun'),
('luxury', 'Luxury'),
('nature', 'Nature Pool'),
('retro', 'Pool Discoteca'),
('tropical', 'TropicalVibes');

-- Tabla para registrar intentos de login (auditoría)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    ip VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN,
    payload TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para guardar cualquier envío de formulario (datos en JSON)
CREATE TABLE IF NOT EXISTS form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(255),
    data JSON,
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
