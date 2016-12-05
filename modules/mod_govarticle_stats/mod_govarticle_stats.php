<?php
/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_stats
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on mod_stats from Joomla!
 */

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$serverinfo      = $params->get('serverinfo');
$siteinfo        = $params->get('siteinfo');
$list            = ModGovArticleStatsHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_govarticle_stats', $params->get('layout', 'default'));
