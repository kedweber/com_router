-- ----------------------------
--  Table structure for `#__routes_patterns`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `#__routes_patterns` (
  `routes_pattern_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `package` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `requirements` text,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`routes_pattern_id`),
  UNIQUE KEY `option` (`package`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;