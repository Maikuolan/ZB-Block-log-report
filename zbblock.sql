-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.6.17-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.4.0.5161
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table test.zbblock
CREATE TABLE IF NOT EXISTS `zbblock` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `why` varchar(50) DEFAULT NULL,
  `total` mediumint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

-- Dumping data for table test.zbblock: 112 rows
/*!40000 ALTER TABLE `zbblock` DISABLE KEYS */;
INSERT INTO `zbblock` (`id`, `date`, `type`, `why`, `total`) VALUES
	(12, '0', 4, 'kyivstar', 0),
	(11, '0', 4, 'your-server.de', 0),
	(10, '0', 4, 'Grapeshot', 0),
	(9, '0', 4, 'Semrush ', 0),
	(8, '0', 4, 'Robot probe', 0),
	(7, '0', 4, ' Test Trigger', 0),
	(6, '0', 4, ' hostile scraper', 0),
	(5, '0', 4, 'Nobis', 0),
	(4, '0', 4, 'Cloud Services', 0),
	(3, '0', 4, 'Bothost', 0),
	(2, '0', 4, 'Amazon', 0),
	(1, '0', 4, 'Baidu', 0),
	(14, '0', 5, NULL, 0),
	(13, '0', 4, 'ADmantX', 0);
/*!40000 ALTER TABLE `zbblock` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
