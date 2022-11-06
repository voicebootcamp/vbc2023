CREATE TABLE IF NOT EXISTS `#__quix_elements` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `params` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) DEFAULT CHARSET=utf8;