-- MySQL dump 10.13  Distrib 9.6.0, for macos26.2 (arm64)
--
-- Host: 127.0.0.1    Database: pmdcrm
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '3e00c4b8-09cf-11f1-a6a3-49d03bb3d308:1-135';

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `acao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalhes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (5,19,2,'Adicionou serviço: Meta - Trafego',NULL,'2026-02-14 23:46:18'),(6,19,2,'Adicionou nota (fechamento)',NULL,'2026-02-14 23:46:35'),(7,19,3,'Adicionou serviço: Google Ads (Tráfego Pago, Copywriting)',NULL,'2026-02-15 00:19:54'),(8,19,2,'Adicionou nota (geral)',NULL,'2026-02-16 13:00:08'),(9,19,2,'Alterou risco para amarelo',NULL,'2026-02-16 13:00:24'),(10,19,2,'Alterou risco para vermelho',NULL,'2026-02-16 13:00:25'),(11,19,2,'Alterou risco para verde',NULL,'2026-02-16 13:00:25'),(12,19,2,'Alterou risco para amarelo',NULL,'2026-02-16 13:00:26'),(13,19,2,'Alterou risco para vermelho',NULL,'2026-02-16 13:00:27'),(14,19,2,'Adicionou serviço: Cliente (Tráfego Pago, Social Media, Site / Landing Page, Design, Copywriting)',NULL,'2026-02-16 13:01:10'),(15,19,2,'Adicionou link: google.com',NULL,'2026-02-16 13:02:03');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_links`
--

DROP TABLE IF EXISTS `client_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `client_links_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_links`
--

LOCK TABLES `client_links` WRITE;
/*!40000 ALTER TABLE `client_links` DISABLE KEYS */;
INSERT INTO `client_links` VALUES (2,19,'google.com','google.com','2026-02-16 13:02:03');
/*!40000 ALTER TABLE `client_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_notes`
--

DROP TABLE IF EXISTS `client_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `user_id` int NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('geral','financeiro','fechamento') COLLATE utf8mb4_unicode_ci DEFAULT 'geral',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `client_notes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_notes`
--

