-- MySQL dump 10.15  Distrib 10.0.14-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: psychomorph
-- ------------------------------------------------------
-- Server version	10.0.14-MariaDB

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
-- Table structure for table `avg_log`
--

DROP TABLE IF EXISTS `avg_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avg_log` (
  `user_id` int(8) unsigned DEFAULT NULL,
  `n` int(4) unsigned DEFAULT NULL,
  `imgtype` enum('jpg','gif','png') DEFAULT 'jpg',
  `avgtype` enum('jpg','gif','png') DEFAULT 'jpg',
  `width` int(5) unsigned DEFAULT NULL,
  `height` int(5) unsigned DEFAULT NULL,
  `norm` enum('none','2point','rigid') DEFAULT 'none',
  `texture` tinyint(1) DEFAULT NULL,
  `contours` tinyint(1) DEFAULT NULL,
  `memory` int(6) unsigned DEFAULT NULL,
  `load_time` int(6) unsigned DEFAULT NULL,
  `make_time` int(7) unsigned DEFAULT NULL,
  `dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fm`
--

DROP TABLE IF EXISTS `fm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fm` (
  `user_id` int(8) unsigned DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `equation` text,
  UNIQUE KEY `user_id` (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `img`
--

DROP TABLE IF EXISTS `img`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `width` int(6) DEFAULT NULL,
  `height` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_name` (`project_id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=123980 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `line`
--

DROP TABLE IF EXISTS `line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `line` (
  `tem_id` int(11) NOT NULL,
  `n` int(4) NOT NULL,
  `linetype` enum('open','closed') DEFAULT 'open',
  `points` varchar(255) NOT NULL,
  `color` varchar(100) DEFAULT 'default',
  UNIQUE KEY `id_n` (`tem_id`,`n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login` (
  `user_id` int(8) unsigned DEFAULT NULL,
  `logintime` datetime DEFAULT NULL,
  `logouttime` datetime DEFAULT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `point`
--

DROP TABLE IF EXISTS `point`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point` (
  `tem_id` int(11) NOT NULL,
  `n` int(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `x` decimal(7,3) DEFAULT NULL,
  `y` decimal(7,3) DEFAULT NULL,
  `sym` int(4) DEFAULT NULL,
  UNIQUE KEY `id_n` (`tem_id`,`n`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pref`
--

DROP TABLE IF EXISTS `pref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pref` (
  `user_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `pref` varchar(255) NOT NULL,
  `prefval` varchar(255) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`pref`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) unsigned DEFAULT NULL,
  `name` varchar(32) NOT NULL,
  `notes` text,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_user`
--

DROP TABLE IF EXISTS `project_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_user` (
  `project_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(8) unsigned DEFAULT NULL,
  UNIQUE KEY `project_id` (`project_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL,
  `type` enum('img') NOT NULL,
  `tag` varchar(255) NOT NULL,
  UNIQUE KEY `id_type_tag` (`id`,`type`,`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tem`
--

DROP TABLE IF EXISTS `tem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notes` text,
  `public` tinyint(1) DEFAULT '0',
  `3ptdelin1` int(4) DEFAULT NULL,
  `3ptdelin2` int(4) DEFAULT NULL,
  `3ptdelin3` int(4) DEFAULT NULL,
  `align_pt1` int(4) DEFAULT NULL,
  `align_pt2` int(4) DEFAULT NULL,
  `width` int(4) DEFAULT NULL,
  `height` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tem_convert`
--

DROP TABLE IF EXISTS `tem_convert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tem_convert` (
  `new_tem` int(11) DEFAULT NULL,
  `n` int(4) DEFAULT NULL,
  `old_tem` int(11) DEFAULT NULL,
  `x` varchar(255) DEFAULT NULL,
  `y` varchar(255) DEFAULT NULL,
  UNIQUE KEY `new_tem` (`new_tem`,`old_tem`,`n`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trans_log`
--

DROP TABLE IF EXISTS `trans_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trans_log` (
  `user_id` int(8) unsigned DEFAULT NULL,
  `imgtype` enum('jpg','gif','png') DEFAULT 'jpg',
  `transtype` enum('jpg','gif','png') DEFAULT 'jpg',
  `shape` int(3) DEFAULT NULL,
  `color` int(3) DEFAULT NULL,
  `texture` int(3) DEFAULT NULL,
  `width` int(5) unsigned DEFAULT NULL,
  `height` int(5) unsigned DEFAULT NULL,
  `norm` enum('none','2point','rigid') DEFAULT 'none',
  `warp` enum('multiscale','linear','tps','multiscalerb') DEFAULT 'multiscale',
  `contours` tinyint(1) DEFAULT NULL,
  `memory` int(6) unsigned DEFAULT NULL,
  `load_time` int(6) unsigned DEFAULT NULL,
  `make_time` int(7) unsigned DEFAULT NULL,
  `dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `sex` enum('female','male','other') DEFAULT NULL,
  `research` tinyint(1) DEFAULT NULL,
  `personal` tinyint(1) DEFAULT NULL,
  `business` tinyint(1) DEFAULT NULL,
  `art` tinyint(1) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  `school` tinyint(1) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `pca` tinyint(1) DEFAULT '0',
  `allocation` int(8) unsigned DEFAULT '1024',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-06 11:57:29
