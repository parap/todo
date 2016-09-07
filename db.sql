CREATE TABLE IF NOT EXISTS `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `todo` varchar(256) NOT NULL,
  `user_id` int(11) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not done, 1 - done',
  `parent_id` int(11) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 - normal, 1 - daily, 2 - weekly, 3 - monthly, 4 - revolver',
  `created_at` datetime NOT NULL,
  `todo_at` datetime NOT NULL,
  `completed_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `completed_at` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;