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

defined('JPATH_BASE') or die;

$blockPosition = $displayData['params']->get('info_block_position', 0);

?>
	<dl class="article-info muted">

		<?php if ($displayData['position'] == 'above' && ($blockPosition == 0 || $blockPosition == 2)
				|| $displayData['position'] == 'below' && ($blockPosition == 1)
				) : ?>

			<dt class="article-info-term">
				<?php if ($displayData['params']->get('info_block_show_title', 1)) : ?>
					<?php echo JText::_('COM_GOVARTICLE_ARTICLE_INFO'); ?>
				<?php endif; ?>
			</dt>

			<?php if ($displayData['params']->get('show_author') && !empty($displayData['item']->author )) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.author', $displayData); ?>
			<?php endif; ?>
			
			<?php //if ($displayData['params']->get('show_produced',1) && !empty($displayData['item']->produced_by )) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.produced', $displayData); ?>
			<?php //endif; ?>

			<?php if ($displayData['params']->get('show_parent_category') && !empty($displayData['item']->parent_slug)) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.parent_category', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_category')) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.category', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_publish_date')) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.publish_date', $displayData); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($displayData['position'] == 'above' && ($blockPosition == 0)
				|| $displayData['position'] == 'below' && ($blockPosition == 1 || $blockPosition == 2)
				) : ?>
			<?php if ($displayData['params']->get('show_create_date')) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.create_date', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_modify_date')) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.modify_date', $displayData); ?>
			<?php endif; ?>

			<?php if ($displayData['params']->get('show_hits')) : ?>
				<?php echo JLayoutHelper::render('govarticle.info_block.hits', $displayData); ?>
			<?php endif; ?>
		<?php endif; ?>
	</dl>
