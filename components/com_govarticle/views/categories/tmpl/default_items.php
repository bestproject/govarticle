<?php

/**
 * @package     GovArticle
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

JHtml::_('bootstrap.tooltip');

$class = ' class="first"';
$lang  = JFactory::getLanguage();

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
?>
	<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
		<?php
		if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :
		if (!isset($this->items[$this->parent->id][$id + 1]))
		{
			$class = ' class="last"';
		}
		?>
		<div <?php echo $class; ?> >
		<?php $class = ''; ?>
			<h3 class="page-header item-title">
				<a href="<?php echo JRoute::_(GovArticleHelperRoute::getCategoryRoute($item->id));?>">
				<?php echo $this->escape($item->title); ?></a>
				<?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
					<?php if ($item->numitems) :?><!-- dodano sprawdzenie czy sa pozycje -->					
					<!---<span class="badge badge-info tip hasTooltip" title="<?php echo JHtml::tooltipText('COM_GOVARTICLE_NUM_ITEMS'); ?>"> -->
					<span class="badge badge-info pull-right">
						<?php 
						 // dodany tekst: Liczba artykułów
						echo JText::_('COM_GOVARTICLE_NUM_ITEMS');							
						echo $item->numitems; ?>
					</span>
					<?php endif; ?>	<!-- dodany koniec dla sprawdzenia czy sa pozycje -->	
				<?php endif; ?>
				<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) : ?>
					<!-- dodano przesuniecie na prawo i ukryta etykiete Expand -->	
					<a id="category-btn-<?php echo $item->id;?>" href="#category-<?php echo $item->id;?>" 
						data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right"><span class="icon-plus"><span class="hidden">
						<?php echo JText::_('COM_GOVARTICLE_EXPAND'); ?></span></span></a>
				<?php endif;?>
			</h3>
			<?php if ($this->params->get('show_description_image') && $item->getParams()->get('image')) : ?>
				<img src="<?php echo $item->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($item->getParams()->get('image_alt')); ?>" />
			<?php endif; ?>
			<?php if ($this->params->get('show_subcat_desc_cat') == 1) :?>
				<?php if ($item->description) : ?>
					<div class="category-desc">
						<?php echo JHtml::_('content.prepare', $item->description, '', 'com_govarticle.categories'); ?>

				<!-- dodany link: Readmore start -->
					<p class="readmore"><a href="<?php echo JRoute::_(GovArticleHelperRoute::getCategoryRoute($item->id));?>">

					<?php
					echo JText::_('COM_CONTENT_READ_MORE'); 
					echo $this->escape($item->title); ?></a></p>
				<!-- dodany link: Readmore end -->						
						
						
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) :?>
				<div class="collapse fade" id="category-<?php echo $item->id;?>">
				<?php
				$this->items[$item->id] = $item->getChildren();
				$this->parent = $item;
				$this->maxLevelcat--;
				echo $this->loadTemplate('items');
				$this->parent = $item->getParent();
				$this->maxLevelcat++;
				?>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif;
