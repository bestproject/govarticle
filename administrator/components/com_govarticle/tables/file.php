<?php

/**
 * @package     GovArticle.Administrator
 * @subpackage  com_govarticle
 * @author      Artur Stępień (artur.stepien@bestproject.pl)
 * @copyright   (C) 2005 - 2015 Open Source Matters. All rights reserved.
 * @copyright   (C) 2015 - Fundacja PCJ Otwarte Źródła
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html   
 * @link        http://dostepny.joomla.pl
 * 
 * Based on com_content from Joomla!
 */

defined('JPATH_PLATFORM') or die;

class GovArticleTableFile extends JTable {

	
	public function __construct(JDatabaseDriver $db) {
		parent::__construct('#__govarticle_files', array('id','version'), $db);
	}
	
	public function check() {
		if ( !isset($this->id) OR $this->id<1) {
			return false;
		}
			
		return true;
	}
	
	public function store($updateNulls = false) {
		
		// Get file data to save
		$properties = $this->getProperties();
		
		// Sanitize data
		foreach( $properties AS &$property ) {
			$property = $this->_db->q($property);
		}
		
		// Prepare query
		$query = $this->_db->getQuery(true);
		$query->insert($this->_tbl);
		$query->columns(array_keys($properties));
		$query->values(array(implode(',', array_values($properties))));
		
		// Execute query
		$this->_db->setQuery($query);
		
		return $this->_db->execute();
	}
	
//	public function delete($pk = null) {
//		
//		// Prepare delete query
//		$query = $this->_db->getQuery(true);
//		$query->delete($this->_tbl);
//	}

}
