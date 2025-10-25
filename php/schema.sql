-- Schema minimal para LemusPool
-- Crea la base de datos y las tablas necesarias usadas por los endpoints PHP

CREATE DATABASE IF NOT EXISTS `lemuspool_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lemuspool_db`;

-- Tabla de reservas
CREATE TABLE IF NOT EXISTS `reservas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cliente_id` VARCHAR(100) DEFAULT NULL,
  `nombre_cliente` VARCHAR(255) NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `dias_estancia` INT NOT NULL DEFAULT 1,
  `cantidad_personas` INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de mensajes
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) DEFAULT 'No proporcionado',
  `mensaje` TEXT NOT NULL,
  `leido` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_envio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de transacciones (finanzas)
CREATE TABLE IF NOT EXISTS `transacciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fecha` DATE NOT NULL,
  `descripcion` VARCHAR(255) NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `categoria` VARCHAR(100) NOT NULL,
  `tipo` ENUM('ingreso','gasto') NOT NULL,
  `total` DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
