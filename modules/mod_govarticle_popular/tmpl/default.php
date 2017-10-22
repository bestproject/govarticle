<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_popular
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2017 Open Source Matters. All rights reserved.
 * @copyright   (C) 2017 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * @link        http://dostepny.joomla.pl
 *
 * Based on mod_article_popular from Joomla!
 */

defined('_JEXEC') or die;
?>
<ul class="govarticle mostread<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
	<li itemscope itemtype="https://schema.org/Article">
		<a href="<?php echo $item->link; ?>" itemprop="url">
			<span itemprop="name">
				<?php echo $item->title; ?>
			</span>
		</a>
	</li>
<?php endforeach; ?>
</ul>