LOCK TABLES `client_notes` WRITE;
/*!40000 ALTER TABLE `client_notes` DISABLE KEYS */;
INSERT INTO `client_notes` VALUES (2,19,2,'Fechou 1 contrato','fechamento','2026-02-14 23:46:35'),(3,19,2,'fechou','geral','2026-02-16 13:00:08');
/*!40000 ALTER TABLE `client_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_services`
--

DROP TABLE IF EXISTS `client_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `plataforma` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_servico` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ativo','pausado','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `client_services_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_services`
--

LOCK TABLES `client_services` WRITE;
/*!40000 ALTER TABLE `client_services` DISABLE KEYS */;
INSERT INTO `client_services` VALUES (2,19,'Meta','Trafego','ativo','2026-02-14 23:46:18'),(3,19,'Google Ads','Tráfego Pago','ativo','2026-02-15 00:19:54'),(4,19,'Google Ads','Copywriting','ativo','2026-02-15 00:19:54'),(5,19,'Cliente','Tráfego Pago','ativo','2026-02-16 13:01:10'),(6,19,'Cliente','Social Media','ativo','2026-02-16 13:01:10'),(7,19,'Cliente','Site / Landing Page','ativo','2026-02-16 13:01:10'),(8,19,'Cliente','Design','ativo','2026-02-16 13:01:10'),(9,19,'Cliente','Copywriting','ativo','2026-02-16 13:01:10');
/*!40000 ALTER TABLE `client_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_empresa` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_responsavel` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `segmento` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pasta_drive_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plano_nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_mensal` decimal(10,2) DEFAULT '0.00',
  `periodo_meses` int DEFAULT '12',
  `data_inicio_contrato` date DEFAULT NULL,
  `data_fim_contrato` date DEFAULT NULL,
  `status_contrato` enum('ativo','inativo','cancelado','pausado') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  `canal_aquisicao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_entrada` date NOT NULL,
  `data_cancelamento` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dia_pagamento` int DEFAULT '5',
  `metodo_pagamento` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Pix',
  `ultimo_pagamento` date DEFAULT NULL,
  `status_risco` enum('verde','amarelo','vermelho') COLLATE utf8mb4_unicode_ci DEFAULT 'verde',
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landing_page_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `produto_servico` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_clientes_status` (`status_contrato`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (17,'Status Test Co','Test User','test@status.co','11999999999','','','','Pro',1000.00,12,'2026-02-14','2027-02-14','inativo','Indicação','2026-02-14',NULL,'2026-02-14 22:55:58',5,'Pix',NULL,'verde',NULL,NULL,NULL),(19,'Teste','jhone','teste@gmail.om','1899999999','','','','pro pro',1500.00,3,'2026-02-14','2026-05-14','ativo','Meta Ads','2026-02-14',NULL,'2026-02-14 23:45:24',15,'Boleto',NULL,'vermelho',NULL,NULL,NULL),(20,'Empresa Teste','Test User','test@example.com','11999999999','','','','Pro Mensal',1000.00,12,'2026-02-16','2027-02-16','ativo','Indicação','2026-02-16',NULL,'2026-02-16 18:39:42',5,'Pix',NULL,'verde','','',''),(21,'xxx','xxx','xxxx@gmail.com','99987786785','','','','xxxx',111.00,3,'2026-02-16','2026-05-16','ativo','Indicação','2026-02-16',NULL,'2026-02-16 23:45:25',5,'Pix',NULL,'verde','xxxxx','','xxxx');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `despesas`
--

DROP TABLE IF EXISTS `despesas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `despesas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_despesa` date NOT NULL,
  `status` enum('pago','pendente','atrasado','agendado') COLLATE utf8mb4_unicode_ci DEFAULT 'pago',
  `comprovante_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `recorrente` tinyint(1) DEFAULT '0',
  `id_origem_recorrencia` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_despesas_data` (`data_despesa`),
  KEY `idx_despesas_categoria` (`categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `despesas`
--

LOCK TABLES `despesas` WRITE;
/*!40000 ALTER TABLE `despesas` DISABLE KEYS */;
INSERT INTO `despesas` VALUES (1,'Lp cliente x','Outros','Site cliente x',350.00,'2026-02-14','pago','','2026-02-14 18:34:45',0,NULL),(4,'Hosting Recurrent','Servidor','',200.00,'2026-03-14','agendado',NULL,'2026-02-14 21:38:26',1,3),(5,'Hosting Recurrent','Servidor','',200.00,'2026-04-14','agendado',NULL,'2026-02-14 21:38:26',1,3),(6,'Teste Final','Marketing','',50.00,'2026-02-16','pago',NULL,'2026-02-16 19:01:45',0,NULL),(7,'Teste Final','Marketing','',50.00,'2026-02-16','pago',NULL,'2026-02-16 19:03:04',0,NULL),(9,'xxxx','Marketing','nao',222.00,'2026-02-16','pago',NULL,'2026-02-16 23:44:45',0,NULL);
/*!40000 ALTER TABLE `despesas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financeiro_recorrente`
--

DROP TABLE IF EXISTS `financeiro_recorrente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financeiro_recorrente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `referencia_mes` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `idx_financeiro_mes` (`referencia_mes`),
  CONSTRAINT `financeiro_recorrente_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financeiro_recorrente`
--

LOCK TABLES `financeiro_recorrente` WRITE;
/*!40000 ALTER TABLE `financeiro_recorrente` DISABLE KEYS */;
/*!40000 ALTER TABLE `financeiro_recorrente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','gestor') COLLATE utf8mb4_unicode_ci DEFAULT 'gestor',
  `foto_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@pmdcrm.com','$2y$12$.aYSRnBn9u14cMggeHldOuEW2Uq.jkTBbTFEpeDLZCVooVnu/6Ium','admin',NULL,'2026-02-14 18:27:36'),(2,'Jhone Oliveira Da silva','test@example.com','$2y$12$MdP25Sb6M3w6fuFFq986CO8xWRLbGoTqqswxam8.EfdHT1JeXZsM6','gestor','assets/uploads/img_6991037da644a.png','2026-02-14 20:00:36'),(3,'Test User','test2@example.com','$2y$12$OxXUqrqq3RpqGGDhYyvJpeQEoN0AgL8hzkMWM4lE9PS6sYRC22Bvq','gestor',NULL,'2026-02-14 21:23:54');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-16 20:46:41
