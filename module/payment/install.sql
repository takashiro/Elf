
ALTER TABLE `pre_user` ADD `wallet` decimal(9,2) NOT NULL DEFAULT '0.0';
ALTER TABLE `pre_user` ADD `lastpaymentmethod` tinyint(4) unsigned NULL;

DROP TABLE IF EXISTS `pre_combinedorder`;
CREATE TABLE IF NOT EXISTS `pre_combinedorder` (
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`userid` mediumint(8) unsigned NOT NULL,
	`price` decimal(9,2) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pre_combinedorderitem`;
CREATE TABLE IF NOT EXISTS `pre_combinedorderitem` (
	`orderid` mediumint(8) unsigned NOT NULL,
	`out_trade_no` varchar(255) NOT NULL,
	INDEX `orderid` (`orderid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pre_prepaidreward`;
CREATE TABLE IF NOT EXISTS `pre_prepaidreward` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `minamount` decimal(9,2) NOT NULL,
  `maxamount` decimal(9,2) NOT NULL,
  `reward` decimal(9,2) NOT NULL,
  `etime_start` int(11) unsigned NOT NULL,
  `etime_end` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pre_userwalletlog`;
CREATE TABLE IF NOT EXISTS `pre_userwalletlog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `dateline` int(11) unsigned NOT NULL,
  `type` tinyint(4) unsigned NOT NULL,
  `delta` decimal(9,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(9,2) NOT NULL DEFAULT '0.00',
  `recharged` tinyint(1) NOT NULL DEFAULT '0',
  `orderid` varchar(255) NULL,
  `paymentmethod` tinyint(4) NULL,
  `tradeid` varchar(255) NULL,
  `tradestate` tinyint(4) NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
