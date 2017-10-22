<?php

/**
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle.layouts
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 */

defined('JPATH_BASE') or die;

?>
<dd class="producedby" itemprop="produced" itemscope itemtype="http://schema.org/Person">
	<span class="icon-user"></span>
	<?php $produced = '<span itemprop="name">' . $displayData['item']->produced_by . '</span>'; ?>
	<?php echo JText::sprintf('COM_GOVARTICLE_PRODUCED_BY', $produced, $displayData['item']->produced_by_position); ?>
</dd>
