CREATE DATABASE  IF NOT EXISTS `13_dabkowska` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `13_dabkowska`;
-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
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
  `firstname` varchar(30) default NULL,
  `surname` varchar(45) default NULL,
  `street` varchar(50) default NULL,
  `idcity` int(11) default NULL,
  `idprovince` int(11) default NULL,
  `idcountry` int(11) default NULL,
  PRIMARY KEY  (`iduser_data`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_user_data`
--

LOCK TABLES `ad_user_data` WRITE;
/*!40000 ALTER TABLE `ad_user_data` DISABLE KEYS */;
INSERT INTO `ad_user_data` VALUES (1,1,'Adrianna','Gabis','RÃ³Å¼ 50/5',1,1,1),(36,61,'Mariusz','Majewski','Kolczasta 15/4',3,18,1),(37,2,'Aga','Warszawa','Lolak 50',1,1,1),(39,64,NULL,NULL,NULL,NULL,NULL,NULL),(40,65,NULL,NULL,NULL,NULL,NULL,NULL),(41,66,NULL,NULL,NULL,NULL,NULL,NULL),(42,67,NULL,NULL,NULL,NULL,NULL,NULL),(43,68,NULL,NULL,NULL,NULL,NULL,NULL),(30,55,'Kot','Kot','kotko',1,1,1),(31,56,'Marek','Kida','JabÅ‚oÅ„skie 90/4',1,1,1),(32,57,'Mateusz','Kobra','Metwe 6/4',15,16,1),(33,58,'BoÅ¼ena','Dykiel','Konopnickiej 40/4',1,1,1);
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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_countries`
--

LOCK TABLES `ad_countries` WRITE;
/*!40000 ALTER TABLE `ad_countries` DISABLE KEYS */;
INSERT INTO `ad_countries` VALUES (1,'Poland');
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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_provinces`
--

LOCK TABLES `ad_provinces` WRITE;
/*!40000 ALTER TABLE `ad_provinces` DISABLE KEYS */;
INSERT INTO `ad_provinces` VALUES (1,'mazowieckie'),(4,'zachodnio-pomorskie'),(5,'pomorskie'),(6,'podlaskie'),(7,'kujawsko-pomorskie'),(8,'lubelskie'),(9,'lubuskie'),(12,'opolskie'),(13,'podkarpackie'),(16,'wielkopolskie'),(17,'maÅ‚opolskie'),(18,'warmiÅ„sko-mazurskie'),(19,'dolnoÅ›lÄ…skie'),(20,'Å‚Ã³dzkie'),(21,'Å›wiÄ™tokrzyskie'),(22,'Å›lÄ…skie');
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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_moderator_category`
--

LOCK TABLES `ad_moderator_category` WRITE;
/*!40000 ALTER TABLE `ad_moderator_category` DISABLE KEYS */;
INSERT INTO `ad_moderator_category` VALUES (1,1,57),(2,2,56),(3,15,57),(4,4,58),(13,29,58),(6,6,0),(8,5,0),(11,25,57),(12,27,0);
/*!40000 ALTER TABLE `ad_moderator_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_about`
--

DROP TABLE IF EXISTS `ad_about`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_about` (
  `idabout` int(11) NOT NULL auto_increment,
  `firstname` varchar(45) character set utf8 collate utf8_bin NOT NULL,
  `surname` varchar(45) NOT NULL,
  `content` varchar(150) default NULL,
  `email` varchar(45) default NULL,
  PRIMARY KEY  (`idabout`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_about`
--

LOCK TABLES `ad_about` WRITE;
/*!40000 ALTER TABLE `ad_about` DISABLE KEYS */;
INSERT INTO `ad_about` VALUES (1,'Adrianna','DÄ…bkowska','Projekt serwisu ogÅ‚oszeniowego stworzony na potrzeby \"Systemu interakcyjnego\"','adrianna.dabkowska@uj.edu.pl');
/*!40000 ALTER TABLE `ad_about` ENABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_categories`
--

LOCK TABLES `ad_categories` WRITE;
/*!40000 ALTER TABLE `ad_categories` DISABLE KEYS */;
INSERT INTO `ad_categories` VALUES (1,'Properties'),(2,'Motorisation'),(15,'Electronics'),(4,'Fashion'),(5,'Sport'),(6,'Animals'),(27,'Dziwna kategoria'),(26,'Dziwna kategoria'),(25,'Meble'),(29,'Inne rzeczy'),(28,'Dziwna kategoria'),(30,'Inne rzeczy');
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
  `password` varchar(90) default NULL,
  `email` varchar(40) default NULL,
  PRIMARY KEY  (`iduser`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_users`
--

LOCK TABLES `ad_users` WRITE;
/*!40000 ALTER TABLE `ad_users` DISABLE KEYS */;
INSERT INTO `ad_users` VALUES (1,3,'adad','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','adus@onet.pl'),(2,1,'aga','5fEcfEpWfUd5m4oY6tGxexGaWNS5t6tIHN7lgF9t/Ik8OCRnBPZaKSolAwpwNbmzHhqM962W3lWKadJf4EqLKQ==','aga@onet.pl'),(58,2,'bozena','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','bozena@wp.pl'),(57,2,'mamut','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','mamut@wp.pl'),(56,2,'pies','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','pies@wp.pl'),(55,1,'kot','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','kot@wp.pl'),(61,1,'maniek','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','maniek@wp.pl'),(64,1,'asia','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','asia@wp.pl'),(67,1,'angiee','Eq+l3fvIiUUeGVYrxvkSoSFU/5a5tf1Vmjx+QHIXngebjg4DjIHSBHfFL2eja88EMou7tTjhNV8llvtsZfo0Lg==','angie.patula@gmail.com'),(68,1,'zuberuber','/T157+Yld7KUD2/R8hfLnAokMSV6pG3/EyU3WLY7wXL+SJ45nVteBD11G90w3DGnuyuh6xp9YL8Y8rF65SM6qg==','asasaf@wp.pl');
/*!40000 ALTER TABLE `ad_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_comments`
--

DROP TABLE IF EXISTS `ad_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_comments` (
  `idcomment` int(11) NOT NULL auto_increment,
  `contence` varchar(45) NOT NULL,
  `comment_date` date default NULL,
  `idad` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY  (`idcomment`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_comments`
--

LOCK TABLES `ad_comments` WRITE;
/*!40000 ALTER TABLE `ad_comments` DISABLE KEYS */;
INSERT INTO `ad_comments` VALUES (54,'PiÄ™knie!','2015-08-19',25,64),(52,'Pewnie drogo','2015-08-15',29,44),(51,'Wcale nie, polecam!','2015-08-15',29,1);
/*!40000 ALTER TABLE `ad_comments` ENABLE KEYS */;
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
  `idad` int(11) NOT NULL,
  `photo_name` varchar(60) NOT NULL,
  `photo_alt` varchar(90) default NULL,
  PRIMARY KEY  (`idphotos`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_photos`
--

LOCK TABLES `ad_photos` WRITE;
/*!40000 ALTER TABLE `ad_photos` DISABLE KEYS */;
INSERT INTO `ad_photos` VALUES (7,1,29,'7eg9r2t9sa23ytdw05d8c72yygdvheek.jpg','pokoik'),(8,2,4,'tw7q81epizi04d1zxh69x3hjvbq466pz.jpg','sukienka');
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
INSERT INTO `ad_roles` VALUES (1,'ROLE_USER'),(2,'ROLE_MODERATOR'),(3,'ROLE_ADMIN');
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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_cities`
--

LOCK TABLES `ad_cities` WRITE;
/*!40000 ALTER TABLE `ad_cities` DISABLE KEYS */;
INSERT INTO `ad_cities` VALUES (1,'Warszawa'),(3,'Olsztyn'),(4,'Szczecin'),(16,'Zakopane'),(15,'PoznaÅ„'),(8,'Lublin'),(9,'KrakÃ³w'),(10,'GdaÅ„sk'),(11,'BiaÅ‚ystok'),(12,'ToruÅ„'),(13,'RzeszÃ³w'),(14,'Katowice');
/*!40000 ALTER TABLE `ad_cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ads` (
  `idad` int(11) unsigned NOT NULL auto_increment,
  `iduser` int(11) unsigned default NULL,
  `idcategory` int(11) unsigned default NULL,
  `ad_name` varchar(60) NOT NULL,
  `ad_date` date default NULL,
  `ad_contence` varchar(200) NOT NULL,
  PRIMARY KEY  (`idad`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
INSERT INTO `ads` VALUES (11,1,6,'Oddam w dobre rÄ™ce','2015-06-26','Piesek, miÅ‚y, maÅ‚y, kudÅ‚aty, przyjazny. Kontakt tel.450983489'),(3,1,1,'Domek na wsi','0000-00-00','duzy domek'),(4,2,4,'Sukieneczka','2015-06-27','zwiewna, piÄ™kna, cud miÃ³d'),(29,1,1,'PokÃ³j do wynajÄ™cia','2015-07-06','PiÄ™kny, w gÃ³rach, kontakt 4634634635'),(21,1,5,'Szukam trenera','2015-06-29','MÅ‚oda studentka szuka trenera na wakacje, by zgubiÄ‡ parÄ™ kilogramÃ³w. Kontakt pod tel. 63463294952948574298'),(25,2,4,'Super buty!','2015-07-01','Nowiutkie, rozmiar 40, mÄ™skie, skÃ³rzane.'),(33,2,2,'Sprzedam samochÃ³d!','2015-08-15','Praktycznie nieuÅ¼ywany, w bardzo dobrym stanie.');
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

-- Dump completed on 2015-08-23 19:32:10
