-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 26-01-2026 a las 10:48:42
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdbbiblioteca`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `sp_log_insertar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_log_insertar` (IN `p_tipo` VARCHAR(20), IN `p_tabla` VARCHAR(30), IN `p_registro_id` INT, IN `p_profesor_id` INT, IN `p_descripcion` VARCHAR(255))   BEGIN
    INSERT INTO log_actividad (fecha_hora, tipo, tabla_afectada, registro_id, profesor_id, descripcion)
    VALUES (NOW(), p_tipo, p_tabla, p_registro_id, p_profesor_id, p_descripcion);
END$$

DROP PROCEDURE IF EXISTS `sp_log_listar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_log_listar` (IN `p_inicio` INT, IN `p_cantidad` INT)   BEGIN
    SELECT l.id, l.fecha_hora, l.tipo, l.tabla_afectada, l.registro_id,
           p.email, l.descripcion
    FROM log_actividad l
    JOIN profesores p ON p.id = l.profesor_id
    ORDER BY l.id DESC
    LIMIT p_inicio, p_cantidad;
END$$

DROP PROCEDURE IF EXISTS `sp_log_total`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_log_total` ()   BEGIN
    SELECT COUNT(*) AS total FROM log_actividad;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

DROP TABLE IF EXISTS `libros`;
CREATE TABLE IF NOT EXISTS `libros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ejemplar` int NOT NULL DEFAULT '1',
  `portada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autor` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `genero` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_publicacion` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editorial` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) DEFAULT NULL,
  `estado` enum('DISPONIBLE','PRESTADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DISPONIBLE',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_isbn_ejemplar` (`isbn`,`ejemplar`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `isbn`, `ejemplar`, `portada`, `titulo`, `autor`, `genero`, `fecha_publicacion`, `editorial`, `descripcion`, `precio`, `estado`, `creado_en`, `actualizado_en`) VALUES
(2, 'AAAAA', 1, NULL, 'AAAAA', 'AAAA', 'jkljkl', '07/2025', 'jkljkl', 'werwerwer', 40.00, 'DISPONIBLE', '2026-01-23 23:36:58', '2026-01-23 23:37:36'),
(3, 'BBBB', 1, NULL, 'BBBB', 'BBBB', 'BBBB', '07/2020', 'BBBB', 'BBBB', 45.00, 'DISPONIBLE', '2026-01-23 23:42:14', NULL),
(4, 'CCCC', 1, NULL, 'CCCC', 'CCCC', 'CCCC', '09/1922', 'CCCC', 'CCCC', 32.00, 'DISPONIBLE', '2026-01-23 23:51:08', NULL),
(5, 'DDDD', 1, 'assets/img/PortadaLibroKafka.jpg', 'DDDD', 'DDDD', 'DDDD', '06/1982', 'DDDD', 'DDDD', 67.00, 'DISPONIBLE', '2026-01-23 23:51:38', '2026-01-26 11:16:02'),
(6, 'XXXX', 1, 'assets/img/PortadaLibroKafka.jpg', 'EEEE', 'EEEE', 'EEEE', '07/1872', 'EEEE', 'EEEE', 32.00, 'DISPONIBLE', '2026-01-23 23:52:18', '2026-01-26 11:15:50'),
(7, 'FFFF', 1, 'assets/img/PortadaLibroKafka.jpg', 'FFFF', 'FFFF', 'FFFF', '11/1948', 'FFFF', 'FFFF', 43.00, 'PRESTADO', '2026-01-23 23:52:41', '2026-01-26 11:00:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_actividad`
--

DROP TABLE IF EXISTS `log_actividad`;
CREATE TABLE IF NOT EXISTS `log_actividad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` enum('VISUALIZACION','ALTA','BAJA','ACTUALIZACION') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla_afectada` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registro_id` int DEFAULT NULL,
  `profesor_id` int NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_profesor` (`profesor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `log_actividad`
--

INSERT INTO `log_actividad` (`id`, `fecha_hora`, `tipo`, `tabla_afectada`, `registro_id`, `profesor_id`, `descripcion`) VALUES
(1, '2026-01-25 16:42:55', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(2, '2026-01-25 16:44:02', 'ALTA', 'libros', 8, 1, 'Alta de libro: ZZZZ'),
(3, '2026-01-25 16:44:07', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(4, '2026-01-25 16:44:11', 'BAJA', 'libros', 8, 1, 'Baja de libro: ZZZZ'),
(5, '2026-01-25 16:44:11', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(6, '2026-01-25 16:44:24', 'ACTUALIZACION', 'libros', 6, 1, 'Modificación de libro: EEEE'),
(7, '2026-01-25 16:44:26', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(8, '2026-01-26 10:57:15', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(9, '2026-01-26 10:57:19', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(10, '2026-01-26 11:00:56', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(11, '2026-01-26 11:02:00', 'ACTUALIZACION', 'libros', 6, 1, 'Modificación de libro: EEEE'),
(12, '2026-01-26 11:02:04', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(13, '2026-01-26 11:02:29', 'ACTUALIZACION', 'libros', 6, 1, 'Modificación de libro: EEEE'),
(14, '2026-01-26 11:02:31', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(15, '2026-01-26 11:15:15', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(16, '2026-01-26 11:15:17', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(17, '2026-01-26 11:15:36', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(18, '2026-01-26 11:15:44', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(19, '2026-01-26 11:15:50', 'ACTUALIZACION', 'libros', 6, 1, 'Modificación de libro: EEEE'),
(20, '2026-01-26 11:15:53', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros'),
(21, '2026-01-26 11:16:02', 'ACTUALIZACION', 'libros', 5, 1, 'Modificación de libro: DDDD'),
(22, '2026-01-26 11:16:05', 'VISUALIZACION', 'libros', NULL, 1, 'Listado principal de libros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

DROP TABLE IF EXISTS `prestamos`;
CREATE TABLE IF NOT EXISTS `prestamos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profesor_id` int NOT NULL,
  `libro_id` int NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado` enum('ACTIVO','DEVUELTO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id`),
  KEY `fk_prestamos_profesor` (`profesor_id`),
  KEY `fk_prestamos_libro` (`libro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `profesor_id`, `libro_id`, `fecha_inicio`, `fecha_fin`, `observaciones`, `estado`) VALUES
(1, 1, 7, '2026-01-24 00:00:40', '2026-01-24 00:08:01', NULL, 'DEVUELTO'),
(2, 1, 7, '2026-01-24 00:10:08', '2026-01-24 00:20:34', NULL, 'DEVUELTO'),
(3, 1, 7, '2026-01-24 00:39:03', NULL, NULL, 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

DROP TABLE IF EXISTS `profesores`;
CREATE TABLE IF NOT EXISTS `profesores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `apellido1` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido2` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perfil` enum('ADMIN','PROFESOR') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PROFESOR',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id`, `apellido1`, `apellido2`, `nombre`, `email`, `password`, `perfil`, `avatar`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 'Admin', 'Sistema', 'Administrador', 'admin@biblioteca.com', 'admin', 'ADMIN', NULL, 1, '2026-01-23 21:35:16', '2026-01-23 22:35:37'),
(2, 'Calero', 'Contreras', 'Miguel', 'profe@biblioteca.com', 'profe', 'PROFESOR', NULL, 1, '2026-01-24 02:12:01', '2026-01-25 13:52:00'),
(3, 'Indurain', 'Bahomontes', 'Miguel', 'profe@profesor.com', 'profe', 'PROFESOR', NULL, 1, '2026-01-24 02:43:25', '2026-01-25 14:16:08'),
(4, 'García', 'López', 'Ana', 'ana.garcia@biblioteca.com', 'profe', 'PROFESOR', NULL, 1, '2026-01-25 14:48:24', NULL),
(5, 'Martín', 'Ruiz', 'Carlos', 'carlos.martin@biblioteca.com', 'profe', 'PROFESOR', NULL, 1, '2026-01-25 14:48:24', NULL),
(6, 'Sánchez', 'Pérez', 'Laura', 'laura.sanchez@biblioteca.com', 'profe', 'PROFESOR', NULL, 0, '2026-01-25 14:48:24', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE IF NOT EXISTS `reservas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profesor_id` int NOT NULL,
  `libro_id` int NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('EN_ESPERA','NOTIFICADO','CANCELADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ESPERA',
  PRIMARY KEY (`id`),
  KEY `fk_reservas_profesor` (`profesor_id`),
  KEY `fk_reservas_libro` (`libro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `profesor_id`, `libro_id`, `fecha`, `estado`) VALUES
(1, 1, 7, '2026-01-24 00:46:33', 'EN_ESPERA');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `log_actividad`
--
ALTER TABLE `log_actividad`
  ADD CONSTRAINT `fk_log_profesor` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_prestamos_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prestamos_profesor` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reservas_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservas_profesor` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
