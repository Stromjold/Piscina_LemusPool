-- Schema for LemusPool (Multi-tenant)

CREATE DATABASE IF NOT EXISTS `lemuspool_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lemuspool_db`;

-- Tabla de plantillas (tenants)
CREATE TABLE IF NOT EXISTS `templates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `archivo_html` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `templates` (`nombre`, `archivo_html`) VALUES
('LemusPool', 'Home_Page.html'),
('Arzopa Aqua', 'template_arzopa_aqua.html'),
('Minimalist', 'template_minimalist.html'),
('Tropical', 'template_tropical.html'),
('Luxury', 'template_luxury.html'),
('Family', 'template_family.html');

-- Tabla de usuarios (administradores de cada plantilla)
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  UNIQUE KEY `unique_user_per_template` (`template_id`, `username`),
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default admin for the first template for testing
-- Password is 'admin'
INSERT INTO `usuarios` (`template_id`, `username`, `password`) VALUES (1, 'admin_lemus', '$2y$12$v/7wXZGohewCUreuOT8NlOkVb0qrEBXRj82eDYlnEPxA/55r.RXQa');

-- Tabla de clientes (por plantilla)
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_email_per_template` (`template_id`, `email`),
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de reservas (por plantilla)
DROP TABLE IF EXISTS `reservas`;
CREATE TABLE `reservas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT NOT NULL,
  `cliente_id` INT NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `dias_estancia` INT NOT NULL DEFAULT 1,
  `cantidad_personas` INT NOT NULL DEFAULT 1,
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de mensajes (por plantilla)
DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE `mensajes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) DEFAULT 'No proporcionado',
  `mensaje` TEXT NOT NULL,
  `leido` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_envio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de transacciones (finanzas, por plantilla)
DROP TABLE IF EXISTS `transacciones`;
CREATE TABLE `transacciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `descripcion` VARCHAR(255) NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `categoria` VARCHAR(100) NOT NULL,
  `tipo` ENUM('ingreso','gasto') NOT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
