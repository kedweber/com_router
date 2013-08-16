CREATE TABLE IF NOT EXISTS `#__routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `itemId` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `query` (`query`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__routes_patterns` (
  `routes_pattern_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pattern` varchar(255) NOT NULL,
  `option` varchar(50) NOT NULL,
  `view` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`routes_pattern_id`),
  UNIQUE KEY `option` (`option`,`view`) USING BTREE
) DEFAULT CHARSET=utf8;