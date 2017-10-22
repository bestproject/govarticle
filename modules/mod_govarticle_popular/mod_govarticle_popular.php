<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_popular
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2017 Open Source Matters. All rights reserved.
 * @copyright   (C) 2017 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Based on mod_article_popular from Joomla!
 */

defined('_JEXEC') or die;

// Include the popular functions only once
JLoader::register('ModGovArticlePopularHelper', __DIR__ . '/helper.php');

$list = ModGovArticlePopularHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

require JModuleHelper::getLayoutPath('mod_govarticle_popular', $params->get('layout', 'default'));
