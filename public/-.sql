
-- -----------------------------
-- Table structure for `webs`
-- -----------------------------
DROP TABLE IF EXISTS `webs`;
CREATE TABLE `webs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '网站',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型（预备）',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `search_link` varchar(255) NOT NULL DEFAULT '' COMMENT '搜索链接',
  `matchs` varchar(6000) NOT NULL DEFAULT '[]' COMMENT '规则',
  `header` varchar(6000) NOT NULL DEFAULT '[]' COMMENT '头部',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='网站';

