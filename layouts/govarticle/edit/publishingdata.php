<?php

/**
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle.layouts
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on com_content layouts from Joomla!
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$form = $displayData->getForm();

$fields = $displayData->get('fields') ?: array(
	'publish_up',
	'publish_down',
	array('created', 'created_time'),
	array('created_by', 'created_user_id'),
	'created_by_alias',
	'produced_by', 
	'produced_by_position',
	array('modified', 'modified_time'),
	array('modified_by', 'modified_user_id'),
	'version',
	'hits',
	'id'
);

$hiddenFields = $displayData->get('hidden_fields') ?: array();

foreach ($fields as $field)
{
	$field = is_array($field) ? $field : array($field);
	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			echo $form->renderField($f);
			break;
		}
	}
}
