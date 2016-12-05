<?php

/**
 * @package     GovArticle
 * @subpackage  mod_govarticle_log
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (c) 2015, Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 */

defined('_JEXEC') or die;

abstract class ModGovarticleLogHelper {
	
	public static function getItems(&$params){
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		
		// Prepare query
		$query->select(
			'l.*, a.title AS `article_title`,u.name AS `user_name`,hn.version_note,'.
			'hn.save_date AS `version_new_date`,ho.save_date AS `version_old_date`'
		);
		$query->from('#__govarticle_log AS l');
		$query->leftJoin('#__govarticle AS a ON a.id=l.content_id');
		$query->leftJoin('#__users AS u ON u.id=l.created_by');
		$query->leftJoin('#__ucm_history AS hn ON hn.version_id=l.version_new');
		$query->leftJoin('#__ucm_history AS ho ON ho.version_id=l.version_old');
		
		// Filter results
		$query->where('l.inspected=0');
		
		// Order data
		$query->order('l.created DESC, l.content_id DESC');
		
		// Limit data 
		$query->setLimit($params->get('limit',25));
		
		// Run query
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

}
