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

?>
			<dd class="category-name">
				<span class="icon-folder"></span>
				<?php $title = $this->escape($displayData['item']->category_title); ?>
				<?php if ($displayData['params']->get('link_category') && $displayData['item']->catslug) : ?>
					<?php $url = '<a href="' . JRoute::_(GovArticleHelperRoute::getCategoryRoute($displayData['item']->catslug)) . '" itemprop="genre">' . $title . '</a>'; ?>
					<?php echo JText::sprintf('COM_GOVARTICLE_CATEGORY', $url); ?>
				<?php else : ?>
					<?php echo JText::sprintf('COM_GOVARTICLE_CATEGORY', '<span itemprop="genre">' . $title . '</span>'); ?>
				<?php endif; ?>
			</dd>