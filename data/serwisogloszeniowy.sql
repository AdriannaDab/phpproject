CREATE DATABASE  IF NOT EXISTS `13_dabkowska` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `13_dabkowska`;
-- MySQL dump 10.13  Distrib 5.5.43, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: 13_dabkowska
-- ------------------------------------------------------
-- Server version	5.0.51a-24+lenny5

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Not dumping tablespaces as no INFORMATION_SCHEMA.FILES table on this server
--

--
-- Table structure for table `ad_user_data`
--

DROP TABLE IF EXISTS `ad_user_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_user_data` (
  `iduser_data` int(11) NOT NULL auto_increment,
  `iduser` int(11) default NULL,
  `name` varchar(30) default NULL,
  `surname` varchar(45) default NULL,
  `street` varchar(50) default NULL,
  `idcity` int(11) default NULL,
  `idprovince` int(11) default NULL,
  `idcountry` int(11) default NULL,
  PRIMARY KEY  (`iduser_data`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_user_data`
--

LOCK TABLES `ad_user_data` WRITE;
/*!40000 ALTER TABLE `ad_user_data` DISABLE KEYS */;
INSERT INTO `ad_user_data` VALUES (1,1,'Adrianna','Gab',NULL,1,3,1),(2,2,'Agnieszka','Dab',NULL,1,3,1),(3,3,'Natalia','Lasowska',NULL,2,1,1),(4,4,'Paweł','Polko',NULL,2,1,1),(5,5,'Szymon','Bania',NULL,3,NULL,3),(6,6,'Krzysztof','Rokliński',NULL,3,NULL,3),(7,7,'Paula','Olejuk',NULL,4,NULL,7),(8,8,'Marcin','Ros',NULL,4,NULL,7);
/*!40000 ALTER TABLE `ad_user_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_countries`
--

DROP TABLE IF EXISTS `ad_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_countries` (
  `idcountry` int(11) NOT NULL auto_increment,
  `country_name` varchar(60) NOT NULL,
  PRIMARY KEY  (`idcountry`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_countries`
--

LOCK TABLES `ad_countries` WRITE;
/*!40000 ALTER TABLE `ad_countries` DISABLE KEYS */;
INSERT INTO `ad_countries` VALUES (1,'Poland'),(2,'USA'),(3,'UK'),(4,'Russia'),(5,'Germany'),(6,'India'),(7,'China');
/*!40000 ALTER TABLE `ad_countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_provinces`
--

DROP TABLE IF EXISTS `ad_provinces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_provinces` (
  `idprovince` int(11) NOT NULL auto_increment,
  `province_name` varchar(80) NOT NULL,
  PRIMARY KEY  (`idprovince`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_provinces`
--

LOCK TABLES `ad_provinces` WRITE;
/*!40000 ALTER TABLE `ad_provinces` DISABLE KEYS */;
INSERT INTO `ad_provinces` VALUES (1,'warmińsko-mazurskie'),(2,'małopolskie'),(3,'mazowieckie'),(4,'zachodnio-pomorskie'),(5,'pomorskie'),(6,'podlaskie'),(7,'kujawsko-pomorskie'),(8,'lubelskie'),(9,'lubuskie'),(10,'dolnośląskie'),(11,'śląskie'),(12,'opolskie'),(13,'podkarpackie'),(14,'świętokrzyskie'),(15,'łódzkie'),(16,'wielkopolskie');
/*!40000 ALTER TABLE `ad_provinces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_moderator_category`
--

DROP TABLE IF EXISTS `ad_moderator_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_moderator_category` (
  `idmoderator_category` int(11) NOT NULL auto_increment,
  `idcategory` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY  (`idmoderator_category`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_moderator_category`
--

LOCK TABLES `ad_moderator_category` WRITE;
/*!40000 ALTER TABLE `ad_moderator_category` DISABLE KEYS */;
INSERT INTO `ad_moderator_category` VALUES (1,1,8),(2,2,2),(3,3,3),(4,4,4),(5,5,5),(6,6,6),(7,7,7);
/*!40000 ALTER TABLE `ad_moderator_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_categories`
--

DROP TABLE IF EXISTS `ad_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_categories` (
  `idcategory` int(11) NOT NULL auto_increment,
  `category_name` varchar(60) NOT NULL,
  PRIMARY KEY  (`idcategory`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_categories`
--

LOCK TABLES `ad_categories` WRITE;
/*!40000 ALTER TABLE `ad_categories` DISABLE KEYS */;
INSERT INTO `ad_categories` VALUES (1,'Properties'),(2,'Motorisation'),(3,'Electronics'),(4,'Fashion'),(5,'Sport'),(6,'Animals'),(7,'Furniture');
/*!40000 ALTER TABLE `ad_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_users`
--

DROP TABLE IF EXISTS `ad_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_users` (
  `iduser` int(11) NOT NULL auto_increment,
  `idrole` int(11) default NULL,
  `login` varchar(16) default NULL,
  `password` varchar(64) default NULL,
  `email` varchar(40) default NULL,
  PRIMARY KEY  (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_users`
--

LOCK TABLES `ad_users` WRITE;
/*!40000 ALTER TABLE `ad_users` DISABLE KEYS */;
INSERT INTO `ad_users` VALUES (1,1,'ada','aaa','ada@onet.pl'),(2,2,'aga','bbb','aga@onet.pl'),(3,2,'natalia','bbb','natalia@onet.pl'),(4,2,'pawel','bbb','pawel@onet.pl'),(5,2,'szymon','bbb','szymon@onet.pl'),(6,2,'krzysztof','bbb','krzysztof@onet.pl'),(7,2,'paula','bbb','paula@onet.pl'),(8,2,'marcin','bbb','marcin@onet.pl'),(9,3,'boss','ccc','boss@onet.pl');
/*!40000 ALTER TABLE `ad_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_photos`
--

DROP TABLE IF EXISTS `ad_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_photos` (
  `idphotos` int(11) NOT NULL auto_increment,
  `iduser` int(11) NOT NULL,
  `idcategory` int(11) NOT NULL,
  `idad` int(11) NOT NULL,
  `photo_name` varchar(60) NOT NULL,
  `photo_date` datetime NOT NULL,
  PRIMARY KEY  (`idphotos`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_photos`
--

LOCK TABLES `ad_photos` WRITE;
/*!40000 ALTER TABLE `ad_photos` DISABLE KEYS */;
INSERT INTO `ad_photos` VALUES (1,1,1,1,'Little house','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `ad_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_roles`
--

DROP TABLE IF EXISTS `ad_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_roles` (
  `idrole` int(11) NOT NULL auto_increment,
  `role_name` varchar(45) default NULL,
  PRIMARY KEY  (`idrole`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_roles`
--

LOCK TABLES `ad_roles` WRITE;
/*!40000 ALTER TABLE `ad_roles` DISABLE KEYS */;
INSERT INTO `ad_roles` VALUES (1,'user'),(2,'moderator'),(3,'admin');
/*!40000 ALTER TABLE `ad_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_cities`
--

DROP TABLE IF EXISTS `ad_cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_cities` (
  `idcity` int(11) NOT NULL auto_increment,
  `city_name` varchar(45) NOT NULL,
  PRIMARY KEY  (`idcity`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_cities`
--

LOCK TABLES `ad_cities` WRITE;
/*!40000 ALTER TABLE `ad_cities` DISABLE KEYS */;
INSERT INTO `ad_cities` VALUES (1,'Warsaw'),(2,'Cracow'),(3,'London'),(4,'Hong-kong'),(5,'Moscow'),(6,'Berlin'),(7,'New Delhi');
/*!40000 ALTER TABLE `ad_cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ads` (
  `idad` int(11) NOT NULL auto_increment,
  `iduser` int(11) default NULL,
  `idcategory` int(11) default NULL,
  `ad_name` varchar(60) default NULL,
  `ad_date` datetime default NULL,
  `ad_contence` varchar(200) default NULL,
  PRIMARY KEY  (`idad`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
INSERT INTO `ads` VALUES (1,1,1,'Little house for sale','0000-00-00 00:00:00','Mam do sprzedania domek na wzgórzu.');
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-06-23 17:57:56
