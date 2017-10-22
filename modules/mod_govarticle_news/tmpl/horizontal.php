<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_news
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2017 Open Source Matters. All rights reserved.
 * @copyright   (C) 2017 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Based on mod_article_news from Joomla!
 */

defined('_JEXEC') or die;
?>
<ul class="govarticle newsflash-horiz<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php for ($i = 0, $n = count($list); $i < $n; $i ++) : ?>
		<?php $item = $list[$i]; ?>
		<li>
			<?php require JModuleHelper::getLayoutPath('mod_govarticle_news', '_item'); ?>

			<?php if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>
				<span class="article-separator">&#160;</span>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
</ul>
