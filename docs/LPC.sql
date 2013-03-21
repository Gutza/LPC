-- MySQL dump 10.11
--
-- Host: localhost    Database: lpc_deployment
-- ------------------------------------------------------
-- Server version	5.0.77

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

SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;

--
-- Table structure for table `LPC_cache`
--

DROP TABLE IF EXISTS `LPC_cache`;
CREATE TABLE `LPC_cache` (
  `user` mediumint(8) unsigned NOT NULL,
  `project` mediumint(8) unsigned NOT NULL,
  `name` varchar(245) NOT NULL,
  `value` mediumblob NOT NULL,
  PRIMARY KEY  (`user`,`project`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_group`
--

DROP TABLE IF EXISTS `LPC_group`;
CREATE TABLE `LPC_group` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `type` varchar(50) default NULL,
  `application` varchar(50) default NULL,
  `category` mediumint(8) unsigned NOT NULL,
  `project` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project` (`project`,`type`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO LPC_group (id, name, type, application, category, project) VALUES (1, "Superuser", "permission", "LPC", 0, 0);

--
-- Table structure for table `LPC_group_categories`
--

DROP TABLE IF EXISTS `LPC_group_categories`;
CREATE TABLE `LPC_group_categories` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `short_desc` varchar(255) default NULL,
  `description` text,
  `parent` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_group_membership`
--

DROP TABLE IF EXISTS `LPC_group_membership`;
CREATE TABLE `LPC_group_membership` (
  `group_member` mediumint(8) unsigned NOT NULL,
  `member_to` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`group_member`,`member_to`),
  KEY `member_to` (`member_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_user_membership`
--

DROP TABLE IF EXISTS `LPC_user_membership`;
CREATE TABLE `LPC_user_membership` (
  `user_member` mediumint(8) unsigned NOT NULL,
  `member_to` mediumint(8) unsigned NOT NULL,
  `project` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`project`,`user_member`,`member_to`),
  KEY `project` (`project`,`member_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_language`
--

DROP TABLE IF EXISTS `LPC_language`;
CREATE TABLE `LPC_language` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT 'unknown',
  `translated` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `locale_POSIX` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `locale_POSIX` (`locale_POSIX`),
  KEY `translated` (`translated`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
INSERT INTO `LPC_language` VALUES (1,'English (US)',1,'en_US.UTF-8'), (2,'Română',1,'en_US.UTF-8');

--
-- Table structure for table `LPC_i18n_message`
--

DROP TABLE IF EXISTS `LPC_i18n_message`;
CREATE TABLE `LPC_i18n_message` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `language` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `message_key` varchar(100) NOT NULL DEFAULT '',
  `translation` text,
  PRIMARY KEY (`id`),
  KEY `message_key` (`message_key`,`language`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_i18n_reference`
--

DROP TABLE IF EXISTS `LPC_i18n_reference`;
CREATE TABLE `LPC_i18n_reference` (
  `message_key` varchar(100) NOT NULL DEFAULT '',
  `comment` mediumtext,
  `system` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_scaffolding_default`
--

DROP TABLE IF EXISTS `LPC_scaffolding_default`;
CREATE TABLE `LPC_scaffolding_default` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `className` varchar(100) NOT NULL DEFAULT '',
  `language` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `defaultValue` mediumtext,
  `attName` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `className` (`className`,`attName`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_log`
--

DROP TABLE IF EXISTS `LPC_log`;
CREATE TABLE `LPC_log` (
  `entry_date` datetime DEFAULT NULL,
  `entry_type` varchar(20) DEFAULT NULL,
  `log_class` varchar(30) DEFAULT NULL,
  `log_id` varchar(20) DEFAULT NULL,
  `log_user` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `entry_attrs` longblob,
  `trace` longblob,
  `reason` varchar(50) DEFAULT NULL,
  KEY `entry_date` (`entry_date`,`entry_type`),
  KEY `log_class` (`log_class`,`log_id`,`entry_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_error`
--

DROP TABLE IF EXISTS `LPC_error`;
CREATE TABLE `LPC_error` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message` longtext,
  `type` varchar(50) DEFAULT NULL,
  `date_registered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`date_registered`),
  KEY `date_registered` (`date_registered`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `LPC_scaffold_fld_visi`
--

DROP TABLE IF EXISTS `LPC_scaffold_fld_visi`;
CREATE TABLE `LPC_scaffold_fld_visi` (
  `user` int(10) unsigned NOT NULL DEFAULT '0',
  `class_name` varchar(30) DEFAULT NULL,
  `field_name` varchar(30) DEFAULT NULL,
  `action` enum('force_hide','force_show') NOT NULL DEFAULT 'force_hide',
  KEY `user_class` (`user`,`class_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-11-30 23:14:14
