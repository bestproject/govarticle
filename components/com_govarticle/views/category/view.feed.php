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

/**
 * HTML View class for the GovArticle component
 *
 * @since  1.5
 */
class GovArticleViewCategory extends JViewCategoryfeed
{
	/**
	 * @var    string  The name of the view to link individual items to
	 * @since  3.2
	 */
	protected $viewName = 'article';

	/**
	 * Method to reconcile non standard names from components to usage in this class.
	 * Typically overriden in the component feed view class.
	 *
	 * @param   object  $item  The item for a feed, an element of the $items array.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function reconcileNames($item)
	{
		// Get description, author and date
		$app               = JFactory::getApplication();
		$params            = $app->getParams();
		$item->description = $params->get('feed_summary', 0) ? $item->introtext . $item->fulltext : $item->introtext;

		// Add readmore link to description if introtext is shown, show_readmore is true and fulltext exists
		if (!$item->params->get('feed_summary', 0) && $item->params->get('feed_show_readmore', 0) && $item->fulltext)
		{
			// Compute the article slug
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

			// URL link to article
			$link = JRoute::_(GovArticleHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language));

			$item->description .= '<p class="feed-readmore"><a target="_blank" href ="' . $link . '">' . JText::_('COM_GOVARTICLE_FEED_READMORE') . '</a></p>';
		}

		$item->author = $item->created_by_alias ? $item->created_by_alias : $item->author;
	}
}
