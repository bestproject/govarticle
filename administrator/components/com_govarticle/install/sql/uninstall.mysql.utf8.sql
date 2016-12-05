-- Podstawowe ustawienia dezinstalacji

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop content tables for component

DROP TABLE IF EXISTS `#__govarticleitem_tag_map`;
DROP TABLE IF EXISTS `#__govarticle_files`;
DROP TABLE IF EXISTS `#__govarticle_attachments`;
DROP TABLE IF EXISTS `#__govarticle_frontpage`;
DROP TABLE IF EXISTS `#__govarticle_log`;
DROP TABLE IF EXISTS `#__govarticle_rating`;
DROP TABLE IF EXISTS `#__govarticle`;

-- Delete content rows in version history

DELETE FROM #__ucm_history WHERE type_id IN(SELECT type_id FROM #__content_types WHERE type_alias='com_govarticle.article' OR type_alias='com_govarticle.category');
-- Delete content rows in content types

DELETE FROM #__content_types WHERE type_alias IN('com_govarticle.article','com_govarticle.category');

-- Delete content rows in categories

DELETE FROM #__categories WHERE extension='com_govarticle';

-- Przywrócenie poprzednich ustawień

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
