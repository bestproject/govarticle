<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_categories
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on mod_article_categories from Joomla!
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_govarticle/helpers/route.php';

abstract class ModGovArticleCategoriesHelper {

	public static function getList(&$params) {
		$options = array();
		$options['countItems'] = $params->get('numitems', 0);

		$categories = JCategories::getInstance('GovArticle', $options);
		$category = $categories->get($params->get('parent', 'root'));

		if ($category != null) {
			$items = $category->getChildren();

			if ($params->get('count', 0) > 0 && count($items) > $params->get('count', 0)) {
				$items = array_slice($items, 0, $params->get('count', 0));
			}

			return $items;
		}
	}

}
