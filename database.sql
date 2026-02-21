-- MySQL dump 10.13  Distrib 9.6.0, for macos26.2 (arm64)
--
-- Host: localhost    Database: pmdcrm
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

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '3e00c4b8-09cf-11f1-a6a3-49d03bb3d308:1-540';

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
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
  `is_pinned` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `client_links_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_links`
--

LOCK TABLES `client_links` WRITE;
/*!40000 ALTER TABLE `client_links` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_notes`
--

LOCK TABLES `client_notes` WRITE;
/*!40000 ALTER TABLE `client_notes` DISABLE KEYS */;
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
  `foto_perfil` text COLLATE utf8mb4_unicode_ci,
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
  `lead_id` int DEFAULT NULL,
  `nicho` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origem` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `obs` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_clientes_status` (`status_contrato`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (36,'teste','','','','','','','Padrão',1000.00,3,'2026-02-20','2026-05-20','ativo','','2026-02-20',NULL,'2026-02-20 18:50:32',19,'boleto',NULL,'verde','','','',NULL,'','','','','',NULL,'');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'meta_verify_token','vw797defy6fbaq4lmyz7p9','2026-02-17 00:30:42','2026-02-19 17:46:46'),(2,'meta_page_access_token','','2026-02-17 00:30:42','2026-02-17 00:30:42'),(3,'meta_page_id','','2026-02-17 00:30:42','2026-02-17 00:30:42'),(4,'whatsapp_default_msg','teste teste','2026-02-17 00:30:42','2026-02-19 22:09:15'),(5,'theme_color','#32c834','2026-02-17 01:18:36','2026-02-19 22:09:15'),(6,'company_logo','assets/uploads/logos/logo_1771538955.png','2026-02-17 01:18:36','2026-02-19 22:09:15'),(7,'whatsapp_templates_json','[{\"title\":\"teste\",\"text\":\"ola tudo bem?\"},{\"title\":\"Tes 02\",\"text\":\"queo falar agora\"}]','2026-02-17 01:18:36','2026-02-17 18:28:38');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `despesas`
--

LOCK TABLES `despesas` WRITE;
/*!40000 ALTER TABLE `despesas` DISABLE KEYS */;
INSERT INTO `despesas` VALUES (42,'teste 5 meses','vendas','receita',500.00,'2026-03-18','agendado',NULL,'2026-02-18 17:22:02',1,41),(43,'teste 5 meses','vendas','receita',500.00,'2026-04-18','agendado',NULL,'2026-02-18 17:22:02',1,41),(44,'teste 5 meses','vendas','receita',500.00,'2026-05-18','agendado',NULL,'2026-02-18 17:22:02',1,41),(45,'teste 5 meses','vendas','receita',500.00,'2026-06-18','agendado',NULL,'2026-02-18 17:22:02',1,41),(47,'Anuncios','marketing','receita',5000.00,'2026-03-19','agendado',NULL,'2026-02-19 22:02:12',1,46),(48,'Anuncios','marketing','receita',5000.00,'2026-04-19','agendado',NULL,'2026-02-19 22:02:12',1,46),(49,'Anuncios','marketing','receita',5000.00,'2026-05-19','agendado',NULL,'2026-02-19 22:02:12',1,46),(51,'lp','operacional','receita',5000.00,'2026-02-20','pago',NULL,'2026-02-20 21:58:05',1,NULL),(52,'lp','operacional','receita',5000.00,'2026-03-20','agendado',NULL,'2026-02-20 21:58:05',1,51),(53,'lp','operacional','receita',5000.00,'2026-04-20','agendado',NULL,'2026-02-20 21:58:05',1,51),(54,'lp','operacional','receita',5000.00,'2026-05-20','agendado',NULL,'2026-02-20 21:58:05',1,51),(55,'lp','operacional','receita',5000.00,'2026-06-20','agendado',NULL,'2026-02-20 21:58:05',1,51);
/*!40000 ALTER TABLE `despesas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facebook_leads`
--

DROP TABLE IF EXISTS `facebook_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facebook_leads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int DEFAULT NULL,
  `facebook_lead_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facebook_leads`
--

LOCK TABLES `facebook_leads` WRITE;
/*!40000 ALTER TABLE `facebook_leads` DISABLE KEYS */;
INSERT INTO `facebook_leads` VALUES (1,NULL,NULL,NULL,'{\"lead_data\": {\"email\": \"simulado@teste.com\", \"full_name\": \"Simulacao 21:58:02\", \"phone_number\": \"5511999999999\"}, \"is_simulation\": true}','2026-02-17 00:58:02'),(2,NULL,NULL,NULL,'{\"lead_data\": {\"email\": \"simulado@teste.com\", \"full_name\": \"Simulacao 22:01:13\", \"phone_number\": \"5511999999999\"}, \"is_simulation\": true}','2026-02-17 01:01:13'),(3,NULL,NULL,NULL,'{\"lead_data\": {\"email\": \"simulado@teste.com\", \"full_name\": \"Simulacao 15:04:54\", \"phone_number\": \"5511999999999\"}, \"is_simulation\": true}','2026-02-17 18:04:55');
/*!40000 ALTER TABLE `facebook_leads` ENABLE KEYS */;
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
-- Table structure for table `kanban_stages`
--

