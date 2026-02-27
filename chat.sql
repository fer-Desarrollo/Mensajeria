-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: messenger
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `conversacion_participantes`
--

DROP TABLE IF EXISTS `conversacion_participantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversacion_participantes` (
  `conversacion_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `rol` enum('admin','miembro') NOT NULL DEFAULT 'miembro',
  `joined_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`conversacion_id`,`usuario_id`),
  KEY `idx_cp_usuario` (`usuario_id`),
  KEY `idx_cp_conversacion` (`conversacion_id`),
  CONSTRAINT `fk_cp_conversacion` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversacion_participantes`
--

LOCK TABLES `conversacion_participantes` WRITE;
/*!40000 ALTER TABLE `conversacion_participantes` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversacion_participantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversaciones`
--

DROP TABLE IF EXISTS `conversaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('directa','grupo') NOT NULL,
  `nombre` varchar(120) DEFAULT NULL,
  `created_by_usuario_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_conversaciones_tipo` (`tipo`),
  KEY `idx_conversaciones_creador` (`created_by_usuario_id`),
  CONSTRAINT `fk_conversaciones_creador` FOREIGN KEY (`created_by_usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversaciones`
--

LOCK TABLES `conversaciones` WRITE;
/*!40000 ALTER TABLE `conversaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensaje_adjuntos`
--

DROP TABLE IF EXISTS `mensaje_adjuntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensaje_adjuntos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mensaje_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('imagen','audio','video') NOT NULL,
  `url` text NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `size_bytes` bigint(20) unsigned DEFAULT NULL,
  `duracion_segundos` int(10) unsigned DEFAULT NULL,
  `ancho` int(10) unsigned DEFAULT NULL,
  `alto` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_adjuntos_mensaje` (`mensaje_id`),
  KEY `idx_adjuntos_tipo` (`tipo`),
  CONSTRAINT `fk_adjuntos_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `mensajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensaje_adjuntos`
--

LOCK TABLES `mensaje_adjuntos` WRITE;
/*!40000 ALTER TABLE `mensaje_adjuntos` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensaje_adjuntos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensaje_estados`
--

DROP TABLE IF EXISTS `mensaje_estados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensaje_estados` (
  `mensaje_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `entregado_at` datetime DEFAULT NULL,
  `leido_at` datetime DEFAULT NULL,
  PRIMARY KEY (`mensaje_id`,`usuario_id`),
  KEY `idx_me_usuario` (`usuario_id`),
  KEY `idx_me_leido` (`usuario_id`,`leido_at`),
  CONSTRAINT `fk_me_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `mensajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_me_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensaje_estados`
--

LOCK TABLES `mensaje_estados` WRITE;
/*!40000 ALTER TABLE `mensaje_estados` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensaje_estados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensajes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `conversacion_id` bigint(20) unsigned NOT NULL,
  `remitente_usuario_id` bigint(20) unsigned NOT NULL,
  `texto` text DEFAULT NULL,
  `reply_to_mensaje_id` bigint(20) unsigned DEFAULT NULL,
  `estado` enum('enviado','entregado','leido','eliminado') NOT NULL DEFAULT 'enviado',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_mensajes_conversacion_fecha` (`conversacion_id`,`created_at`),
  KEY `idx_mensajes_remitente_fecha` (`remitente_usuario_id`,`created_at`),
  KEY `idx_mensajes_reply` (`reply_to_mensaje_id`),
  CONSTRAINT `fk_mensajes_conversacion` FOREIGN KEY (`conversacion_id`) REFERENCES `conversaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mensajes_remitente` FOREIGN KEY (`remitente_usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_mensajes_reply` FOREIGN KEY (`reply_to_mensaje_id`) REFERENCES `mensajes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personas`
--

DROP TABLE IF EXISTS `personas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombres` varchar(120) NOT NULL,
  `apellidos` varchar(120) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `foto_perfil_url` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_personas_telefono` (`telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personas`
--

LOCK TABLES `personas` WRITE;
/*!40000 ALTER TABLE `personas` DISABLE KEYS */;
/*!40000 ALTER TABLE `personas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `persona_id` bigint(20) unsigned NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `estado` enum('activo','bloqueado') NOT NULL DEFAULT 'activo',
  `ultimo_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_persona_id` (`persona_id`),
  UNIQUE KEY `uk_usuarios_username` (`username`),
  UNIQUE KEY `uk_usuarios_email` (`email`),
  CONSTRAINT `fk_usuarios_personas` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vw_conversaciones_ultimo_mensaje`
--

DROP TABLE IF EXISTS `vw_conversaciones_ultimo_mensaje`;
/*!50001 DROP VIEW IF EXISTS `vw_conversaciones_ultimo_mensaje`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_conversaciones_ultimo_mensaje` AS SELECT 
 1 AS `conversacion_id`,
 1 AS `tipo`,
 1 AS `nombre`,
 1 AS `conversacion_creada`,
 1 AS `ultimo_mensaje_id`,
 1 AS `ultimo_mensaje_fecha`,
 1 AS `ultimo_mensaje_texto`,
 1 AS `remitente_usuario_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_conversaciones_ultimo_mensaje`
--

/*!50001 DROP VIEW IF EXISTS `vw_conversaciones_ultimo_mensaje`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_conversaciones_ultimo_mensaje` AS select `c`.`id` AS `conversacion_id`,`c`.`tipo` AS `tipo`,`c`.`nombre` AS `nombre`,`c`.`created_at` AS `conversacion_creada`,`m`.`id` AS `ultimo_mensaje_id`,`m`.`created_at` AS `ultimo_mensaje_fecha`,`m`.`texto` AS `ultimo_mensaje_texto`,`m`.`remitente_usuario_id` AS `remitente_usuario_id` from (`conversaciones` `c` left join `mensajes` `m` on(`m`.`id` = (select `m2`.`id` from `mensajes` `m2` where `m2`.`conversacion_id` = `c`.`id` order by `m2`.`created_at` desc,`m2`.`id` desc limit 1))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-26 19:29:28
