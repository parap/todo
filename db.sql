CREATE TABLE IF NOT EXISTS `daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `completed_at` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=137 ;

-- --------------------------------------------------------

--
-- Структура таблицы `item`
--

CREATE TABLE IF NOT EXISTS `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `user_id` int(11) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not done, 1 - done',
  `parent_id` int(11) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 - normal, 1 - daily, 2 - weekly, 3 - monthly, 4 - revolver',
  `created_at` date NOT NULL,
  `todo_at` datetime NOT NULL,
  `completed_at` date NOT NULL,
  `archived_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=77 ;

CREATE TABLE IF NOT EXISTS `repeat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `number1` smallint(3) DEFAULT NULL,
  `interval_type1` varchar(1) DEFAULT NULL COMMENT 'w - day of week, m - day of month, y - month of year',
  `number2` smallint(3) DEFAULT NULL,
  `interval_type2` varchar(1) DEFAULT NULL COMMENT 'w - day of week, m - day of month, y - month of year',
  `user_id` int(11) NOT NULL,
  `created_at` date NOT NULL DEFAULT '0000-00-00',
  `archived_at` date NOT NULL DEFAULT '0000-00-00',
  `repeat` tinyint(1) NOT NULL DEFAULT  '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;