DROP TABLE IF EXISTS `kanban_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kanban_stages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` int NOT NULL DEFAULT '0',
  `cor` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#cbd5e1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kanban_stages`
--

LOCK TABLES `kanban_stages` WRITE;
/*!40000 ALTER TABLE `kanban_stages` DISABLE KEYS */;
INSERT INTO `kanban_stages` VALUES (1,'Desqualificado',2,'#8f050c','2026-02-16 23:51:45'),(2,'Contato Inicial',0,'#eab308','2026-02-16 23:51:45'),(3,'Reunião agendada',3,'#f97316','2026-02-16 23:51:45'),(4,'Fechado',4,'#22c55e','2026-02-16 23:51:45'),(5,'Follou Up 1',5,'#3c85ec','2026-02-17 15:05:26'),(6,'Follou Up 2',6,'#2d58d7','2026-02-17 17:46:49'),(7,'Follou Up 3',7,'#1356b4','2026-02-17 18:02:59'),(10,'Qualificado',1,'#21c058','2026-02-17 18:44:31'),(12,'Ganhou',8,'#10b981','2026-02-19 16:35:57');
/*!40000 ALTER TABLE `kanban_stages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_history`
--

DROP TABLE IF EXISTS `lead_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int NOT NULL,
  `de_estagio_id` int DEFAULT NULL,
  `para_estagio_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `data_movimentacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  CONSTRAINT `lead_history_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_history`
--

LOCK TABLES `lead_history` WRITE;
/*!40000 ALTER TABLE `lead_history` DISABLE KEYS */;
INSERT INTO `lead_history` VALUES (1,1,1,2,3,'2026-02-16 23:54:23'),(2,1,2,3,2,'2026-02-17 01:17:55'),(4,1,3,2,2,'2026-02-17 14:50:25'),(11,4,1,3,2,'2026-02-17 21:46:29'),(13,4,3,10,2,'2026-02-17 22:35:09'),(14,4,10,2,2,'2026-02-17 22:35:25'),(19,1,2,10,6,'2026-02-18 12:50:36'),(20,1,10,2,6,'2026-02-18 12:57:11'),(33,1,2,3,9,'2026-02-18 19:09:57'),(35,1,3,4,9,'2026-02-18 19:10:01'),(37,1,4,1,9,'2026-02-18 19:14:13'),(41,1,1,3,9,'2026-02-19 22:03:30'),(42,1,3,1,9,'2026-02-19 22:03:34'),(43,1,1,10,9,'2026-02-19 22:03:35'),(45,1,10,2,9,'2026-02-19 22:12:19'),(46,4,2,10,9,'2026-02-19 22:12:20'),(47,4,10,1,9,'2026-02-19 22:12:21'),(48,4,1,10,9,'2026-02-19 22:12:23'),(49,4,10,1,9,'2026-02-19 22:12:25'),(50,4,1,3,9,'2026-02-19 22:12:26'),(51,4,3,10,9,'2026-02-19 22:12:27'),(52,4,10,2,9,'2026-02-20 19:56:54'),(53,4,2,10,9,'2026-02-20 21:57:26'),(54,4,10,1,9,'2026-02-20 21:57:26'),(55,4,1,10,9,'2026-02-20 21:57:27'),(56,4,10,3,9,'2026-02-20 21:58:28'),(57,4,3,4,9,'2026-02-20 21:58:30'),(58,1,2,10,9,'2026-02-20 22:04:35'),(59,1,10,2,9,'2026-02-20 22:10:23'),(60,11,2,10,9,'2026-02-21 13:43:01'),(61,11,10,2,9,'2026-02-21 13:44:48'),(62,12,1,10,9,'2026-02-21 13:44:50'),(63,13,3,10,9,'2026-02-21 13:44:51'),(64,14,5,1,9,'2026-02-21 13:44:53'),(65,4,4,3,9,'2026-02-21 13:44:55'),(66,14,1,2,9,'2026-02-21 13:44:57'),(67,4,3,1,9,'2026-02-21 13:44:58');
/*!40000 ALTER TABLE `lead_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_notes`
--

DROP TABLE IF EXISTS `lead_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `lead_notes_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_notes`
--

LOCK TABLES `lead_notes` WRITE;
/*!40000 ALTER TABLE `lead_notes` DISABLE KEYS */;
INSERT INTO `lead_notes` VALUES (1,1,'Success Verification',2,'2026-02-17 17:38:38'),(5,4,'Verification Note Feb 19 2026 14:00',7,'2026-02-19 17:00:40'),(7,4,'teste',9,'2026-02-19 17:50:05');
/*!40000 ALTER TABLE `lead_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origem` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Manual',
  `valor_estimado` decimal(10,2) DEFAULT '0.00',
  `status_id` int DEFAULT NULL,
  `anotacoes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `kanban_stages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,'Lead Teste Browser','teste@browser.com','11999999999','Manual',5000.00,2,NULL,'2026-02-16 23:54:11','2026-02-20 22:10:23'),(4,'Simulacao 22:01:13','simulado@teste.com','5511999999999','Manual',899.00,1,NULL,'2026-02-17 01:01:13','2026-02-21 13:44:58'),(11,'teste qualificado',NULL,'867564546768','Indicação',899.00,2,NULL,'2026-02-21 13:42:57','2026-02-21 13:44:48'),(12,'teste desqualificado',NULL,'767856477','Indicação',899.00,10,NULL,'2026-02-21 13:43:20','2026-02-21 13:44:50'),(13,'reuniao agndada',NULL,'875674698798','Indicação',899.00,10,NULL,'2026-02-21 13:43:38','2026-02-21 13:44:51'),(14,'flollou up 01',NULL,'786587346785','Indicação',899.00,2,NULL,'2026-02-21 13:43:56','2026-02-21 13:44:57');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@pmdcrm.com','$2y$12$.aYSRnBn9u14cMggeHldOuEW2Uq.jkTBbTFEpeDLZCVooVnu/6Ium','admin',NULL,'2026-02-14 18:27:36'),(2,'Jhone Oliveira Da silva','test@example.com','$2y$12$MdP25Sb6M3w6fuFFq986CO8xWRLbGoTqqswxam8.EfdHT1JeXZsM6','gestor','assets/uploads/img_6994ee3cc6324.png','2026-02-14 20:00:36'),(3,'Test User','test2@example.com','$2y$12$OxXUqrqq3RpqGGDhYyvJpeQEoN0AgL8hzkMWM4lE9PS6sYRC22Bvq','gestor',NULL,'2026-02-14 21:23:54'),(4,'Novo Usuario','novo@usuario.com','$2y$12$ofoC09EDy5RjbVxUfVDXkeU9LFWMBQnWLL9Bioo0mrVMzf3z98A6i','gestor',NULL,'2026-02-17 02:15:37'),(5,'Verifier','verifier@example.com','$2y$12$SNKReEAfR/kgNVtY2R5i/ulPixy5Kn2lLWyScstBMMOTob1f8Qbpy','gestor',NULL,'2026-02-17 17:04:50'),(6,'Admin','superadmin@pmdcrm.com','$2y$12$YdFxMUNpGdbO38AeTUUGAuZYrbguiOnxjRWRTVW.GaKdqw8KDndti','gestor','assets/uploads/img_6995d6a1a1142.png','2026-02-17 17:09:29');
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
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Browser Test','test@test.com',NULL,'2026-02-17 17:35:11','gestor','$2y$12$VDZCMjkqtT0PrcpbTQs3CO3ky7kKWMb3yD9.ejmCDvRejKVt4pqq.',NULL),(2,'Browser Test','test2@test.com',NULL,'2026-02-17 17:36:13','gestor','$2y$12$tE6MoSeE259h5.wwWk9UFupO/2LKq567W6TgPk1bTRhbqzwptcBAK',NULL),(3,'API Tester','api_test_1771350408@test.com',NULL,'2026-02-17 17:46:49','gestor','$2y$12$gyGnDlKBA/pBixnyow4EA.utb3xaCn2UgiemNdis4ofez4VZ2C6B.',NULL),(4,'System Tester','system_test_1234@test.com',NULL,'2026-02-17 17:53:08','gestor','$2y$12$2uw50SFo9u9B1O8dL5PtGuDY.Aq5Q.QNf25Z0IZrSF6Kh9255VRrG',NULL),(5,'Admin Test','admin_test@pmdcrm.com',NULL,'2026-02-17 18:42:52','gestor','$2y$12$atKvaTGp8QgRS7izPjUqful9exJMU1onjuNiOIxKsGCIJZ/w0T0FC',NULL),(6,'Admin','admin@pmdcrm.com',NULL,'2026-02-18 00:33:25','admin','$2y$12$605H5xxeQPCiHEh0cJBKlOVWAxd6xw9Bb4zKitunLHF2hdClRR8ne',NULL),(7,'Admin Test','admin@admin.com',NULL,'2026-02-18 12:53:52','gestor','$2y$12$3hMMr6dzGmhBcxMVbGzNvO.4vOO8e9YjXyoAIUgEc4EjtxxU61yp2',NULL),(8,'Test User','test@example.com',NULL,'2026-02-18 15:07:05','gestor','$2y$12$1D.IMyfFOtnRVGYT4hBLluZvDFBIxZQCwVUlxKxcgro4R0nUCh.n.',NULL),(9,'Jhone Oliveira','jhoneoliveira.17000@gmail.com',NULL,'2026-02-18 15:12:33','gestor','$2y$12$QsMezBme0Aa72jpT/gI92e7irXXGakhqLGOAObu1rKsChyv1hqFpK',NULL),(10,'Theme Tester','theme_tester@example.com',NULL,'2026-02-18 15:33:12','gestor','$2y$12$kzC58rTr2nMAuV3OLjXr4eswfnMUuCrToeVf0yAgrbezaz.J6BhLC',NULL),(11,'Agent Jetski','agent_jetski@test.com',NULL,'2026-02-20 14:16:59','gestor','$2y$12$HzbIb.tA/6QGRBy8EEPfRe8tc3oOB3OqHwObpFPsKpLxxPNfwHCGK',NULL);
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

-- Dump completed on 2026-02-21 11:15:09
