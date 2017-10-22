<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_news
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2017 Open Source Matters. All rights reserved.
 * @copyright   (C) 2017 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Based on mod_article_news from Joomla!
 */

defined('_JEXEC') or die;

// Include the news functions only once
JLoader::register('ModGovArticleNewsHelper', __DIR__ . '/helper.php');

$list            = ModGovArticleNewsHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_govarticle_news', $params->get('layout', 'horizontal'));
