<?php
/**
 * @package     GovArticle
 * @subpackage  Content.GovArticleNavigation
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2016 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Based on plg_content_govarticlenavigation from Joomla!
 */

defined('_JEXEC') or die;

$lang = JFactory::getLanguage(); ?>

<ul class="pager pagenav">
<?php if ($row->prev) :
	$direction = $lang->isRtl() ? 'right' : 'left'; ?>
	<li class="previous">
		<a href="<?php echo $row->prev; ?>" rel="prev">
			<?php echo '<span class="icon-chevron-' . $direction . '"></span> ' . $row->prev_label; ?>
		</a>
	</li>
<?php endif; ?>
<?php if ($row->next) :
	$direction = $lang->isRtl() ? 'left' : 'right'; ?>
	<li class="next">
		<a href="<?php echo $row->next; ?>" rel="next">
			<?php echo $row->next_label . ' <span class="icon-chevron-' . $direction . '"></span>'; ?>
		</a>
	</li>
<?php endif; ?>
</ul>
