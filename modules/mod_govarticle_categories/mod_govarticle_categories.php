<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_categories
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on mod_article_categories from Joomla!
 */

defined('_JEXEC') or die;

// Include the helper functions only once
require_once __DIR__ . '/helper.php';

JLoader::register('JCategoryNode', JPATH_BASE . '/libraries/legacy/categories/categories.php');

$cacheid = md5($module->id);

$cacheparams               = new stdClass;
$cacheparams->cachemode    = 'id';
$cacheparams->class        = 'ModGovArticleCategoriesHelper';
$cacheparams->method       = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams   = $cacheid;

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

if (!empty($list))
{
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
	$startLevel      = reset($list)->getParent()->level;
	require JModuleHelper::getLayoutPath('mod_govarticle_categories', $params->get('layout', 'default'));
}
