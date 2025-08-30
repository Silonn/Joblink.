-- Base de Datos para JobLink - v3 Final
-- Añade soft-delete a todas las tablas de CV y elimina ON DELETE CASCADE.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- 1. CREACIÓN DE TABLAS
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('candidato','empresa','administrador') NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cv_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `telefono` varchar(20) DEFAULT NULL,
  `resumen_perfil` TEXT DEFAULT NULL,
  `sitio_web` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `ofertas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `salario` varchar(100) DEFAULT 'A convenir',
  `fecha_publicacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `postulaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_oferta` int NOT NULL,
  `id_candidato` int NOT NULL,
  `fecha_postulacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Pendiente','Visto','En proceso','Rechazado') NOT NULL DEFAULT 'Pendiente',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `cv_experiencia` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `puesto` VARCHAR(255) NOT NULL,
    `empresa` VARCHAR(255) NOT NULL,
    `fecha_inicio` DATE, 
    `fecha_fin` DATE, 
    `descripcion` TEXT,
    `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `cv_educacion` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `institucion` VARCHAR(255) NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `fecha_inicio` DATE, 
    `fecha_fin` DATE,
    `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `cv_habilidades` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `habilidad` VARCHAR(100) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `empresa_portafolio` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_empresa` INT NOT NULL,
    `titulo_proyecto` VARCHAR(255) NOT NULL,
    `descripcion_proyecto` TEXT,
    `imagen_path` VARCHAR(255) NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- 2. INSERCIÓN DE DATOS DE EJEMPLO
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `tipo_usuario`) VALUES
(1, 'Admin', 'admin@joblink.com', '$2y$10$6yVIMw0yoh9AA0qZPPXOiugA33mJJnlsefuEg6Z9b4iAX0HZkvhh2', 'administrador'),
(2, 'Empresa Ejemplo', 'empresa@joblink.com', '$2y$10$Ea1b2c3d4e5f6g7h8i9j0k.l.m.n.o.p.q.r.s.t', 'empresa'),
(3, 'Candidato Ejemplo', 'candidato@joblink.com', '$2y$10$F1g2h3i4j5k6l7m8n9o0p.q.r.s.t.u.v.w.x.y', 'candidato');

--
-- 3. CREACIÓN DE LLAVES FORÁNEAS (CONSTRAINTS)
--

ALTER TABLE `ofertas`
  ADD KEY `id_empresa` (`id_empresa`),
  ADD CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `usuarios` (`id`);

ALTER TABLE `postulaciones`
  ADD KEY `id_oferta` (`id_oferta`),
  ADD KEY `id_candidato` (`id_candidato`),
  ADD CONSTRAINT `postulaciones_ibfk_1` FOREIGN KEY (`id_oferta`) REFERENCES `ofertas` (`id`),
  ADD CONSTRAINT `postulaciones_ibfk_2` FOREIGN KEY (`id_candidato`) REFERENCES `usuarios` (`id`);

ALTER TABLE `cv_experiencia`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD CONSTRAINT `cv_experiencia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`);

ALTER TABLE `cv_educacion`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD CONSTRAINT `cv_educacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`);

ALTER TABLE `cv_habilidades`
  ADD KEY `id_usuario` (`id_usuario`),
  ADD CONSTRAINT `cv_habilidades_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`);

ALTER TABLE `empresa_portafolio`
  ADD KEY `id_empresa` (`id_empresa`),
  ADD CONSTRAINT `empresa_portafolio_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `usuarios`(`id`);

COMMIT;