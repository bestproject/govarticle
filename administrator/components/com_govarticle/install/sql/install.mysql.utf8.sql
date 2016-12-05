CREATE TABLE IF NOT EXISTS `#__govarticle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Udostępniony przez',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '' COMMENT 'Udostępniony przez alias',
  `produced_by` varchar(255) NOT NULL DEFAULT '' COMMENT 'Wytworzony przez',
  `produced_by_position` varchar(255) NOT NULL DEFAULT '' COMMENT 'Wytworzony przez stanowisko',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` varchar(5120) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `language` char(7) NOT NULL COMMENT 'The language code for the article.',
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `changes_marker` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_language` (`language`),
  KEY `idx_xreference` (`xreference`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- Struktura tabeli dla tabeli `#__govarticleitem_tag_map`

CREATE TABLE IF NOT EXISTS `#__govarticleitem_tag_map` (
  `type_alias` varchar(255) NOT NULL DEFAULT '',
  `core_content_id` int(10) unsigned NOT NULL COMMENT 'PK from the core content table',
  `content_item_id` int(11) NOT NULL COMMENT 'PK from the content type table',
  `tag_id` int(10) unsigned NOT NULL COMMENT 'PK from the tag table',
  `tag_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date of most recent save for this tag-item',
  `type_id` mediumint(8) NOT NULL COMMENT 'PK from the content_type table',
  UNIQUE KEY `uc_ItemnameTagid` (`type_id`,`content_item_id`,`tag_id`),
  KEY `idx_tag_type` (`tag_id`,`type_id`),
  KEY `idx_date_id` (`tag_date`,`tag_id`),
  KEY `idx_tag` (`tag_id`),
  KEY `idx_type` (`type_id`),
  KEY `idx_core_content_id` (`core_content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maps items from content tables to tags';

-- Struktura tabeli dla tabeli `#__govarticle_attachments`

CREATE TABLE IF NOT EXISTS `#__govarticle_attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`content_id`),
  KEY `fk_govartcile_attachment-article_idx` (`content_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;


-- Struktura tabeli dla tabeli `#__govarticle_files`

CREATE TABLE IF NOT EXISTS `#__govarticle_files` (
  `id` int(10) unsigned NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `filename` varchar(128) NOT NULL DEFAULT '',
  `filename_internal` varchar(128) NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL,
  UNIQUE KEY `uq_govarticle_attachment-version` (`id`,`version`),
  KEY `fk_govarticle_attachment_version-attachment_idx` (`id`),
  KEY `fk_govarticle_file-user_idx` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Struktura tabeli dla tabeli `#__govarticle_frontpage`

CREATE TABLE IF NOT EXISTS `#__govarticle_frontpage` (
  `content_id` int(10) unsigned NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Struktura tabeli dla tabeli `#__govarticle_log`

CREATE TABLE IF NOT EXISTS `#__govarticle_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) unsigned NOT NULL,
  `title` varchar(265) NOT NULL,
  `version_old` int(10) unsigned DEFAULT NULL,
  `version_new` int(10) unsigned NOT NULL,
  `created_by` int(10) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `inspected` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_govarticle_log-article_idx` (`content_id`),
  KEY `fk_govarticle_log-creator_idx` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- Struktura tabeli dla tabeli `#__govarticle_rating`

CREATE TABLE IF NOT EXISTS `#__govarticle_rating` (
  `content_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_sum` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(10) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Klucz obcy dla `#__govarticle_attachments`

ALTER TABLE `#__govarticle_attachments`
  ADD CONSTRAINT `fk_govartcile_attachment-article` FOREIGN KEY (`content_id`) REFERENCES `#__govarticle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Klucz obcy dla `#__govarticle_files`

ALTER TABLE `#__govarticle_files`
  ADD CONSTRAINT `fk_govarticle_attachment_version-attachment` FOREIGN KEY (`id`) REFERENCES `#__govarticle_attachments` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_govarticle_file-user` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

-- Klucz obcy dla `#__govarticle_frontpage`

ALTER TABLE `#__govarticle_frontpage`
  ADD CONSTRAINT `fk_frontpage-article` FOREIGN KEY (`content_id`) REFERENCES `#__govarticle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Klucz obcy dla `#__govarticle_log`

ALTER TABLE `#__govarticle_log`
  ADD CONSTRAINT `fk_govarticle_log-article` FOREIGN KEY (`content_id`) REFERENCES `#__govarticle` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_govarticle_log-creator` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

-- Klucz obcy dla `#__govarticle_rating`

ALTER TABLE `#__govarticle_rating`
  ADD CONSTRAINT `fk_rating-article` FOREIGN KEY (`content_id`) REFERENCES `#__govarticle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Definicja typu treści

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('Article', 'com_govarticle.article', '{"special":{"dbtable":"#__govarticle","key":"id","type":"Content","prefix":"GovArticleTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"introtext", "core_hits":"hits","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"attribs", "core_featured":"featured", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"urls", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"xreference", "asset_id":"asset_id"}, "special":{"fulltext":"fulltext"}}', 'GovArticleHelperRoute::getArticleRoute', '{"formFile":"administrator\\/components\\/com_govarticle\\/models\\/forms\\/article.xml", "hideFields":["asset_id","checked_out","changes_marker","checked_out_time","version","id","created","ordering","version","attribs","urls","images","metakey","metadesc","access","hits","metadata","featured","language","xreference","publish_down","state"],"ignoreChanges":["modified_by","modified","checked_out", "checked_out_time", "version", "hits", "ordering"],"convertToInt":["publish_up", "publish_down", "featured", "ordering"],"displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}'),
('GovArticle Category', 'com_govarticle.category', '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}', 'GovArticleHelperRoute::getCategoryRoute', '{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}');