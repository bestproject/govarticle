<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_category
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on mod_article_category from Joomla!
 */

defined('_JEXEC') or die;

?>
<ul class="govarticle-category-module<?php echo $moduleclass_sfx; ?>">
	<?php if ($grouped) : ?>
		<?php foreach ($list as $group_name => $group) : ?>
		<li>
			<div class="mod-govarticle-category-group"><?php echo $group_name;?></div>
			<ul>
				<?php foreach ($group as $item) : ?>
					<li>
						<?php if ($params->get('link_titles') == 1) : ?>
							<a class="mod-govarticle-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php echo $item->title; ?>
							</a>
						<?php else : ?>
							<?php echo $item->title; ?>
						<?php endif; ?>
	
						<?php if ($item->displayHits) : ?>
							<span class="mod-govarticle-category-hits">
								(<?php echo $item->displayHits; ?>)
							</span>
						<?php endif; ?>
	
						<?php if ($params->get('show_author')) : ?>
							<span class="mod-govarticle-category-writtenby">
								<?php echo $item->displayAuthorName; ?>
							</span>
						<?php endif;?>
	
						<?php if ($item->displayCategoryTitle) : ?>
							<span class="mod-govarticle-category-category">
								(<?php echo $item->displayCategoryTitle; ?>)
							</span>
						<?php endif; ?>
	
						<?php if ($item->displayDate) : ?>
							<span class="mod-govarticle-category-date"><?php echo $item->displayDate; ?></span>
						<?php endif; ?>
	
						<?php if ($params->get('show_introtext')) : ?>
							<p class="mod-govarticle-category-introtext">
								<?php echo $item->displayIntrotext; ?>
							</p>
						<?php endif; ?>
	
						<?php if ($params->get('show_readmore')) : ?>
							<p class="mod-govarticle-category-readmore">
								<a class="mod-govarticle-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
									<?php if ($item->params->get('access-view') == false) : ?>
										<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
									<?php elseif ($readmore = $item->alternative_readmore) : ?>
										<?php echo $readmore; ?>
										<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
											<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
												<?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>
											<?php endif; ?>
									<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
										<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
									<?php else : ?>
										<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
										<?php echo JHtml::_('string.truncate', ($item->title), $params->get('readmore_limit')); ?>
									<?php endif; ?>
								</a>
							</p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</li>
		<?php endforeach; ?>
	<?php else : ?>
		<?php foreach ($list as $item) : ?>
			<li>
				<?php if ($params->get('link_titles') == 1) : ?>
					<a class="mod-govarticle-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
						<?php echo $item->title; ?>
					</a>
				<?php else : ?>
					<?php echo $item->title; ?>
				<?php endif; ?>
	
				<?php if ($item->displayHits) : ?>
					<span class="mod-govarticle-category-hits">
						(<?php echo $item->displayHits; ?>)
					</span>
				<?php endif; ?>
	
				<?php if ($params->get('show_author')) : ?>
					<span class="mod-govarticle-category-writtenby">
						<?php echo $item->displayAuthorName; ?>
					</span>
				<?php endif;?>
	
				<?php if ($item->displayCategoryTitle) : ?>
					<span class="mod-govarticle-category-category">
						(<?php echo $item->displayCategoryTitle; ?>)
					</span>
				<?php endif; ?>
	
				<?php if ($item->displayDate) : ?>
					<span class="mod-govarticle-category-date">
						<?php echo $item->displayDate; ?>
					</span>
				<?php endif; ?>
	
				<?php if ($params->get('show_introtext')) : ?>
					<p class="mod-govarticle-category-introtext">
						<?php echo $item->displayIntrotext; ?>
					</p>
				<?php endif; ?>
	
				<?php if ($params->get('show_readmore')) : ?>
					<p class="mod-govarticle-category-readmore">
						<a class="mod-govarticle-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
							<?php if ($item->params->get('access-view') == false) : ?>
								<?php echo JText::_('MOD_GOVARTICLE_CATEGORY_REGISTER_TO_READ_MORE'); ?>
							<?php elseif ($readmore = $item->alternative_readmore) : ?>
								<?php echo $readmore; ?>
								<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
							<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
								<?php echo JText::sprintf('MOD_GOVARTICLE_CATEGORY_READ_MORE_TITLE'); ?>
							<?php else : ?>
								<?php echo JText::_('MOD_GOVARTICLE_CATEGORY_READ_MORE'); ?>
								<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
							<?php endif; ?>
						</a>
					</p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>
