<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_log
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (c) 2015, Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$items = ModGovarticleLogHelper::getItems($params);

require JModuleHelper::getLayoutPath('mod_govarticle_log', $params->get('layout', 'default'));
