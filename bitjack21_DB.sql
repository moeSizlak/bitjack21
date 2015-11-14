-- MySQL dump 10.13  Distrib 5.1.54, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: bj
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4

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
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `player` varchar(256) NOT NULL DEFAULT '',
  `shoe` text,
  `dcards` text,
  `p1cards` text,
  `p2cards` text,
  `p3cards` text,
  `shoenext` int(1) NOT NULL DEFAULT '0',
  `dnext` int(1) NOT NULL DEFAULT '2',
  `p1next` int(1) NOT NULL DEFAULT '2',
  `p2next` int(1) NOT NULL DEFAULT '0',
  `p3next` int(1) NOT NULL DEFAULT '0',
  `bet` int(1) NOT NULL DEFAULT '0',
  `p1double` int(1) NOT NULL DEFAULT '0',
  `p2double` int(1) NOT NULL DEFAULT '0',
  `p3double` int(1) NOT NULL DEFAULT '0',
  `numSplits` int(1) NOT NULL DEFAULT '0',
  `p1score` int(1) NOT NULL DEFAULT '0',
  `p2score` int(1) NOT NULL DEFAULT '0',
  `p3score` int(1) NOT NULL DEFAULT '0',
  `dscore` int(1) NOT NULL DEFAULT '0',
  `priorBalance` float NOT NULL DEFAULT '0',
  `gameID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `startTime` datetime NOT NULL,
  `endTime` datetime DEFAULT NULL,
  `showHit` int(1) NOT NULL DEFAULT '1',
  `showDouble` int(1) NOT NULL DEFAULT '1',
  `showSplit` int(1) NOT NULL DEFAULT '0',
  `currentHand` int(1) NOT NULL DEFAULT '0',
  `showStay` int(1) NOT NULL DEFAULT '1',
  `netGain` float NOT NULL DEFAULT '0',
  `R1` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `R2` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `RX` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`gameID`)
) ENGINE=MyISAM AUTO_INCREMENT=49837 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;

/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `username` varchar(256) NOT NULL,
  `pwhash` varchar(256) DEFAULT NULL,
  `pwsalt` varchar(256) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `wallet` varchar(256) DEFAULT NULL,
  `deposit` varchar(256) DEFAULT NULL,
  `session` varchar(512) DEFAULT NULL,
  `joindate` datetime DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `lastactive` datetime DEFAULT NULL,
  `joinip` varchar(32) DEFAULT NULL,
  `loginip` varchar(32) DEFAULT NULL,
  `nextR1` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `nextRX` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdrawals`
--

DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `withdrawals` (
  `username` varchar(256) NOT NULL,
  `reqdate` datetime NOT NULL,
  `senddate` datetime DEFAULT NULL,
  `txid` varchar(64) DEFAULT NULL,
  `amount` varchar(64) NOT NULL,
  `destination` varchar(64) NOT NULL,
  `idnum` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ipaddr` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`idnum`)
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdrawals`
--

LOCK TABLES `withdrawals` WRITE;
/*!40000 ALTER TABLE `withdrawals` DISABLE KEYS */;

/*!40000 ALTER TABLE `withdrawals` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-09-13 12:15:02
