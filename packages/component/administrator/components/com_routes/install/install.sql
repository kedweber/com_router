-- ----------------------------
--  Table structure for `#__routes`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `#__routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `itemId` int(11) NOT NULL,
  `custom` tinyint(1) NOT NULL DEFAULT '0',
  `redirect` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `lang` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`lang`,`query`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `#__routes_patterns`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `#__routes_patterns` (
  `routes_pattern_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pattern` varchar(255) NOT NULL,
  `component` varchar(50) NOT NULL,
  `view` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`routes_pattern_id`),
  UNIQUE KEY `option` (`component`,`view`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;