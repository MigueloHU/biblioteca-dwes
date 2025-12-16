-- ------------------------------------------------------
-- Base de datos: bdbiblioteca
-- ------------------------------------------------------
DROP DATABASE IF EXISTS bdbiblioteca;
CREATE DATABASE bdbiblioteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bdbiblioteca;

-- ------------------------------------------------------
-- Tabla: profesores (usuarios)
-- ------------------------------------------------------
DROP TABLE IF EXISTS profesores;
CREATE TABLE profesores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  apellido1 VARCHAR(60) NOT NULL,
  apellido2 VARCHAR(60) DEFAULT NULL,
  nombre VARCHAR(60) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  perfil ENUM('ADMIN','PROFESOR') NOT NULL DEFAULT 'PROFESOR',
  avatar VARCHAR(255) DEFAULT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- NOTA ENUNCIADO: existe un único admin cuyo id será 1
-- Insertamos el admin primero para asegurar id=1
INSERT INTO profesores (apellido1, apellido2, nombre, email, password, perfil, avatar, estado)
VALUES ('Admin', NULL, 'Biblioteca', 'admin@biblioteca.local',
        '$2y$10$u3Zx8mCkF5Yc3Zx8mCkF5uQm6w3J9z6v7fYp3v1d1n5j9V4w1eKcW', -- password: admin1234
        'ADMIN', NULL, 1);

-- ------------------------------------------------------
-- Tabla: libros
-- ------------------------------------------------------
DROP TABLE IF EXISTS libros;
CREATE TABLE libros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  isbn VARCHAR(20) NOT NULL,
  ejemplar VARCHAR(20) NOT NULL,
  portada VARCHAR(255) DEFAULT NULL,
  titulo VARCHAR(200) NOT NULL,
  autor VARCHAR(200) NOT NULL,
  genero VARCHAR(80) DEFAULT NULL,
  anio_publicacion INT DEFAULT NULL,
  editorial VARCHAR(120) DEFAULT NULL,
  descripcion TEXT DEFAULT NULL,
  estado ENUM('DISPONIBLE','PRESTADO') NOT NULL DEFAULT 'DISPONIBLE',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_isbn_ejemplar (isbn, ejemplar)
) ENGINE=InnoDB;

-- ------------------------------------------------------
-- Tabla: prestamos
-- ------------------------------------------------------
DROP TABLE IF EXISTS prestamos;
CREATE TABLE prestamos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_prof INT NOT NULL,
  id_libro INT NOT NULL,
  fecha_inicio DATETIME NOT NULL,
  fecha_entrega DATETIME DEFAULT NULL,
  observaciones VARCHAR(255) DEFAULT NULL,
  estado ENUM('ACTIVO','DEVUELTO') NOT NULL DEFAULT 'ACTIVO',

  CONSTRAINT fk_prestamo_prof FOREIGN KEY (id_prof) REFERENCES profesores(id),
  CONSTRAINT fk_prestamo_libro FOREIGN KEY (id_libro) REFERENCES libros(id)
) ENGINE=InnoDB;

-- Evitar 2 préstamos activos del mismo libro a la vez (regla simple)
CREATE UNIQUE INDEX uk_prestamo_activo_libro
ON prestamos (id_libro, estado);

-- ------------------------------------------------------
-- Tabla: reservas (ampliación/opcional, pero dejamos creada)
-- ------------------------------------------------------
DROP TABLE IF EXISTS reservas;
CREATE TABLE reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_prof INT NOT NULL,
  id_libro INT NOT NULL,
  fecha DATETIME NOT NULL,
  estado ENUM('EN_COLA','ATENDIDA','CANCELADA') NOT NULL DEFAULT 'EN_COLA',

  CONSTRAINT fk_reserva_prof FOREIGN KEY (id_prof) REFERENCES profesores(id),
  CONSTRAINT fk_reserva_libro FOREIGN KEY (id_libro) REFERENCES libros(id)
) ENGINE=InnoDB;

-- ------------------------------------------------------
-- Tabla: log (OBLIGATORIA). Se gestionará con PROCEDIMIENTOS (Fase 4),
-- pero la tabla la creamos ya.
-- ------------------------------------------------------
DROP TABLE IF EXISTS log_actividad;
CREATE TABLE log_actividad (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_prof INT DEFAULT NULL,
  fecha_hora DATETIME NOT NULL,
  tipo ENUM('VISUALIZACION','ALTA','BAJA','ACTUALIZACION') NOT NULL,
  descripcion VARCHAR(255) NOT NULL,

  CONSTRAINT fk_log_prof FOREIGN KEY (id_prof) REFERENCES profesores(id)
) ENGINE=InnoDB;
