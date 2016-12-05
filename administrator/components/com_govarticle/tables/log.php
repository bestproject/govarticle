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

class GovArticleTableLog extends JTable {

	
	public function __construct(JDatabaseDriver $db) {
		parent::__construct('#__govarticle_log', array('id'), $db);
	}
	
	public function check() {
		return true;
	}

}
