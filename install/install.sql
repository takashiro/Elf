SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP TABLE IF EXISTS `pre_administrator`;
CREATE TABLE IF NOT EXISTS `pre_administrator` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(15) NOT NULL,
  `pwmd5` varchar(32) NOT NULL,
  `nickname` varchar(50) NULL,
  `permissions` text NULL,
  `logintime` int(11) unsigned NULL,
  `realname` varchar(50) NULL,
  `mobile` varchar(11) NULL,
  `loginkey` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pre_authkey`;
CREATE TABLE IF NOT EXISTS `pre_authkey` (
  `code` varchar(32) NOT NULL,
  `authkey` varchar(32) NOT NULL,
  `expiry` int(11) unsigned NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pre_user`;
CREATE TABLE IF NOT EXISTS `pre_user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `pwmd5` varchar(32) NOT NULL DEFAULT '',
  `nickname` varchar(50) NOT NULL DEFAULT '',
  `realname` varchar(50) NOT NULL DEFAULT '',
  `regtime` int(11) unsigned NOT NULL,
  `formkey` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `logintime` int(11) unsigned DEFAULT NULL,
  `loginkey` smallint(5) unsigned DEFAULT NULL,
  `trickflag` int(11) unsigned NOT NULL DEFAULT '0',
  `avatar` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
