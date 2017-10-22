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
<dd class="createdby" itemprop="author" itemscope itemtype="http://schema.org/Person">
	<span class="icon-user"></span>
	<?php $author = ($displayData['item']->created_by_alias ? $displayData['item']->created_by_alias : $displayData['item']->author); ?>
	<?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
	<?php if (!empty($displayData['item']->contact_link ) && $displayData['params']->get('link_author') == true) : ?>
		<?php echo JText::sprintf('COM_GOVARTICLE_WRITTEN_BY', JHtml::_('link', $displayData['item']->contact_link, $author, array('itemprop' => 'url'))); ?>
	<?php else :?>
		<?php echo JText::sprintf('COM_GOVARTICLE_WRITTEN_BY', $author); ?>
	<?php endif; ?>
</dd>
