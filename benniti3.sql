-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: bennit
-- ------------------------------------------------------
-- Server version	8.0.36

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
-- Table structure for table `tbl_credentials`
--

DROP TABLE IF EXISTS `tbl_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_credentials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_trainingpartner_id` int DEFAULT NULL,
  `credential_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_credentials`
--

LOCK TABLES `tbl_credentials` WRITE;
/*!40000 ALTER TABLE `tbl_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_industry`
--

DROP TABLE IF EXISTS `tbl_industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_industry` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_industry`
--

LOCK TABLES `tbl_industry` WRITE;
/*!40000 ALTER TABLE `tbl_industry` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_industry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_matches`
--

DROP TABLE IF EXISTS `tbl_matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_matches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `public_id` varchar(30) NOT NULL,
  `fk_opportunity_id` varchar(30) NOT NULL,
  `fk_solver_id` varchar(30) NOT NULL,
  `matched_by` varchar(30) NOT NULL,
  `seeker_viewed` timestamp NULL DEFAULT NULL,
  `solver_viewed` timestamp NULL DEFAULT NULL,
  `seeker_match` varchar(30) DEFAULT '0',
  `solver_match` varchar(30) DEFAULT '0',
  `matchmaker_approved` varchar(30) DEFAULT '0',
  `seeker_solver_connect` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_matches`
--

LOCK TABLES `tbl_matches` WRITE;
/*!40000 ALTER TABLE `tbl_matches` DISABLE KEYS */;
INSERT INTO `tbl_matches` VALUES (194,'607714764092604916','36','39','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-03-11 07:29:38','2024-03-11 07:29:38'),(195,'607714764310711985','35','39','595811327738383799',NULL,NULL,'595811327738383799','0','0',NULL,'2024-03-11 07:29:38','2024-03-11 07:29:38');
/*!40000 ALTER TABLE `tbl_matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunities`
--

DROP TABLE IF EXISTS `tbl_opportunities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `requirements` longtext,
  `start_date` varchar(255) DEFAULT NULL,
  `complete_date` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `rate_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunities`
--

LOCK TABLES `tbl_opportunities` WRITE;
/*!40000 ALTER TABLE `tbl_opportunities` DISABLE KEYS */;
INSERT INTO `tbl_opportunities` VALUES (33,'25','41','604481292104369054','Need a good php developer','Should be good at php . Should be able to write robust application. Should have 3 years of experience . Should have more experience on designing','2024-03-14','','remote','100','2024-03-02 09:20:58','2024-03-02 09:20:58','na','na','na','na','na','null'),(35,'13','40','604581489425776940','Web Developer 434343','is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.','2024-03-19','','On premise','no','2024-03-02 15:59:07','2024-03-02 15:59:07','remote','remote','',NULL,'','null'),(36,'13','40','605899021462014718','Need a good java developer','Need a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developer','2024-03-06','2025-03-20','On premise','123','2024-03-06 07:14:31','2024-03-06 07:14:31','remote','remote','Columbus','Ohio','43215','per_day'),(37,'25','41','608085235157960462','Anything is ok Anything is ok','Anything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is okAnything is ok Anything is ok','2024-03-12','2024-03-27','remote',NULL,'2024-03-12 08:01:45','2024-03-12 08:01:45','na','na','na','na','na','per_day');
/*!40000 ALTER TABLE `tbl_opportunities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunity_credentials`
--

DROP TABLE IF EXISTS `tbl_opportunity_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunity_credentials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_opportunity_id` int NOT NULL,
  `fk_credential_id` int NOT NULL,
  `completed` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunity_credentials`
--

LOCK TABLES `tbl_opportunity_credentials` WRITE;
/*!40000 ALTER TABLE `tbl_opportunity_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_opportunity_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunity_skills`
--

DROP TABLE IF EXISTS `tbl_opportunity_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunity_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_opportunity_id` int NOT NULL,
  `fk_skill_id` int DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunity_skills`
--

LOCK TABLES `tbl_opportunity_skills` WRITE;
/*!40000 ALTER TABLE `tbl_opportunity_skills` DISABLE KEYS */;
INSERT INTO `tbl_opportunity_skills` VALUES (42,33,31,NULL,NULL),(44,35,35,NULL,NULL),(45,36,36,NULL,NULL),(46,37,37,NULL,NULL);
/*!40000 ALTER TABLE `tbl_opportunity_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_opportunity_smprofiles`
--

DROP TABLE IF EXISTS `tbl_opportunity_smprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_opportunity_smprofiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_opportunity_id` int NOT NULL,
  `fk_profile_id` int NOT NULL,
  `last_activity` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_opportunity_smprofiles`
--

LOCK TABLES `tbl_opportunity_smprofiles` WRITE;
/*!40000 ALTER TABLE `tbl_opportunity_smprofiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_opportunity_smprofiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_organization_users`
--

DROP TABLE IF EXISTS `tbl_organization_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_organization_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_org_id` int NOT NULL,
  `fk_user_id` int NOT NULL,
  `org_level` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_organization_users`
--

LOCK TABLES `tbl_organization_users` WRITE;
/*!40000 ALTER TABLE `tbl_organization_users` DISABLE KEYS */;
INSERT INTO `tbl_organization_users` VALUES (0,1,1,1),(49,40,13,1),(50,41,25,1),(51,1,26,100),(52,1,25,100),(53,1,25,100),(55,43,28,1);
/*!40000 ALTER TABLE `tbl_organization_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_organizations`
--

DROP TABLE IF EXISTS `tbl_organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_organizations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orgname` varchar(255) DEFAULT NULL,
  `creator` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `orgtype` tinyint DEFAULT NULL,
  `description` longtext,
  `location` varchar(255) DEFAULT NULL,
  `precise_location` point DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `social_media` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_organizations`
--

LOCK TABLES `tbl_organizations` WRITE;
/*!40000 ALTER TABLE `tbl_organizations` DISABLE KEYS */;
INSERT INTO `tbl_organizations` VALUES (1,'Bennit Inc','1','1',0,'Default Bennit organization','Novelty, Ohio',NULL,'https://www.bennit.ai',NULL,NULL,NULL,NULL,NULL,'2023-08-16 16:23:01','2023-08-16 16:23:01',NULL),(40,'Axiom','13','604471520768558765',0,'The organization specializes in providing comprehensive services in PHP and Python development. With a team of highly skilled developers and experts in both languages, we deliver tailored solutions to meet our clients\' unique needs.\r\n\r\nOur PHP services cover a wide range of areas, including web development, application programming, content management systems (CMS) development, e-commerce solutions, and more. Leveraging the power and flexibility of PHP frameworks such as Laravel, Symfony, and CodeIgniter, we ensure scalable, secure, and efficient PHP-based solutions for businesses of all sizes.\r\n\r\nIn the realm of Python development, we excel in building robust web applications, data analysis tools, machine learning algorithms, automation scripts, and much more. Our expertise extends across popular Python frameworks like Django, Flask, and Pyramid, enabling us to create high-performance, scalable, and feature-rich Python applications.\r\n\r\nWhether it\'s developing dynamic websites, building custom web applications, implementing sophisticated algorithms, or automating business processes, our organization combines the strengths of PHP and Python to deliver cutting-edge solutions that drive innovation and accelerate business growth.',NULL,NULL,'https://ukrain','Columbus,Ohio','Cleveland','Columbus','Ohio','43215','2024-03-02 08:42:09','2024-03-02 08:42:09',NULL),(41,'Webflow','25','604480763169081953',0,'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',NULL,NULL,'https://ukrain/1','Columbus,Ohio','Cleveland','Columbus','Ohio','43215','2024-03-02 09:18:52','2024-03-02 09:18:52',NULL),(43,'Webflexrr','28','608133196072095120',0,'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum',NULL,NULL,'https://ukrain','Columbus,Ohio','Cleveland','Columbus','Ohio','43215','2024-03-12 11:12:20','2024-03-12 11:12:20','https://social');
/*!40000 ALTER TABLE `tbl_organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_roles`
--

DROP TABLE IF EXISTS `tbl_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_roles` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'role_id',
  `role` varchar(255) DEFAULT NULL COMMENT 'role_text',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_roles`
--

LOCK TABLES `tbl_roles` WRITE;
/*!40000 ALTER TABLE `tbl_roles` DISABLE KEYS */;
INSERT INTO `tbl_roles` VALUES (1,'Admin'),(2,'Editor'),(3,'User');
/*!40000 ALTER TABLE `tbl_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_skills`
--

DROP TABLE IF EXISTS `tbl_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_skills`
--

LOCK TABLES `tbl_skills` WRITE;
/*!40000 ALTER TABLE `tbl_skills` DISABLE KEYS */;
INSERT INTO `tbl_skills` VALUES (31,'php'),(32,'python'),(33,'node js'),(34,'react js'),(35,'react developer'),(36,'java'),(37,'phps');
/*!40000 ALTER TABLE `tbl_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_smprofiles`
--

DROP TABLE IF EXISTS `tbl_smprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_smprofiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(255) NOT NULL,
  `profile_namespace_uri` varchar(255) NOT NULL,
  `profile_marketplace_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_smprofiles`
--

LOCK TABLES `tbl_smprofiles` WRITE;
/*!40000 ALTER TABLE `tbl_smprofiles` DISABLE KEYS */;
INSERT INTO `tbl_smprofiles` VALUES (0,'NCD Wireless Sensors','https://axiomsystems.io/profiles/ncdwireless',2147483647);
/*!40000 ALTER TABLE `tbl_smprofiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_credentials`
--

DROP TABLE IF EXISTS `tbl_solver_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_credentials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_user_id` int NOT NULL,
  `fk_credential_id` int NOT NULL,
  `completed` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_credentials`
--

LOCK TABLES `tbl_solver_credentials` WRITE;
/*!40000 ALTER TABLE `tbl_solver_credentials` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_industry`
--

DROP TABLE IF EXISTS `tbl_solver_industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_industry` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_industry_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_industry_solver` (`fk_solver_id`),
  KEY `fk_solver_industry_industry` (`fk_industry_id`),
  CONSTRAINT `fk_solver_industry_industry` FOREIGN KEY (`fk_industry_id`) REFERENCES `tbl_industry` (`id`),
  CONSTRAINT `fk_solver_industry_solver` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_industry`
--

LOCK TABLES `tbl_solver_industry` WRITE;
/*!40000 ALTER TABLE `tbl_solver_industry` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_industry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_locations`
--

DROP TABLE IF EXISTS `tbl_solver_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_id` (`fk_solver_id`),
  CONSTRAINT `tbl_solver_locations_ibfk_1` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_locations`
--

LOCK TABLES `tbl_solver_locations` WRITE;
/*!40000 ALTER TABLE `tbl_solver_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_skills`
--

DROP TABLE IF EXISTS `tbl_solver_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_skill_id` int NOT NULL,
  `duration` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_skills`
--

LOCK TABLES `tbl_solver_skills` WRITE;
/*!40000 ALTER TABLE `tbl_solver_skills` DISABLE KEYS */;
INSERT INTO `tbl_solver_skills` VALUES (28,36,31,NULL,NULL),(29,36,32,NULL,NULL),(30,37,31,NULL,NULL),(31,37,32,NULL,NULL),(32,38,33,NULL,NULL),(33,39,36,NULL,NULL),(34,40,37,NULL,NULL);
/*!40000 ALTER TABLE `tbl_solver_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_smprofiles`
--

DROP TABLE IF EXISTS `tbl_solver_smprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_smprofiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_profile_id` int NOT NULL,
  `last_activity` int DEFAULT NULL,
  `level` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_smprofiles`
--

LOCK TABLES `tbl_solver_smprofiles` WRITE;
/*!40000 ALTER TABLE `tbl_solver_smprofiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_smprofiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_speciality`
--

DROP TABLE IF EXISTS `tbl_solver_speciality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_speciality` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_speciality_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_speciality_solver` (`fk_solver_id`),
  KEY `fk_solver_speciality_speciality` (`fk_speciality_id`),
  CONSTRAINT `fk_solver_speciality_solver` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`),
  CONSTRAINT `fk_solver_speciality_speciality` FOREIGN KEY (`fk_speciality_id`) REFERENCES `tbl_speciality` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_speciality`
--

LOCK TABLES `tbl_solver_speciality` WRITE;
/*!40000 ALTER TABLE `tbl_solver_speciality` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_speciality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solver_technology`
--

DROP TABLE IF EXISTS `tbl_solver_technology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solver_technology` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_solver_id` int NOT NULL,
  `fk_technology_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_solver_technology_solver` (`fk_solver_id`),
  KEY `fk_solver_technology_technology` (`fk_technology_id`),
  CONSTRAINT `fk_solver_technology_solver` FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers` (`id`),
  CONSTRAINT `fk_solver_technology_technology` FOREIGN KEY (`fk_technology_id`) REFERENCES `tbl_technology` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solver_technology`
--

LOCK TABLES `tbl_solver_technology` WRITE;
/*!40000 ALTER TABLE `tbl_solver_technology` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solver_technology` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solvers`
--

DROP TABLE IF EXISTS `tbl_solvers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solvers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `abstract` varchar(255) DEFAULT NULL,
  `experience` longtext,
  `portraitImage` longblob,
  `bannerImage` longblob,
  `availability` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `locations` varchar(255) DEFAULT NULL,
  `is_coach` bit(1) DEFAULT b'0',
  `allow_external` bit(1) DEFAULT b'0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `certificates` text,
  `location_preference` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `rate_type` varchar(50) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `technology` varchar(255) DEFAULT NULL,
  `speciality` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solvers`
--

LOCK TABLES `tbl_solvers` WRITE;
/*!40000 ALTER TABLE `tbl_solvers` DISABLE KEYS */;
INSERT INTO `tbl_solvers` VALUES (37,'13','40','604477971863964300','Unlocking limitless possibilities with seamless PHP and Python expertise – building robust solutions from front to back.',NULL,'Experience:\r\n\r\nProficient in PHP and Python programming languages.\r\nExtensive hands-on experience with PHP frameworks such as Laravel and CodeIgniter.\r\nSkilled in developing web applications, APIs, and backend systems using PHP.\r\nStrong understanding of Python libraries and frameworks like Django and Flask.\r\nExperienced in database management systems including MySQL and PostgreSQL.\r\nFamiliar with version control systems like Git for collaborative development.\r\nExpertise:\r\n\r\nFull-stack development using PHP and Python, covering both frontend and backend aspects.\r\nDesigning and implementing scalable and efficient solutions for various software projects.\r\nTroubleshooting and debugging complex code to ensure optimal performance and functionality.\r\nIntegrating third-party APIs and services into applications for enhanced functionality.\r\nImplementing security measures and best practices to protect against vulnerabilities.\r\nCollaborating with multidisciplinary teams to deliver high-quality software products.',NULL,NULL,'24/7','140',NULL,_binary '\0',_binary '\0','2024-03-02 09:07:47','2024-03-02 09:07:47','','On premise,hybrid,remote','Columbus','Ohio','43215','per_day',NULL,NULL,NULL),(38,'13','40','604525530724699136','A good Node js developer at your service',NULL,'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.',NULL,NULL,'10 am to 8 pm','10',NULL,_binary '\0',_binary '\0','2024-03-02 12:16:46','2024-03-02 12:16:46','jjk','On premise,hybrid,remote','Columbus','Ohio','43215','per_hour',NULL,NULL,NULL),(39,'26','1','605899445615201845','Java developer',NULL,'Need a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developerNeed a good java developer',NULL,NULL,'24/7','140',NULL,_binary '\0',_binary '\0','2024-03-06 07:16:12','2024-03-06 07:16:12','jjk','On premise,hybrid,remote','Columbus','Ohio','43215','per_day',NULL,NULL,NULL);
/*!40000 ALTER TABLE `tbl_solvers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_speciality`
--

DROP TABLE IF EXISTS `tbl_speciality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_speciality` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_speciality`
--

LOCK TABLES `tbl_speciality` WRITE;
/*!40000 ALTER TABLE `tbl_speciality` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_speciality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_subscriptions`
--

DROP TABLE IF EXISTS `tbl_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `public_id` varchar(30) NOT NULL,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) DEFAULT NULL,
  `subscription_type` varchar(255) DEFAULT NULL,
  `purchase_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `canceled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_subscriptions`
--

LOCK TABLES `tbl_subscriptions` WRITE;
/*!40000 ALTER TABLE `tbl_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_technology`
--

DROP TABLE IF EXISTS `tbl_technology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_technology` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_technology`
--

LOCK TABLES `tbl_technology` WRITE;
/*!40000 ALTER TABLE `tbl_technology` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_technology` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `public_id` varchar(30) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `roleid` tinyint DEFAULT NULL,
  `is_disabled` bit(1) DEFAULT b'0',
  `is_firstrun` bit(1) DEFAULT b'1',
  `stripe_id` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_users`
--

LOCK TABLES `tbl_users` WRITE;
/*!40000 ALTER TABLE `tbl_users` DISABLE KEYS */;
INSERT INTO `tbl_users` VALUES (2,'','Jonathan Wise','jwise','jonathan@bennit.ai','8bc7fa56ac4a66d2cb0fc781d213c14f2d518e2be0e1fe65e83231514420f011','2167721051',1,_binary '\0',_binary '\0',NULL,'2023-08-16 16:23:01','2023-08-16 16:23:01'),(3,'','Guest User','guest','guest@bennit.ai','8016040fc911a0900c62d0da720ff13114f845d6eb84a923bb86537ec5896081','5551234',3,_binary '',_binary '\0',NULL,'2023-08-06 19:32:27','2023-08-06 19:32:27'),(10,'594283096145137493','Shahid Ali','shahidali','shahid451998@gmail.com','59fe1c215dff079f386bb52a4801cd61f723fd76dd10d5a27f3b6e58e4bc43c4','7908169084',1,_binary '\0',_binary '',NULL,'2024-02-03 05:56:59','2024-02-03 05:56:59'),(11,'594284322131804173','Shahid Editor','shahideditor','editor@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-02-03 06:01:51','2024-02-03 06:01:51'),(12,'595380632893262578','testingthis','testingthis','testtwo@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','7908169084',3,_binary '\0',_binary '',NULL,'2024-02-06 06:38:12','2024-02-06 06:38:12'),(13,'595811327738383799','Test Case','TestCase','testcase@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','7908169084',3,_binary '\0',_binary '',NULL,'2024-02-07 11:09:38','2024-02-07 11:09:38'),(25,'604480362281700582','seekeriseeker','seekeriseeker','seeker@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-03-02 09:17:17','2024-03-02 09:17:17'),(26,'604524969161916542','solverissolver','solverissolver','solver@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','1234567890',3,_binary '\0',_binary '',NULL,'2024-03-02 12:14:32','2024-03-02 12:14:32'),(27,'606031467411148114','Kathy Cahalane','KathyCahalane','kathy@bennit.ai','cb27ee9e5e1248484c6935fa3546a235c230e5d30eef7f10a4ae2ef1b033d84e','1234567890',1,_binary '\0',_binary '',NULL,'2024-03-06 16:00:49','2024-03-06 16:00:49'),(28,'608089440648170392','newtest','newtest','newday2@gmail.com','dad387e8454b9098b6c355ba51a8af419550d4f08ebb3984d907234e716b749b','7908169084',3,_binary '\0',_binary '',NULL,'2024-03-12 08:18:28','2024-03-12 08:18:28');
/*!40000 ALTER TABLE `tbl_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-12 16:45:18
