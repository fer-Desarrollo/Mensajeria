CREATE DATABASE messenger;
USE messenger

CREATE TABLE `archivos` (
   `id` char(36) NOT NULL,
   `mensaje_id` char(36) NOT NULL,
   `nombre_original` varchar(255) NOT NULL,
   `tipo_mime` varchar(100) NOT NULL,
   `tamano_bytes` bigint(20) NOT NULL,
   `storage_key` text NOT NULL,
   `clave_cifrado` text NOT NULL,
   `iv_archivo` varchar(32) NOT NULL,
   `miniatura_url` text DEFAULT NULL,
   `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY (`id`),
   KEY `idx_archivos_mensaje` (`mensaje_id`),
   CONSTRAINT `archivos_ibfk_1` FOREIGN KEY (`mensaje_id`) REFERENCES `mensajes` (`id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
 
 CREATE TABLE `confirmaciones` (
   `mensaje_id` char(36) NOT NULL,
   `usuario_id` char(36) NOT NULL,
   `tipo` varchar(10) NOT NULL,
   `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY (`mensaje_id`,`usuario_id`,`tipo`),
   KEY `usuario_id` (`usuario_id`),
   KEY `idx_confirmaciones_mensaje` (`mensaje_id`),
   CONSTRAINT `confirmaciones_ibfk_1` FOREIGN KEY (`mensaje_id`) REFERENCES `mensajes` (`id`) ON DELETE CASCADE,
   CONSTRAINT `confirmaciones_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
 
 CREATE TABLE `contactos` (
   `usuario_id` char(36) NOT NULL,
   `contacto_id` char(36) NOT NULL,
   `nombre_guardado` varchar(100) DEFAULT NULL,
   `bloqueado` tinyint(1) DEFAULT 0,
   `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY (`usuario_id`,`contacto_id`),
   KEY `fk_contacto_contacto` (`contacto_id`),
   KEY `idx_contactos_usuario` (`usuario_id`),
   CONSTRAINT `fk_contacto_contacto` FOREIGN KEY (`contacto_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
   CONSTRAINT `fk_contacto_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
 
 CREATE TABLE `conversaciones` (
   `id` char(36) NOT NULL,
   `es_grupo` tinyint(1) DEFAULT 0,
   `nombre_grupo` varchar(100) DEFAULT NULL,
   `foto_grupo_url` text DEFAULT NULL,
   `creador_id` char(36) DEFAULT NULL,
   `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY (`id`),
   KEY `fk_conversacion_creador` (`creador_id`),
   CONSTRAINT `fk_conversacion_creador` FOREIGN KEY (`creador_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
 
 CREATE TABLE `mensajes` (
   `id` char(36) NOT NULL,
   `conversacion_id` char(36) NOT NULL,
   `remitente_id` char(36) DEFAULT NULL,
   `tipo` varchar(20) DEFAULT 'texto',
   `contenido_cifrado` text NOT NULL,
   `iv` varchar(32) NOT NULL,
   `responde_a` char(36) DEFAULT NULL,
   `eliminado` tinyint(1) DEFAULT 0,
   `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY (`id`),
   KEY `remitente_id` (`remitente_id`),
   KEY `responde_a` (`responde_a`),
   KEY `idx_mensajes_conversacion` (`conversacion_id`,`fecha_envio`),
   CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones` (`id`) ON DELETE CASCADE,
   CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`remitente_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL,
   CONSTRAINT `mensajes_ibfk_3` FOREIGN KEY (`responde_a`) REFERENCES `mensajes` (`id`) ON DELETE SET NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
 
 CREATE TABLE `participantes` (
   `conversacion_id` char(36) NOT NULL,
   `usuario_id` char(36) NOT NULL,
   `es_admin` tinyint(1) DEFAULT 0,
   `ultima_lectura` timestamp NULL DEFAULT NULL,
   `fecha_union` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY (`conversacion_id`,`usuario_id`),
   KEY `idx_participantes_usuario` (`usuario_id`),
   CONSTRAINT `participantes_ibfk_1` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones` (`id`) ON DELETE CASCADE,
   CONSTRAINT `participantes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
 
 CREATE TABLE `personas` (
   `id` char(36) NOT NULL,
   `nombre_completo` varchar(100) NOT NULL,
   `foto_url` text DEFAULT NULL,
   `fecha_nacimiento` date DEFAULT NULL,
   `genero` varchar(20) DEFAULT NULL,
   `pais` varchar(60) DEFAULT NULL,
   `ciudad` varchar(60) DEFAULT NULL,
   `about` varchar(139) DEFAULT 'Usando la app',
   `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
 
 CREATE TABLE `usuario` (
   `id` char(36) NOT NULL,
   `persona_id` char(36) NOT NULL,
   `nombre_usuario` varchar(50) NOT NULL,
   `email` varchar(150) DEFAULT NULL,
   `telefono` varchar(20) NOT NULL,
   `contrasena_hash` text NOT NULL,
   `llave_publica` text NOT NULL,
   `llave_privada_cifrada` text NOT NULL,
   `en_linea` tinyint(1) DEFAULT 0,
   `ultima_conexion` timestamp NULL DEFAULT NULL,
   `activo` tinyint(1) DEFAULT 1,
   `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
   `password_temporal` tinyint(1) DEFAULT 1,
   PRIMARY KEY (`id`),
   UNIQUE KEY `persona_id` (`persona_id`),
   UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
   UNIQUE KEY `telefono` (`telefono`),
   UNIQUE KEY `email` (`email`),
   KEY `idx_usuario_telefono` (`telefono`),
   KEY `idx_usuario_nombre` (`nombre_usuario`),
   CONSTRAINT `fk_usuario_persona` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci