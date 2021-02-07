DROP TABLE IF EXISTS `ptn_config`, `ptn_news`;

CREATE TABLE `ptn_settings` (
  `source` varchar(255) NOT NULL,
  `link_selector` varchar(255) NOT NULL,
  `title_selector` varchar(255) NOT NULL,
  `text_selector` varchar(255) NOT NULL,
  `image_selector` varchar(255) NOT NULL,
  PRIMARY KEY (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `ptn_news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NULL,
  `image` varchar(255) NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (source)  REFERENCES ptn_settings (source)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE INDEX code ON ptn_news(code)