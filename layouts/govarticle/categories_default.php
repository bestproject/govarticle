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

?>
<?php if ($displayData->params->get('show_page_heading')) : ?>
<h1>
	<?php echo $displayData->escape($displayData->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ($displayData->params->get('show_base_description')) : ?>
	<?php //If there is a description in the menu parameters use that; ?>
		<?php if($displayData->params->get('categories_description')) : ?>
			<div class="category-desc base-desc">
			<?php echo JHtml::_('content.prepare', $displayData->params->get('categories_description'), '',  $displayData->get('extension') . '.categories'); ?>
			</div>
		<?php else : ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($displayData->parent->description) : ?>
				<div class="category-desc base-desc">
					<?php echo JHtml::_('content.prepare', $displayData->parent->description, '', $displayData->parent->extension . '.categories'); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
