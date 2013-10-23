CREATE TABLE IF NOT EXISTS `qa_mailqueue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fromemail` varchar(256) CHARACTER SET ascii DEFAULT NULL,
  `fromname` varchar(256) DEFAULT NULL,
  `toemail` varchar(256) CHARACTER SET ascii DEFAULT NULL,
  `toname` varchar(256) DEFAULT NULL,
  `subject` varchar(800) DEFAULT NULL,
  `body` varchar(8000) DEFAULT NULL,
  `html` tinyint(3) DEFAULT '0',
  `create` datetime NOT NULL,
  `retrycount` tinyint(3) unsigned DEFAULT '0',
  `errorinfo` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
