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
$published = $this->state->get('filter.published');
?>

<p><?php echo JText::_('COM_GOVARTICLE_BATCH_TIP'); ?></p>
<div class="row-fluid">
	<div class="control-group span6">
		<div class="controls">
			<?php echo JHtml::_('batch.language'); ?>
		</div>
	</div>
	<div class="control-group span6">
		<div class="controls">
			<?php echo JHtml::_('batch.access'); ?>
		</div>
	</div>
</div>
<div class="row-fluid">
	<?php if ($published >= 0) : ?>
		<div class="control-group span6">
			<div class="controls">
				<?php echo JHtml::_('batch.item', 'com_govarticle'); ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="control-group span6">
		<div class="controls">
			<?php echo JHtml::_('batch.tag'); ?>
		</div>
	</div>
</div>
