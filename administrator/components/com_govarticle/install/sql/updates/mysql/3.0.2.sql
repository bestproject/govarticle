-- Podstawowe ustawienia instalacji

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Aktualizacja struktury tabeli `#__content_types`

UPDATE #__content_types SET
`type_title` = 'Article',
`type_alias` = 'com_govarticle.article',
`table` = '{"special":{"dbtable":"#__govarticle","key":"id","type":"Content","prefix":"GovArticleTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
`field_mappings` = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"introtext", "core_hits":"hits","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"attribs", "core_featured":"featured", "core_metadata":"metadata", "core_language":"language", "core_images":"images", "core_urls":"urls", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"xreference", "asset_id":"asset_id"}, "special":{"fulltext":"fulltext"}}',
`router` = 'GovArticleHelperRoute::getArticleRoute',
`content_history_options` = '{"formFile":"administrator\\/components\\/com_govarticle\\/models\\/forms\\/article.xml", "hideFields":["asset_id","checked_out","changes_marker","checked_out_time","version","id","created","ordering","version","attribs","urls","images","metakey","metadesc","access","hits","metadata","featured","language","xreference","publish_down","state"],"ignoreChanges":["modified_by","modified","checked_out", "checked_out_time", "version", "hits", "ordering"],"convertToInt":["publish_up", "publish_down", "featured", "ordering"],"displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}'
WHERE `type_alias` = 'com_govarticle.article';

-- Przywrócenie poprzednich ustawień

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
