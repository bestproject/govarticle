<?php

/**
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Based on com_content from Joomla!
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_govarticle'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$url_config = JRoute::_('index.php?option=com_config&view=component&component=com_govarticle');

$params = JComponentHelper::getParams('com_govarticle');
if( !$params->get('save_history','1') ) {
	JFactory::getApplication()->enqueueMessage(JText::plural('COM_GOVARTICLE_WARNING_SAVE_HISTORY_OFF_S', $url_config), 'warning');
}
if( $params->get('history_limit','0')<100 AND $params->get('history_limit','0')>0 ) {
	JFactory::getApplication()->enqueueMessage(JText::plural('COM_GOVARTICLE_WARNING_SAVE_HISTORY_LIMIT_LOW_S', $url_config), 'warning');
}

JLoader::register('GovArticleHelper', __DIR__ . '/helpers/govarticle.php');

$controller = JControllerLegacy::getInstance('GovArticle');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
