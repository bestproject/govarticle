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

JHtml::_('behavior.framework');

?>
<fieldset class="<?php echo !empty($displayData->formclass) ? $displayData->formclass : 'form-horizontal'; ?>">
	<legend><?php echo $displayData->name ?></legend>
	<?php if (!empty($displayData->description)): ?>
		<p><?php echo $displayData->description; ?></p>
	<?php endif; ?>
	<?php
	$fieldsnames = explode(',', $displayData->fieldsname);
	foreach($fieldsnames as $fieldname)
	{
		foreach ($displayData->form->getFieldset($fieldname) as $field)
		{
			$classnames = 'control-group';
			$rel = '';
			$showon = $displayData->form->getFieldAttribute($field->fieldname, 'showon');
			if (!empty($showon))
			{
				JHtml::_('jquery.framework');
				JHtml::_('script', 'jui/cms.js', false, true);

				$id = $displayData->form->getFormControl();
				$showon = explode(':', $showon, 2);
				$classnames .= ' showon_' . implode(' showon_', explode(',', $showon[1]));
				$rel = ' rel="showon_' . $id . '['. $showon[0] . ']"';
			}
	?>
		<div class="<?php echo $classnames; ?>"<?php echo $rel; ?>>
			<?php if (!isset($displayData->showlabel) || $displayData->showlabel): ?>
				<div class="control-label"><?php echo $field->label; ?></div>
			<?php endif; ?>
			<div class="controls"><?php echo $field->input; ?></div>
		</div>
	<?php
		}
	}
?>
</fieldset>
