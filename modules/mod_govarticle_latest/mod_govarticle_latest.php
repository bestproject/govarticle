<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_latest
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Based on mod_articles_latest from Joomla!
 */

defined('_JEXEC') or die;

// Include the latest functions only once
JLoader::register('ModGovArticleLatestHelper', __DIR__ . '/helper.php');

$list            = ModGovArticleLatestHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_govarticle_latest', $params->get('layout', 'default'));
